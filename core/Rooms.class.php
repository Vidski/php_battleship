<?php

require 'Room.class.php';

class Rooms implements iHandler
{

    private $rooms;

    public function __construct()
    {
        $this->rooms = array();
    }

    public function new_room($owner)
    {
        $newRoom = new Room($owner);
        array_push($this->rooms, $newRoom);
        return $newRoom;
    }

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
                break;
        }
    }

    private function handle_create_room($messageObj, $user)
    {
        if ($user->get_room()) {
            return null;
        }
        $newRoom = $this->new_room($user);
        $user->set_room($newRoom);
        //TODO: Im Moment kann man nur Battleship spielen
        $user->get_room()->new_game(new Battleship($user, null));
        EventManager::add_event(new Event($user, 'rooms_handler', 'create_room', array('pin' => $newRoom->get_pin())));
    }

    private function handle_join_room($messageObj, $user)
    {
        $room = $this->get_room($messageObj->pin);
        if (is_null($room)) {
            EventManager::add_event(new Event($user, 'rooms_handler', 'join_room', array('error' => 1, 'message' => 'Room not found.')));
            return;
        }

        //TODO: replace_missing_player ist nur für Battleship.class da, man sollte für neue Spiele ein iInterface bauen
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
        foreach ($rUsers as $rUser) {
            EventManager::add_event(new Event($rUser, 'rooms_handler', 'join_room', array('message' => $username . ' joined the room.')));
            //EventManager::add_event(new Event($rUser, 'rooms_handler', 'receive_message', array('message' => ' ⚔ ' . $room->get_players()[0]->get_username() . ' versus ' . $room->get_players()[1]->get_username() . ' ⚔')));
        }

        if ($room->get_game()->are_we_missing_a_player_questionmark()) {
            $room->get_game()->replace_missing_player($user);
        }
    }

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
            EventManager::add_event(new Event($rUser, 'rooms_handler', 'join_room', array('message' => $username . ' left the room.')));
        }
    }

    private function handle_my_room($messageObj, $user)
    {
        $room = $user->get_room();
        if (is_null($room)) {
            return;
        }
        EventManager::add_event(new Event($user, 'rooms_handler', 'my_room', $user->get_room()->get_info()));
    }

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

    public function on_user_disconnected($user)
    {
        $room = $user->get_room();
        if (!is_null($user->get_room())) {
            if ($room->leave_room($user)) {
                $rUsers = $room->get_players();
                $room->get_game()->someone_left();
                foreach ($rUsers as $rUser) {
                    EventManager::add_event(new Event($rUser, 'rooms_handler', 'receive_message', array('message' => $user->get_username() . ' left the room.')));
                }
            }
        }
    }

}
