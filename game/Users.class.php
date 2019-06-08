<?php

class Users implements iHandler
{

    public function action($messageObj, $user = null)
    {
        switch ($messageObj->action) {
            case 'set_username':
                $user->set_username(ucfirst($messageObj->username));
                EventManager::add_event("SET_USERNAME");
                return $this->build_packet('send_message', 'set_username', array('username' => $user->get_username()));
                break;
        }
    }

    public function build_packet($function, $action, $content)
    {
        return array(
            'handler' => 'users_handler',
            'function' => $function,
            'action' => $action,
            'content' => $content,
        );
    }

}
