<?php

require 'Room.class.php';

/**
 * Rooms.class.php
 * 
 * Die Klasse Rooms verwaltet alle Räume, die aktuell auf dem Server laufen.
 * 
 * @author David Rydwanski, Stefan Hackstein
 */
class Rooms implements iHandler
{

    private $rooms; //Array mit allen Räumen


    public function __construct()
    {
        $this->rooms = array();
    }

    /**
     * new_room($owner)
     * 
     * Hier wird ein neuer Raum erstellt. 
     * 
     * @param User $owner
     * @return Room $newRoom
     */
    public function new_room($owner)
    {
        $newRoom = new Room($owner);
        array_push($this->rooms, $newRoom);
        return $newRoom;
    }

    /**
     * get_room($pin)
     * 
     * Hier wird der Raum mit der übergebenen Pin gesucht.
     * Falls ein leerer Raum gefunden wurde oder eine bestimmte ZEit inaktiv ist wird der Raum gelöscht.
     * 
     * @param INT $pin
     * @return Room $room || null
     */
    public function get_room($pin)
    {
        foreach ($this->rooms as $room) {
            if ($room->is_empty() || $room->get_game()->destroy_time() < time()) {
                unset($this->rooms->$room);
                continue;
            }

            if ($room->get_pin() == $pin) {
                return $room;
            }
        }
        return null;
    }

    /**
     * action($messageObj, $user = null)
     * 
     * Hier werden die Pakete von dem Client verarbeitet.
     * 
     * @param Array $messageObj
     * @param User $user
     */
    public function action($messageObj, $user = null)
    {
        switch ($messageObj->action) {

            case 'game_action':
                $game = $user->get_room()->get_game();
                if ($game) {
                    return $game->action($messageObj, $user);
                }
                break;

            case 'create_room':
                $this->handle_create_room($messageObj, $user);
                break;

            case 'join_room':
                $this->handle_join_room($messageObj, $user);
                break;

            case 'leave_room':
                $this->handle_leave_room($messageObj, $user);
                break;

            case 'my_room':
                $this->handle_my_room($messageObj, $user);
                break;

            case 'send_message':
                $this->handle_send_message($messageObj, $user);
                break;

            default:
                print("\! Unknown Action !\n");
                print_r($messageObj);
                break;
        }
    }

    /**
     * handle_create_room($messageObj, $user)
     * 
     * Hier wird wird das Paket vom Client für die erstellung eines neuen Raumes bearbeitet.
     * Dazu wird new_room() aufgerufen, dem User wird der Raum zugewiesen und in dem Raum wird ein neues Battleship spiel gestartet.
     * Wenn alles Funktioniert hat wird dem EventManager ein Event zur bestätigung hinzugefügt.
     * 
     * @param Array $messageObj
     * @param User $user
     * @return null falls kein Raum vorhanden ist.
     */
    private function handle_create_room($messageObj, $user)
    {
        if ($user->get_room()) {
            return null;
        }

        switch ($messageObj->game) {
            case 'Battleship':
                $newRoom = $this->new_room($user);
                $user->set_room($newRoom);
                $user->get_room()->new_game(new Battleship($user, null));
                break;

            default:
                return;
        }

        EventManager::add_event(new Event($user, 'rooms_handler', 'create_room', array('pin' => $newRoom->get_pin())));
    }

