<?php
require 'socketfunctions.inc.php';
require dirname(__FILE__) . '\game\User.class.php';

abstract class Server
{
    //CONFIG
    protected $host;
    protected $port;

    //ARRAYS
    protected $server;
    protected $sockets;
    protected $users;

    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;

        $this->server = null;
        $this->sockets = array();
        $this->users = array();

        $this->server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
        socket_set_option($this->server, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($this->server, $this->host, $this->port) or die("Could not bind socket\n");
        socket_listen($this->server) or die("Could not set up socket listener\n");

        $this->sockets['s'] = $this->server;

        $this->on_server_started();
        $this->server_loop();
    }

    abstract protected function action($user, $message);
    abstract protected function connected($user);
    abstract protected function disconnected($user);

    //HAUPT LOOP FÜR DIE ÜBERWACHUNG VON CLIENTS
    protected function server_loop()
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
                        $this->connected($user);
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
                                $this->action($user, $messageObj);
                            }
                        }
                    }
                }
            }
        }
    }

    //SERVER WURDE ERFOLGREICH GESTARTED
    protected function on_server_started()
    {
        printf("Server running on %s:%d \n\n", $this->host, $this->port);
    }

    //EINE NEUER CLIENT WURDE GEFUNDEN
    protected function on_client_connect($socket)
    {
        socket_getpeername($socket, $clientIP);
        printf("%s - Server->on_client_connect()\n", $clientIP);

        $user = new User(uniqid('u'), $socket);
        $this->users[$user->get_id()] = $user;
        $this->sockets[$user->get_id()] = $socket;
        $this->on_client_connecting($user);
    }

    //DER CLIENT IST VEBUNDEN WURDE ABER NOCH NICHT GEHANDSHAKED
    protected function on_client_connecting($user)
    {
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - Server->on_client_connecting()\n", $clientIP);
    }

    //DER CLIENT HAT DIE VERBINDUNG VERLOREN
    protected function on_client_disconnect($socket)
    {
        socket_getpeername($socket, $clientIP);
        printf("%s - Server->on_client_disconnect()\n", $clientIP);
        $dUser = $this->get_user($socket);

        if ($dUser !== null) {
            unset($this->users[$dUser->get_id()]);

            if (array_key_exists($dUser->get_id(), $this->sockets)) {
                unset($this->sockets[$dUser->get_id()]);
            }

            $this->disconnected($dUser);
            socket_close($dUser->get_socket());
        }
    }

    //SEND MESSAGE ZUM CLIENT
    protected function send_message($user, $message)
    {
        $message = $this->encode_and_mask($message);
        socket_write($user->get_socket(), $message, strlen($message));
    }

    //GETTER
    protected function get_user($socket)
    {
        foreach ($this->users as $user) {
            if ($user->get_socket() == $socket) {
                return $user;
            }
        }
        return null;
    }

    //JSON ENCODEN UND MASKIEREN DER PACKETE
    protected function encode_and_mask($message)
    {
        return mask(json_encode($message));
    }
}
