<?php

require 'Server.php';
require dirname(__FILE__) . '/core/iHandler.interface.php';
require dirname(__FILE__) . '/core/EventManager.class.php';
require dirname(__FILE__) . '/core/Rooms.class.php';
require dirname(__FILE__) . '/core/Users.class.php';
require dirname(__FILE__) . '/core/games/iGame.interface.php';
require dirname(__FILE__) . '/core/games/battleship/Battleship.class.php';

/**
 * GameServer.php
 *
 * Verwaltet alles
 *
 * @author David Rydwanski, Stefan Hackstein
 */
class GameServer extends Server
{

    //Handlers
    private $roomsHandler;
    private $usersHandler;

    /**
     * started()
     *
     * Hier werden die Handler und der EventManager erstellt.
     */
    protected function started()
    {
        EventManager::init();
        $this->roomsHandler = new Rooms();
        $this->usersHandler = new Users();
    }

    /**
     * handle_in($user, $messageObj)
     *
     * Wenn der Server ein Packet bekommt, wird die Funktion aufgerufen und das Paket an den passenden Handler weitergeleitet.
     *
     * @param User $user
     * @param Array $messageObj
     */
    protected function handle_in($user, $messageObj)
    {
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - GameServer->action()\n", $clientIP);

        //DEBUG
        //print_r($messageObj);

        switch ($messageObj->handler) {
            case 'rooms_handler':
                $this->roomsHandler->action($messageObj, $user);
                break;

            case 'users_handler':
                $this->usersHandler->action($messageObj, $user);
                break;

            default:
                print("\! Unknown Handler !\n");
                print_r($messageObj);
                break;
        }
    }

    /**
     * handle_out
     *
     * Die Funktion holt sich aus dem EventMananger die Events.
     * Die gefundenen Events werden bearbeitet.
     */
    protected function handle_out()
    {
        $events = EventManager::events();

        $reversed = new Stack();

        while ($events->length() != 0) {
            $reversed->push($events->pop());
        }

        while ($reversed->length() != 0) {
            $this->execute($reversed->pop());
        }
    }

    /**
     * connected($user)
     *
     * Wird aufgerufen wenn sich ein Spieler auf dem Server verbindet.
     *
     * @param User $users
     */
    protected function connected($user)
    {
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - GameServer->connected()\n", $clientIP);
    }

    /**
     * disconnected($user)
     *
     * Diese Funktion wird aufgerufen, wenn ein Spieler die Verbindung trennt.
     *
     * @param User $users
     */
    protected function disconnected($user)
    {
        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - GameServer->disconnected()\n", $clientIP);

        $this->roomsHandler->on_user_disconnected($user);
    }

    /**
     * execute($event)
     *
     * Die Funktion execute schickt das übergebene Event an die Benutzer raus.
     *
     * @param Event $event
     */
    private function execute($event)
    {
        $user = $event->get_user();

        socket_getpeername($user->get_socket(), $clientIP);
        printf("%s - GameServer->execute()\n", $clientIP);

        //DEBUG
        //print_r($event);

        $packet = $event->get_packet();
        $this->send_message($user, $packet);
    }

}
