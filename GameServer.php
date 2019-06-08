<?php

require 'Server.php';
require dirname(__FILE__) . '/game/iHandler.interface.php';
require dirname(__FILE__) . '/game/Rooms.class.php';
require dirname(__FILE__) . '/game/Users.class.php';
require dirname(__FILE__) . '/game/Battleship.class.php';
require dirname(__FILE__) . '/game/EventManager.class.php';

class GameServer extends Server
{

    //Handlers
    private $roomsHandler;
    private $usersHandler;

    protected function started()
    {
        $this->roomsHandler = new Rooms();
        $this->usersHandler = new Users();
        EventManager::init();
    }

    protected function handle_in($user, $messageObj)
    {
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - GameServer->action()\n", $clientIP);
        print_r($messageObj);

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
        $length = $events->length();
        
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
        $packet = $event->get_packet();
        
        switch ($packet['function']) {
            // packet('send_message', 'test', array('message' => 'Hello World'));
            case 'send_message':
                unset($packet['function']);
                $this->send_message($user, $packet);
                return true;

            // packet('send_message_room', 'test', array('message' => $message, 'users' => array($user, $user2, $user3)));
            case 'send_message_room':
                unset($packet['function']);
                $users = $packet['content']['users'];
                unset($packet['content']['users']);
                foreach ($users as $user) {
                    $this->send_message($user, $packet);
                }
                return true;
                
            // packet('send_messages', 'test', array('users' => array($user1, $user2), 'message' => array($user1Msg, $user2Msg)));
            case 'send_messages':
                unset($packet['function']);
                $users = $packet['content']['users'];
                unset($packet['content']['users']);
                $messages = $packet['content']['message'];
                unset($packet['content']['message']);
                
                $i = 0;
                foreach ($users as $user) {
                    $packet['content'] = $messages[$i];
                    $this->send_message($user, $packet);
                    $i++;
                }
                return true;


            default:
                return false;
        }

        return false;
    }

}
