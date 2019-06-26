<?php

/**
 * Room.class.php
 * 
 * @author David Rydwanski, Stefan Hackstein
 */
class Room
{

    private $roomSize; //Anzahl Spieler die in dem Raum sein können.
    private $roomOwner; //Eigentümer des Raumes.
    private $roomPlayers; //Arry mit allen Spielern im Raum.
    private $roomPin; //PIN des Raumes um dem Raum beizutreten.

    private $game; //Das Spiel was gerade in diesem Raum gespielt wird.


    private $lastAction; //letzte Action im Raum
    private $isEmpty; //bool ob der Raum leer ist


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

    /**
     * add_player($user)
     * 
     * Hier wird der Spieler dem Raum hinzugefügt. 
     * Falls der Spieler schon in dem Raum ist wird false zurück gegeben. 
     * Ansonsten true
     * 
     * @param User $user
     * @return bool 
     */
    public function add_player($user)
    {
        if (in_array($user, $this->roomPlayers)) {
            return false;
        }
        array_push($this->roomPlayers, $user);
        return true;
    }

    /**
     * leave_room($user)
     * 
     * Hier wird ein Spieler aus dem Raum genommen. 
     * Falls der Spieler nicht mehr in dem Raum ist, wird false zurück gegeben.
     * 
     * @param User $user
     * @return bool 
     */
    public function leave_room($user)
    {
        $index = array_search($user, $this->roomPlayers);
        if ($index < 0) {
            return false;
        }
        unset($this->roomPlayers[$index]);
        $user->set_room(null);
        if (array_search(!null, $this->roomPlayers) > 0) {
            $this->isEmpty = false;
        }

        if (!is_null($this->game)) {
            $this->game->remove_player($user);
        }
        return true;
    }

    /**
     * new_game($game)
     * 
     * hier wird das Spiel zugewiesen, welches in dem Raum gespielt wird.
     * 
     * @param iGame $game
     */
    public function new_game($game)
    {
        $this->game = $game;
    }

    /**
     * get_game()
     * 
     * Getter für $game
     * 
     * @return Game $this->game
     */
    public function get_game()
    {
        return $this->game;
    }

    /**
     * get_owner()
     * 
     * Getter für $roomOwner
     * 
     * @return User $this->roomOwner
     */
    public function get_owner()
    {
        return $this->roomOwner;
    }

    /**
     * get_players()
     * 
     * Getter für den Array mit Spielern
     * 
     * @return Array $this->roomPlayers
     */
    public function get_players()
    {
        return $this->roomPlayers;
    }

    /**
     * get_pin()
     * 
     * Getter für den pin
     * 
     * @return Int $this->roomPin
     */
    public function get_pin()
    {
        return $this->roomPin;
    }

    /**
     * get_info()
     * 
     * Getter für die Infos des Raumes
     * 
     * @return Array -> Array mit der Pin, dem Owner, der Größe und den Spielern
     */
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

    /**
     * is_empty()
     * 
     * Getter für den Boolean isEmpty()
     * 
     * @return bool $this->isEmpty 
     */
    public function is_empty()
    {
        return $this->isEmpty;
    }

    /**
     * random_pin()
     * 
     * Funktion um einen zufälligen Pin für den Raum zu bekommen.
     * 
     * @return Int random zwischen 1000 und 9999 
     */
    private function random_pin()
    {
        return rand(1000, 9999);
    }

}
?>