<?php

class Users implements iHandler
{

    public function action($messageObj, $user = null)
    {
        switch ($messageObj->action) {
            case 'set_username':
                $this->handle_set_username($messageObj, $user);
                break;

            default:
                print("\! Unknown Action !\n");
                print_r($messageObj);
                break;
        }
    }

    private function handle_set_username($messageObj, $user)
    {
        $user->set_username(ucfirst($messageObj->username));
        EventManager::add_event(new Event($user, 'users_handler', 'set_username', array('username' => $user->get_username())));
    }

}
