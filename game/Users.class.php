<?php

class Users implements iHandler
{

    public function action($messageObj, $user = null)
    {
        switch ($messageObj->action) {
            case 'set_username':
                $user->set_username(ucfirst($messageObj->username));
                EventManager::add_event(new Event($user, 'users_handler', 'set_username', array('username' => $user->get_username())));
                break;
        }
    }

}
