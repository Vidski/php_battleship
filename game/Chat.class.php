<?php

class Chat
{
    public function action($msgObj) 
    {
        switch ($msgObj->action) {
            case 'send_message_all':
                return $this->build_packet(true, 'receive_message', $msgObj->message);
                break;
            
            case 'random_roll':
                return $this->build_packet(false, 'receive_message', '3');
                break;

            default:
                return null;
                break;
        }
    }

    private function build_packet($messageAll, $action, $content) 
    {
        if ($messageAll) {
            return array(
                'handler' => 'chat_handler',
                'function' => 'send_message_all',
                'action' => $action,
                'content' => $content
            );
        }

        return array(
            'handler' => 'chat_handler',
            'function' => 'send_message',
            'action' => $action,
            'content' => $content
        );
    }
}
