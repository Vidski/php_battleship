<?php

require 'iHandler.interface.php';
require 'Rooms.class.php';

class Game implements iHandler
{
    private $server;

    //Handlers
    private $roomsHandler;

    private $players;

    public function __construct($server)
    {
        $this->server = $server;
        $this->roomsHandler = new Rooms();
        $this->players = array();
    }

    public function handler_action($msgObj, $socket)
    {
        switch ($msgObj->handler) {
            case 'game_handler':
                return $this->action($msgObj, $socket);
                break;

            case 'rooms_handler':
                return $this->roomsHandler->action($msgObj, $socket);
                break;

            default:
                return null;
                break;
        }
    }

    public function action($messageObj, $user = null)
    {
        switch ($messageObj->action) {
            case 'set_username':
                $user->set_username(trim($messageObj->username));
                return $this->build_packet('send_message', 'registered', $messageObj->username);
                
                return null;

            default:
                return null;
        }
    }

    public function build_packet($function, $action, $content)
    {
        return array(
            'handler' => 'game_handler',
            'function' => $function,
            'action' => $action,
            'content' => $content,
        );
    }

}
