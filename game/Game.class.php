<?php

require 'iHandler.interface.php';
require 'Rooms.class.php';
require 'Player.class.php';

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

    public function action($msgObj, $socket = null)
    {
        switch ($msgObj->action) {
            case 'register':
                if ($this->register_player($socket, $msgObj)) {
                    return $this->build_packet('send_message', 'set_username', $msgObj->username);
                }
                return null;
                break;

            default:
                return null;
                break;
        }
    }

    public function register_player($socket, $msgObj)
    {
        $player = new Player($msgObj->username, $socket);
        array_push(new Player($msgObj->username, $socket));
        return $player;
    }

    public function get_player($socket) 
    {
        foreach ($this->players as $player) {
            if ($player->get_socket() === $socket) {
                return $player;
                break;
            }
        }
        return false;
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
