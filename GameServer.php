<?php

require 'Server.php';

class GameServer extends Server 
{

    protected function action($user, $message) 
    {
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - GameServer->action()\n", $clientIP);
    }

    protected function connected($user) 
    {
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - GameServer->connected()\n", $clientIP);
    }

    protected function disconnected($user)
    {
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - GameServer->disconnected()\n", $clientIP);
    }

}
