<?php

class Room
{

    private $roomSize;
    private $roomOwner;
    private $roomPlayers;
    private $roomPin;

    private $game;

    //TODO:
    //FALLS RAUM INAKTIV NACH 5MIN LÖSCHEN
    private $lastAction;
    private $isEmpty;

    public function __construct($roomOwner)
    {
        $this->roomSize = 2;
        $this->roomOwner = $roomOwner;
        $this->roomPlayers = array();
        $this->add_player($this->roomOwner);
        $this->roomPin = $this->random_pin();
        $this->isEmpty = false;
        $this->game = null;
    }

    public function add_player($user)
    {
        if (in_array($user, $this->roomPlayers)) {
            return false;
        }
        array_push($this->roomPlayers, $user);
        return true;
    }

    public function leave_room($user)
    {
        $index = array_search($user, $this->roomPlayers);
        if (!$index) {
            return false;
        }
        unset($this->roomPlayers[$index]);
        $user->set_room(null);
        if (count($this->roomPlayers === 0 || $user === $this->roomOwner)) {
            $this->isEmpty = true;
        }
        return true;
    }

    public function new_game($game)
    {
        $this->game = $game;
    }

    public function get_game()
    {
        return $this->game;
    }

    public function get_owner()
    {
        return $this->roomOwner;
    }

    public function get_players()
    {
        return $this->roomPlayers;
    }

    public function get_pin()
    {
        return $this->roomPin;
    }

    public function get_info()
    {
        $players = array();

        foreach ($this->roomPlayers as $player) {
            array_push($players, $player->get_username());
        }

        return array(
            'pin' => $this->roomPin,
            'owner' => $this->roomOwner->get_username(),
            'size' => count($this->roomPlayers),
            'players' => $players,
        );
    }

    public function is_empty()
    {
        return $this->isEmpty;
    }

    private function random_pin()
    {
        return rand(1000, 9999);
    }

}
