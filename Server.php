<?php

require 'socketfunctions.inc.php';
require dirname(__FILE__) . '\game\Game.class.php';

class Server
{

    private $host;
    private $port;
    private $clientSockets;
    private $serverSocket;

    private $GAME;

    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->clientSockets = array();
        $this->serverSocket = null;

        $this->GAME = new Game($this);

        $this->server_start();
    }

    //SERVER STARTEN
    private function server_start()
    {
        $this->serverSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");

        socket_set_option($this->serverSocket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($this->serverSocket, $this->host, $this->port) or die("Could not bind socket\n");
        socket_listen($this->serverSocket) or die("Could not set up socket listener\n");

        $this->on_server_started();
        $this->server_loop();
    }

    //HAUPT LOOP FÜR DIE ÜBERWACHUNG VON CLIENTS
    private function server_loop()
    {
        while (1) {
            $socketsToWatch = $this->clientSockets;
            $socketsToWatch[] = $this->serverSocket;
            $anzahlAktiveSockets = socket_select($socketsToWatch, $null, $null, $null);

            if ($anzahlAktiveSockets === false) {
                die;
            }

            if ($anzahlAktiveSockets == 0) {
                continue;
            }

            if (in_array($this->serverSocket, $socketsToWatch)) {
                $neuerSocket = socket_accept($this->serverSocket);
                socket_getpeername($neuerSocket, $clientIP);

                $header = socket_read($neuerSocket, 1024);
                perform_handshaking($header, $neuerSocket, $this->host, $this->port);

                $this->clientSockets[] = $neuerSocket;
                $index = array_search($this->serverSocket, $socketsToWatch);
                unset($socketsToWatch[$index]);

                $this->on_client_connected($neuerSocket);
            }

            foreach ($socketsToWatch as $socket) {
                socket_getpeername($socket, $clientIP);

                $ende = false;
                $text = (socket_read($socket, 1024));
                $msgObj = "";

                if ($text === false || $text == "") {
                    $ende = true;
                } else {
                    $text = unmask($text);
                }

                //End of Text prüfen, wird vom Browser beim Fenster schließen geschickt:
                if (preg_match('/\x03/', $text)) {
                    $ende = true;
                } else if (strlen($text) > 0) {
                    $msgObj = json_decode($text);
                }

                if ($ende || (isset($msgObj->message) && $msgObj->message == "exit")) {
                    $index = array_search($socket, $this->clientSockets);
                    unset($this->clientSockets[$index]);
                    socket_close($socket);

                    $this->on_client_disconnected($clientIP);
                } else {
                    if (isset($msgObj->message)) {
                        $this->on_message_received($socket, $msgObj);
                    }
                }
            }
        }
    }

    //SERVER WURDE ERFOLGREICH GESTARTED
    private function on_server_started()
    {
        printf("Server running on %s:%d \n", $this->host, $this->port);
    }

    //EIN NEUER CLIENT WURDE ERFOLGREICH VERBUNDEN
    private function on_client_connected($socket)
    {
        socket_getpeername($socket, $cip);
        printf("%s connected \n", $cip);
    }

    //EIN CLIENT HAT DIE VERBINDUNG ABGEBROCHEN
    private function on_client_disconnected($cip)
    {
        printf("%s disconnected \n", $cip);
    }

    //SERVER HAT EINE NACHRICHT VON EINEM CLIENT ERHALTEN
    private function on_message_received($socket, $msgObj)
    {
        //GAME HANDLER
        $action = $this->GAME->handler_action($msgObj);

        //print_r($action);
        if ($action === null) {
            return;
        }

        if ($action['function'] === 'send_message') {
            unset($action['function']);
            $this->send_message($socket, $action);
        } else if ($action['function'] === 'send_message_all') {
            unset($action['function']);
            $this->send_message_all($action);
        }
    }

    //SENDE NACHRICHT AN CLIENT
    //$message ist ein Array z.B. array("foo" => "bar", "foo" => "bar");
    private function send_message($socket, $message)
    {
        $message = $this->encode_and_mask($message);
        socket_write($socket, $message, strlen($message));
    }

    //SENDE NACHRICHT AN ALLE CLIENTS
    //$message ist ein Array z.B. array("foo" => "bar", "foo" => "bar");
    private function send_message_all($message)
    {
        $clientSockets = $this->clientSockets;
        $message = $this->encode_and_mask($message);
        foreach ($clientSockets as $socket) {
            socket_write($socket, $message, strlen($message));
        }
    }

    //JSON ENCODEN UND MASKIEREN DER PACKETE
    private function encode_and_mask($message)
    {
        return mask(json_encode($message));
    }

}
