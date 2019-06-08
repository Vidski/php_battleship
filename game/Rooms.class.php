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
                return null;

            case 'create_room':
                if ($user->get_room()) {
                    return null;
                }
                $newRoom = $this->new_room($user);
                $user->set_room($newRoom);
                //TODO: Im Moment kann man nur Battleship spielen
                $user->get_room()->new_game(new Battleship($user, null));
                EventManager::add_event(new Event($user, 'rooms_handler', 'send_message', 'create_room', array('pin' => $newRoom->get_pin())));
                break;

            case 'join_room':
                $room = $this->get_room($messageObj->pin);
                if (!is_null($room)) {
                    //TODO: replace_missing_player ist nur für Battleship.class da, man sollte für neue Spiele ein iInterface bauen
                    if ($room->get_game()->replace_missing_player($user)) {
                        if (!$room->add_player($user)) {
                            EventManager::add_event(new Event($user, 'rooms_handler', 'send_message', 'join_room', array('error' => 1, 'message' => 'You are already in this room.')));
                            break;
                        }
                        $user->set_room($room);
                        EventManager::add_event(new Event($user, 'rooms_handler', 'send_message_room', 'join_room', array('message' => $user->get_username() . ' joined the room.', 'users' => $room->get_players())));
                        break;
                    }
                    EventManager::add_event(new Event($user, 'rooms_handler', 'join_room', array('error' => 1, 'message' => 'Room is full.')));
                    break;
                }
                EventManager::add_event(new Event($user, 'rooms_handler', 'send_message', 'join_room', array('error' => 1, 'message' => 'Room not found.')));
                break;

            case 'leave_room':
                $room = $user->get_room();
                if ($room) {
                    if ($room->leave_room($user)) {
                        EventMananger::add_event(new Event($user, 'rooms_handler', 'send_message_room', 'leave_room', array('message' => $user->get_username() . ' left the room.', 'users' => $room->get_players())));
                        break;
                    }
                }
                break;

            case 'my_room':
                $room = $user->get_room();
                if ($room) {
                    EventMananger::add_event(new Event($user, 'rooms_handler', 'my_room', $user->get_room()->get_info()));
                }
                break;

            case 'send_message_room':
                $room = $user->get_room();
                $message = htmlspecialchars($messageObj->message);
                if ($room) {
                    EventMananger::add_event(new Event($user, 'rooms_handler', 'send_message_room', 'send_message_room', array('message' => $user->get_username() . ': ' . $message, 'users' => $room->get_players())));
                    break;
                }
                break;

            default:
                break;
        }
    }

    public function on_user_disconnect($user)
    {
        if ($room = $user->get_room()) {
            if ($room->leave_room($user)) {
                EventMananger::add_event(new Event($user, 'rooms_handler', 'leave_room', array('message' => $user->get_username() . ' left the room.', 'users' => $room->get_players())));
            }
        }
    }

}
