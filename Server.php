<?php
require 'socketfunctions.inc.php';
require dirname(__FILE__) . '\game\Game.class.php';
require dirname(__FILE__) . '\game\User.class.php';
class Server
{
    //CONFIG
    private $host;
    private $port;

    //ARRAYS
    private $server;
    private $sockets;
    private $users;

    //HANDLER
    private $GAME;

    
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;

        $this->server = null;
        $this->sockets = array();
        $this->users = array();

        //GAME
        $this->GAME = new Game($this);

        $this->server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
        socket_set_option($this->server, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($this->server, $this->host, $this->port) or die("Could not bind socket\n");
        socket_listen($this->server) or die("Could not set up socket listener\n");

        $this->sockets['s'] = $this->server;

        $this->on_server_started();
        $this->server_loop();
    }

    //HAUPT LOOP FÜR DIE ÜBERWACHUNG VON CLIENTS
    private function server_loop()
    {
        while (true) {
            if (empty($this->sockets)) {
                $this->sockets['s'] = $this->server;
            }

            $read = $this->sockets;
            $write = $except = null;
            socket_select($read, $write, $except, null);

            foreach ($read as $socket) {
                if ($socket == $this->server) {
                    $client = socket_accept($socket);
                    if ($client < 0) {
                        continue;
                    } else {
                        $this->on_client_connect($client);
                    }
                } else {
                    $user = $this->get_user($socket);
                    if (!$user->did_handshake()) {
                        $header = socket_read($socket, 1024);
                        perform_handshaking($header, $socket, $this->host, $this->port);
                        $user->handshake();
                        $this->on_client_connected($user);
                    } else {
                        $ende = false;
                        $text = (socket_read($socket, 1024));
                        $messageObj = "";
                        if ($text === false || $text == "") {
                            $ende = true;
                        } else {
                            $text = unmask($text);
                        }
                        if (preg_match('/\x03/', $text)) {
                            $ende = true;
                        } else if (strlen($text) > 0) {
                            $messageObj = json_decode($text);
                        }
                        if ($ende) {
                            $this->on_client_disconnect($user->get_socket());
                        } else {
                            if (isset($messageObj->handler)) {
                                $this->on_message_received($user, $messageObj);
                            }
                        }
                    }
                }
            }
        }
    }

    //SERVER WURDE ERFOLGREICH GESTARTED
    private function on_server_started()
    {
        printf("Server running on %s:%d \n\n", $this->host, $this->port);
    }

    //EINE NEUER CLIENT WURDE GEFUNDEN
    private function on_client_connect($socket)
    {
        socket_getpeername($socket, $clientIP);
        printf("%s - on_client_connect()\n", $clientIP);

        $user = new User(uniqid('u'), $socket);
        $this->users[$user->get_id()] = $user;
        $this->sockets[$user->get_id()] = $socket;
        $this->on_client_connecting($user);
    }

    //DER CLIENT IST VEBUNDEN WURDE ABER NOCH NICHT GEHANDSHAKED
    private function on_client_connecting($user)
    {
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - on_client_connecting()\n", $clientIP);
    }

    //DER CLIENT WURDE ERFOLGREICH VERBUNDEN
    private function on_client_connected($user)
    {
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - on_client_connected()\n", $clientIP);
    }

    //DER CLIENT HAT DIE VERBINDUNG VERLOREN
    private function on_client_disconnect($socket)
    {
        socket_getpeername($socket, $clientIP);
        printf("%s - on_client_disconnect()\n", $clientIP);
        $dUser = $this->get_user($socket);

        if ($dUser !== null) {
            unset($this->users[$dUser->get_id()]);

            if (array_key_exists($dUser->get_id(), $this->sockets)) {
                unset($this->sockets[$dUser->get_id()]);
            }

            $this->on_client_disconnected($dUser);
            socket_close($dUser->get_socket());
        }
    }

    //DIE VERBINDUNG ZUM CLIENT WURDE BEENDET
    private function on_client_disconnected($dUser)
    {
        socket_getpeername($dUser->get_socket(), $clientIP);
        printf("%s - on_client_disconnected()\n", $clientIP);
    }

    //SERVER HAT EINE NACHRICHT VON EINEM CLIENT ERHALTEN
    private function on_message_received($socket, $msgObj)
    {
        print_r($msgObj);
        //GAME HANDLER
        $action = $this->GAME->handler_action($msgObj, $socket);
        print_r($action);
        if ($action === null) {
            return;
        }
        if ($action['function'] === 'send_message') {
            unset($action['function']);
            $this->send_message($socket, $action);
        } else if ($action['function'] === 'send_message_all') {
            unset($action['function']);
            $this->send_message_all($action);
        } else if ($action['function'] === 'send_message_group') {
            unset($action['function']);
            $this->send_message_group($action['group'], $action);
        }
    }

    //GETTER
    private function get_user($socket)
    {
        foreach ($this->users as $user) {
            if ($user->get_socket() == $socket) {
                return $user;
            }
        }
        return null;
    }

    //SENDE VON MESSAGES
    private function send_message($user, $message)
    {
        $message = $this->encode_and_mask($message);
        socket_write($user->get_socket(), $message, strlen($message));
    }
  
    //SENDE NACHRICHT AN ARRAY VON CLIENTS
    private function send_message_group($users, $message)
    {
        $message = $this->encode_and_mask($message);
        foreach ($users as $user) {
            socket_write($user->get_socket(), $message, strlen($message));
        }
    }

    //SENDE NACHRICHT AN ALLE CLIENTS
    private function send_message_all($message)
    {
        $message = $this->encode_and_mask($message);
        foreach ($this->sockets as $socket) {
            socket_write($socket, $message, strlen($message));
        }
    }

    //JSON ENCODEN UND MASKIEREN DER PACKETE
    private function encode_and_mask($message)
    {
        return mask(json_encode($message));
    }
}
