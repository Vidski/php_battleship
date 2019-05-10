<?php

require 'iHandler.interface.php';
require 'Chat.class.php';
require 'Rooms.class.php';

class Game implements iHandler
{
    private $server;

    //Handlers
    private $chatHandler;
    private $roomsHandler;

    public function __construct($server)
    {
        $this->server = $server;
        $this->chatHandler = new Chat();
        $this->roomsHandler = new Rooms();
    }

    public function handler_action($msgObj, $socket)
    {
        switch ($msgObj->handler) {
            case 'chat_handler':
                return $this->chatHandler->action($msgObj, $socket);
                break;

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

    public function action($msgObj, $socket = null)
    {
        switch ($msgObj->action) {
            case 'set_username':
                return $this->build_packet('send_message', 'set_username', $msgObj->message);
                break;

            default:
                return null;
                break;
        }
    }

    public function build_packet($function, $action, $content)
    {
        return array(
            'handler' => 'game_handler',
            'function' => $function,
            'action' => $action,
            'content' => $content
        );
    }

}
