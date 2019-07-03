<?php

/**
 * Users.class.php Ist ein Handler für User Events
 *
 * @author  David Rydwanski, Stefan Hackstein
 */
class Users implements iHandler
{

    /**
     * action von iHandler
     * 
     * Hier werden die Pakete von dem Client verarbeitet
     *
     * @param  Array $messageObj Das Packet von dem Client
     * @param  User $user Der User (Client)
     */
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

    /**
     * handle_set_username
     * 
     * Hier wird der Name des Spielers geändert
     *
     * @param  Array $messageObj Das Packet von dem Client
     * @param  User $user Der User (Client)
     */
    private function handle_set_username($messageObj, $user)
    {
        $user->set_username(ucfirst($messageObj->username));
        EventManager::add_event(new Event($user, 'users_handler', 'set_username', array('username' => $user->get_username())));
    }

}
