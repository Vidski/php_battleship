<?php

class Chat implements iHandler
{
    public function action($msgObj, $socket = null)
    {
        switch ($msgObj->action) {
            case 'send_message_all':
                return $this->build_packet('send_message_all', 'receive_message', $msgObj->message);
                break;

            case 'send_message_room':
                return $this->build_packet('send_message_group', 'receive_message', $msgObj->message);

            case 'random_roll':
                return $this->build_packet('send_message', 'receive_message', rand(0, 6));
                break;

            default:
                return null;
                break;
        }
    }

    public function build_packet($function, $action, $content)
    {
        return array(
            'handler' => 'chat_handler',
            'function' => $function,
            'action' => $action,
            'content' => htmlspecialchars($content),
        );
    }
}
