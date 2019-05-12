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
            if ($room->is_empty()) {
                unset($this->rooms->$room);
                continue;
            }
            if ($room->get_pin() === $pin) {
                return $room;
            }
        }
        return null;
    }

    public function action($messageObj, $user = null)
    {
        switch ($messageObj->action) {
            case 'create_room':
                if ($user->get_room())
                    return null;
                $newRoom = $this->new_room($user);
                $user->set_room($newRoom);
                return $this->build_packet('send_message', 'create_room', array('pin' => $newRoom->get_pin()));

            case 'join_room':
                $room = $this->get_room($messageObj->pin);
                if ($room) {
                    if ($room->add_player($user)) {
                        return $this->build_packet('send_message', 'join_room', $room->get_info());
                    }
                }
                return $this->build_packet('send_message', 'join_room', array('message' => 'Room not found.'));

            case 'leave_room':
                $room = $user->get_room();
                if ($room) {
                    if ($room->leave_room($user)) {
                        return $this->build_packet('send_message', 'leave_room', array('message' => 'You left the room.'));
                    }
                }
                return null;

            case 'my_room':
                $room = $user->get_room();
                if ($room) {
                    return $this->build_packet('send_message', 'my_room', $user->get_room()->get_info());
                }
                return null;

            default:
                return null;
        }
    }

    public function build_packet($function, $action, $content)
    {
        return array(
            'handler' => 'rooms_handler',
            'function' => $function,
            'action' => $action,
            'content' => $content,
        );
    }

}
