<?php

class Room
{

    private $roomSize;
    private $roomOwner;
    private $roomPlayers;
    private $roomPin;

    //TODO:
    //FALLS RAUM INAKTIV NACH 5MIN LÖSCHEN
    private $lastAction;

    public function __construct()
    {
        $this->roomSize = 2;
        $this->roomPlayers = array();
        $this->roomPin = $this->random_pin();
    }

    public function add_player($socket)
    {
        if(in_array($socket, $this->roomPlayers)) {
            return false;
        }
        array_push($this->roomPlayers, $socket);
    }

    public function set_room_owner($socket)
    {
        $this->roomOwner = $socket;
    }

    public function get_room_info()
    {
        socket_getpeername($this->roomOwner, $clientIP);
        return array(
            'pin' => $this->roomPin,
            'owner' => $clientIP,
            'size' => $this->roomSize,
            'players' => count($this->roomPlayers),
        );
    }

    public function get_room_owner()
    {
        return $this->roomOwner;
    }

    public function get_room_players()
    {
        return $this->roomPlayers;
    }

    public function get_room_pin()
    {
        return $this->roomPin;
    }

    private function random_pin()
    {
        return rand(1000, 9999);
    }
}
