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
        $newRoom = new Room();
        $newRoom->set_room_owner($owner);
        $newRoom->add_player($owner);
        array_push($this->rooms, $newRoom);
        return $newRoom->get_room_pin();
    }

    public function get_room($pin)
    {
        foreach ($this->rooms as $room) {
            if ($room->get_room_pin() == $pin) {
                return $room;
                break;
            }
        }
        return false;
    }

    public function action($msgObj, $socket = null)
    {
        switch ($msgObj->action) {
            case 'create_room':
                return $this->build_packet('send_message', 'requested_room', $this->new_room($socket));
                break;

            case 'join_room':
                if (isset($msgObj->pin)) {
                    $room = $this->get_room($msgObj->pin);
                    if ($room) {
                        if ($room->add_player($socket)) {
                            return $this->build_packet('send_message', 'join_room', true);
                        }
                    }
                }
                return $this->build_packet('send_message', 'join_room', false);
                break;

            case 'room_info':
                return $this->build_packet('send_message', 'room_info', $this->get_room($msgObj->pin)->get_room_info());
                break;

            default:
                return null;
                break;
        }
    }

    public function build_packet($function, $action, $content)
    {
        return array(
            'handler' => 'rooms_handler',
            'function' => 'send_message',
            'action' => $action,
            'content' => $content,
        );
    }

}
