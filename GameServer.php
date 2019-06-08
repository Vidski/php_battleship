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

        $this->packetsIn = array();
        $this->packetsOut = array();
        EventManager::init();
    }

    protected function handle_in($user, $messageObj)
    {
        array_push($this->packetsIn, $messageObj);
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - GameServer->action()\n", $clientIP);
        print_r($messageObj);
        
        $action = null;
        switch ($messageObj->handler) {
            case 'rooms_handler':
                $action = $this->roomsHandler->action($messageObj, $user);
                break;

            case 'users_handler':
                $action = $this->usersHandler->action($messageObj, $user);
                break;

            default:
                return;
                break;
        }

        if (!$action) {
            return;
        }

        switch ($action['function']) {
            // $this->build_packet('send_message', 'test', array('message' => 'Hello World'));
            case 'send_message':
                unset($action['function']);
                $this->send_message($user, $action);
                break;

            // $this->build_packet('send_message_room', 'test', array('message' => $message, 'users' => array($user, $user2, $user3)));
            case 'send_message_room':
                unset($action['function']);
                $users = $action['content']['users'];
                unset($action['content']['users']);
                foreach ($users as $user) {
                    $this->send_message($user, $action);
                }
                break;

            // $this->build_packet('send_messages', 'test', array('users' => array($user1, $user2), 'message' => array($user1Msg, $user2Msg)));
            case 'send_messages':
                unset($action['function']);
                $users = $action['content']['users'];
                unset($action['content']['users']);
                $messages = $action['content']['message'];
                unset($action['content']['message']);
                
                $i = 0;
                foreach ($users as $user) {
                    $action['content'] = $messages[$i];
                    $this->send_message($user, $action);
                    $i++;
                }
                break;

            default:
                break;
        }

        //LOGGING
        print_r($action);
    }

    protected function handle_out() {
        $events = EventManager::events();
        $length =EventManager::events()->length();
        for ($i=0; $i < $length; $i++) { 
            //echo(EventManager::events()->pop());
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

}
