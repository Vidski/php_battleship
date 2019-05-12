<?php

require 'Server.php';
require dirname(__FILE__) . '\game\iHandler.interface.php';
require dirname(__FILE__) . '\game\Rooms.class.php';
require dirname(__FILE__) . '\game\Users.class.php';

class GameServer extends Server
{

    //Handlers
    private $roomsHandler;
    private $usersHandler;

    protected function started()
    {
        $this->roomsHandler = new Rooms();
        $this->usersHandler = new Users();
    }

    protected function action($user, $messageObj)
    {
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - GameServer->action()\n", $clientIP);

        $action = null;
        switch ($messageObj->handler) {
            case 'rooms_handler':
                $action = $this->roomsHandler->action($messageObj, $user);
                break;

            case 'users_handler':
                $action = $this->usersHandler->action($messageObj, $user);
                break;

            default:
                break;
        }

        switch ($action['function']) {
            case 'send_message':
                unset($action['function']);
                $this->send_message($user, $action);
                break;

            default:
                # code...
                break;
        }
        print_r($action);
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
