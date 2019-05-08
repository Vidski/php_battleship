<?php

require 'Chat.class.php';

class Game implements iHandler
{
    private $server;

    //Handlers
    private $chatHandler;

    public function __construct($server)
    {
        $this->server = $server;
        $this->chatHandler = new Chat();
    }

    public function handler_action($msgObj)
    {
        switch ($msgObj->handler) {
            case 'chat_handler':
                return $this->chatHandler->action($msgObj);
                break;

            case 'game_handler':
                return $this->action($msgObj);
                break;

            default:
                return null;
                break;
        }
    }

    public function action($msgObj)
    {
        switch ($msgObj->action) {
            case 'set_username':
                return $this->build_packet(false, 'set_username', $msgObj->message);
                break;

            default:
                return null;
                break;
        }
    }

    public function build_packet($messageAll, $action, $content)
    {
        return array(
            'handler' => 'game_handler',
            'function' => $messageAll ? 'send_message_all' : 'send_message',
            'action' => $action,
            'content' => $content,
        );
    }

}