    /**
     * handle_join_room($messageObj, $user)
     * 
     * Hier wird wird das Paket vom Client für das beitreten eines Raumes bearbeitet.
     * Dazu wird geprüft ob der eingegeben Raum vorhanden ist, falls nicht kommt eine Fehlermeldung.
     * Zusätzlich wird geprüft ob in dem Raum noch ein Spieler fehlt, falls nicht kommt eine Fehlermelung.
     * Falls man schon in dem Raum ist, wird eine Fehlermeldung angezeigt.
     * 
     * Falls alles okay ist wird dem Spieler der Raum zugewiesen und allen Spielern eine Nachricht über den EventManager geschickt.
     * 
     * @param Array $messageObj
     * @param User $user
     */
    private function handle_join_room($messageObj, $user)
    {
        $room = $this->get_room($messageObj->pin);
        if (is_null($room)) {
            EventManager::add_event(new Event($user, 'rooms_handler', 'join_room', array('error' => 1, 'message' => 'Room not found.')));
            return;
        }

        if (!$room->get_game()->missing_player()) {
            EventManager::add_event(new Event($user, 'rooms_handler', 'join_room', array('error' => 1, 'message' => 'Room is full.')));
            return;
        }

        if (!$room->add_player($user)) {
            EventManager::add_event(new Event($user, 'rooms_handler', 'join_room', array('error' => 1, 'message' => 'You are already in this room.')));
            return;
        }

        $user->set_room($room);
        $username = $user->get_username();
        $rUsers = $room->get_players();
        EventManager::add_event(new Event($user, 'rooms_handler', 'join_room', array('joined' => true, 'pin' => $messageObj->pin)));
        foreach ($rUsers as $rUser) {
            EventManager::add_event(new Event($rUser, 'rooms_handler', 'receive_message', array('message' => $username . ' joined the room.')));
        }

        $room->get_game()->add_player($user);
    }

    /**
     * handle_leave_room($messageObj, $user)
     * 
     * Hier wird wird das Paket vom Client für das verlassen eines Raumes bearbeitet.
     * 
     * Den Spielern wird eine Message über den EventHandler geschickt.
     * 
     * @param Array $messageObj
     * @param User $user
     */
    private function handle_leave_room($messageObj, $user)
    {
        $room = $user->get_room();
        if (is_null($room)) {
            return;
        }

        if (!$room->leave_room($user)) {
            return;
        }

        $username = $user->get_username();
        $rUsers = $room->get_players();
        foreach ($rUsers as $rUser) {
            EventManager::add_event(new Event($rUser, 'rooms_handler', 'receive_message', array('message' => $username . ' left the room.')));
        }
    }

    /**
     * handle_my_room($messageObj, $user)
     * 
     * Falls der Raum vorhanden ist wird ein Event mit den Infos des Raumes an die den Spieler geschickt.
     * 
     * @param Array $messageObj
     * @param User $user
     */
    private function handle_my_room($messageObj, $user)
    {
        $room = $user->get_room();
        if (is_null($room)) {
            return;
        }
        EventManager::add_event(new Event($user, 'rooms_handler', 'my_room', $user->get_room()->get_info()));
    }

    /**
     * handle_send_message($messageObj, $user)
     * 
     * Hier wird das Paket eines Clients verarbeitet, wenn eine Nachricht in den Chat geschrieben wurde.
     * Der EventManager schickt die Nachrict an alle Spieler im Raum.
     * 
     * @param Array $messageObj
     * @param User $user
     */
    private function handle_send_message($messageObj, $user)
    {
        $room = $user->get_room();
        if (is_null($room)) {
            return;
        }

        $message = htmlspecialchars($messageObj->message);
        $username = $user->get_username();
        $rUsers = $room->get_players();
        foreach ($rUsers as $rUser) {
            EventManager::add_event(new Event($rUser, 'rooms_handler', 'receive_message', array('message' => $username . ': ' . $message)));
        }
    }

    /**
     * on_user_disconnected($user)
     * 
     * Diese Funktion wird aufgerufen, falls ein Spieler die Verbindung verliert.
     * 
     * @param User $user
     */
    public function on_user_disconnected($user)
    {
        $room = $user->get_room();
        if (!is_null($user->get_room())) {
            if ($room->leave_room($user)) {
                $rUsers = $room->get_players();
                $room->get_game()->remove_player($user);
                foreach ($rUsers as $rUser) {
                    EventManager::add_event(new Event($rUser, 'rooms_handler', 'receive_message', array('message' => $user->get_username() . ' left the room.')));
                }
            }
        }
    }

}
?>