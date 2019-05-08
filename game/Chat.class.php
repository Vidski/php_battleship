<?php

require 'iHandler.interface.php';

class Chat implements iHandler
{
    public function action($msgObj)
    {
        switch ($msgObj->action) {
            case 'send_message_all':
                return $this->build_packet(true, 'receive_message', $msgObj->message);
                break;

            case 'random_roll':
                return $this->build_packet(false, 'receive_message', rand(0, 6));
                break;

            default:
                return null;
                break;
        }
    }

    public function build_packet($messageAll, $action, $content)
    {
        return array(
            'handler' => 'chat_handler',
            'function' => $messageAll ? 'send_message_all' : 'send_message',
            'action' => $action,
            'content' => htmlspecialchars($content),
        );
    }
}
