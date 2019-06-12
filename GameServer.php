<?php

require 'Server.php';
require dirname(__FILE__) . '/core/iHandler.interface.php';
require dirname(__FILE__) . '/core/EventManager.class.php';
require dirname(__FILE__) . '/core/Rooms.class.php';
require dirname(__FILE__) . '/core/Users.class.php';
require dirname(__FILE__) . '/core/games/battleship/Battleship.class.php';

class GameServer extends Server
{

    //Handlers
    private $roomsHandler;
    private $usersHandler;

    protected function started()
    {
        EventManager::init();
        $this->roomsHandler = new Rooms();
        $this->usersHandler = new Users();
    }

    protected function handle_in($user, $messageObj)
    {
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - GameServer->action()\n", $clientIP);

        //DEBUG
        //print_r($messageObj);

        switch ($messageObj->handler) {
            case 'rooms_handler':
                $this->roomsHandler->action($messageObj, $user);
                break;

            case 'users_handler':
                $this->usersHandler->action($messageObj, $user);
                break;

            default:
                break;
        }
    }

    protected function handle_out()
    {
        $events = EventManager::events();

        $reversed = new Stack();

        while ($events->length() != 0) {
            $reversed->push($events->pop());
        }

        while ($reversed->length() != 0) {
            $this->execute($reversed->pop());
        }
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

    private function execute($event)
    {
        $user = $event->get_user();

        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - GameServer->execute()\n", $clientIP);

        //DEBUG
        //print_r($event);

        $packet = $event->get_packet();
        $this->send_message($user, $packet);
    }

}
