<?php

require 'Server.php';
require dirname(__FILE__) . '/game/iHandler.interface.php';
require dirname(__FILE__) . '/game/Rooms.class.php';
require dirname(__FILE__) . '/game/Users.class.php';
require dirname(__FILE__) . '/game/Battleship.class.php';

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
                return;
                break;
        }

        if (!$action)
            return;
            
        switch ($action['function']) {
            case 'send_message':
                unset($action['function']);
                $this->send_message($user, $action);
                break;

			case 'send_message_room':
                unset($action['function']);
				$users = $action['content']['users'];
				unset($action['content']['users']);
				foreach ($users as $user) {
					$this->send_message($user, $action);
				}
                break;
            
                //TODO user 2 fix
            case 'send_message_for_shoot':
                unset($action['function']);

                $user1 = $action['content'][0]['user'];
                $user2 = $action['content'][1]['user'];

                unset($action['content'][0]['user']);
                unset($action['content'][1]['user']);

                // Action wird als neuer Array für die beiden Spieler angelegt um im Client nicht [content][0] und [content][1] zu haben. 
                // Somit gibt es für beide Nachrichten beim Client nur noch [content]
                $action1 = array(
                    'handler' => 'battleship_handler',
                    'action' => $action['action'],
                    'content' => $action['content'][0],
                );

                $action2 = array(
                    'handler' => 'battleship_handler',
                    'action' => $action['action'],
                    'content' => $action['content'][1],
                );

                $this->send_message($user1, $action1);
                $this->send_message($user2, $action2);
                break;
            default:
                
                break;
        }
        //LOGGING
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
