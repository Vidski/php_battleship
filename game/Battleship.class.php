<?php

require 'Ship.class.php';

/**
 * Battleship.class.php beinhaltet die Spiellogik für das Spiel.
 *
 * @author  David Rydwanski, Stefan Hackstein
 */
class Battleship implements iHandler
{

    private $shipLimit = array(2 => 4, 3 => 3, 4 => 2, 5 => 1);
    private $shipSizes = array(
        "ship2V" => array('x' => 1, 'y' => 2),
        "ship3V" => array('x' => 1, 'y' => 3),
        "ship4V" => array('x' => 1, 'y' => 4),
        "ship5V" => array('x' => 1, 'y' => 5),
        "ship2H" => array('x' => 2, 'y' => 1),
        "ship3H" => array('x' => 3, 'y' => 1),
        "ship4H" => array('x' => 4, 'y' => 1),
        "ship5H" => array('x' => 5, 'y' => 1),
    );

    private $gameStarted;
    private $playerTurn;
    private $lastMove; //Wird bei jeder Action aktualisiert ( $lastMove = time() )
    private const DESTROY_TIME = 600; //Falls in <Sekunden> keine Action passiert wird das Spiel gelöscht

    private $playerOne;
    private $playerOneReady;
    private $playerOneField;
    private $playerOneShips;

    private $playerTwo;
    private $playerTwoReady;
    private $playerTwoField;
    private $playerTwoShips;

    /**
     * __construct für Battleship.class
     * @param  User $playerOne Spieler1
     * @param  User $playerTwo Spieler2
     */
    public function __construct($playerOne, $playerTwo)
    {
        $this->gameStarted = false;
        $this->playerOne = $playerOne;
        $this->playerTwo = $playerTwo;
        $this->playerOneReady = false;
        $this->playerTwoReady = false;
        $this->playerOneField = array();
        $this->playerTwoField = array();
        $this->playerOneShips = array();
        $this->playerTwoShips = array();
        $this->playerTurn = $playerOne;
        $this->fill_field();
        $this->lastMove = time();
    }

    /**
     * action von iHandler
     * Hier werden die Packete von dem Client für das Spiel verwaltet
     *  
     * @param  Array $messageObj Das Packet von dem Client
     * @param  User $user Der User (Client)
     */
    public function action($messageObj, $user = null)
    {
        $this->lastMove = time();

        //Falls ein Spieler disconnected Spiel pausieren
        if ($this->gameStarted) {
            if ($this->playerOne->disconnected() || $this->playerTwo->disconnected()) {
                return null;
            }
        }

        switch ($messageObj->content->action) {

            case 'shoot':
                if (!$this->gameStarted) {
                    return;
                }
                $this->handle_shoot($messageObj, $user);
                break;

            case 'place':
                if ($this->gameStarted) {
                    return;
                }
                $this->handle_place($messageObj, $user);
                break;

            default:
                break;
        }
    }

    /**
     * function handle_shoot
     * 
     * In dieser funktion wird das Schießen auf dem Server verwaltet.
     * Dazu wird die Funktion check_hit aufgerufen
     * 
     * @param  Array $messageObj Das Packet von dem Client
     * @param  User $user Der User (Client)
     */
    private function handle_shoot($messageObj, $user)
    {
        if ($this->playerTurn != $user) {
            return;
        }

        /**
         * Setzen der Spieler
         */
        $temp = $this->playerTurn;
        if ($this->playerTurn == $this->playerOne) {
            $other_player = $this->playerTwo;
        } else {
            $other_player = $this->playerOne;
        }

        /**
         * Setzen des Feldes und der Schiffe
         */
        $targetField = null;
        $targetShips = null;
        switch ($this->playerTurn) {

            case $this->playerOne:
                $targetField = &$this->playerTwoField;
                $targetShips = &$this->playerTwoShips;
                break;

            case $this->playerTwo:
                $targetField = &$this->playerOneField;
                $targetShips = &$this->playerOneShips;
                break;

            default:
                return null;
        }

        /**
         * Die Überprüfung des Schusses.
         */
        $x = $messageObj->content->position->x;
        $y = $messageObj->content->position->y;
        $result = $this->check_hit($x, $y, $targetField, $targetShips);

        /**
         * @param $result ist null, wenn auf das angefragte Feld schonmal geschossen wurde.
         */
        if (is_null($result)) {
            return null;
        }

        /**
         * Falls nicht getroffen wurde ist der andere Spieler am Zug. 
         * Wenn getroffen wurde, wird geschaut ob das Schiff versenkt wurde oder nicht.
         */
        $deadShip = null;
        if (!$result) {
            $this->playerTurn = $other_player;
        } else {
            foreach ($targetShips as $ship) {
                if ($ship->is_dead($x, $y)) {
                    $deadShip = $ship;
                    break;
                }
            }
        }

        //Nachrichten an die Spieler
        $pOne = array(
            'x' => $x,
            'y' => $y,
            'field' => 'right',
            'hit' => $result,
            'myturn' => $user == $this->playerTurn,
            'ship' => is_null($deadShip) ? null : array(
                'id' => $deadShip->get_id(),
                'position' => $deadShip->get_position_formatted(),
            ),
        );

        $pTwo = $pOne;
        $pTwo['field'] = 'left';
        $pTwo['myturn'] = $user != $this->playerTurn;

        EventManager::add_event(new Event($temp, 'battleship_handler', 'shoot', $pOne));
        EventManager::add_event(new Event($other_player, 'battleship_handler', 'shoot', $pTwo));
    }

    private function handle_place($messageObj, $user)
    {
        $field = null;
        $ships = null;
        if ($user == $this->playerOne && !$this->playerOneReady) {
            $field = &$this->playerOneField;
            $ships = &$this->playerOneShips;
        } else if ($user == $this->playerTwo && !$this->playerTwoReady) {
            $field = &$this->playerTwoField;
            $ships = &$this->playerTwoShips;
        } else {
            return;
        }

        $posX = $messageObj->content->position->x;
        $posY = $messageObj->content->position->y;
        $ship = $messageObj->content->ship;

        if ($field[$posX . $posY] == 0) {
            for ($y = $posY - 1; $y < $posY + $this->shipSizes[$ship]['y']; $y++) {
                for ($x = $posX - 1; $x < $posX + $this->shipSizes[$ship]['x'] + 1; $x++) {
                    if ($x < 0 || $y < 0 || $x > 9 || $y > 9) {
                        continue;
                    }
                    if ($field[$x . $y] == 1) {
                        EventManager::add_event(new Event($user, 'battleship_handler', 'place', 'Can\'t place here.'));
                        return;
                    }
                }
            }
        } else {
            EventManager::add_event(new Event($user, 'battleship_handler', 'place', 'Can\'t place here.'));
            return;
        }

        //BLOCKIEREN
        $blocked = array();
        for ($y = $posY - 1; $y < $posY + $this->shipSizes[$ship]['y'] + 1; $y++) {
            for ($x = $posX - 1; $x < $posX + $this->shipSizes[$ship]['x'] + 1; $x++) {
                if ($x < 0 || $y < 0 || $x > 9 || $y > 9) {
                    continue;
                }
                $field[$x . $y] = 4;
                array_push($blocked, $x . $y);
            }
        }

        //SCHIFF PLATZIEREN
        $placed = array();
        for ($y = $posY; $y < $posY + $this->shipSizes[$ship]['y']; $y++) {
            for ($x = $posX; $x < $posX + $this->shipSizes[$ship]['x']; $x++) {
                $field[$x . $y] = 1;
                array_push($placed, $x . $y);
            }
        }
        //TODO: LIMIT ÜBERPRÜFEN
        array_push($ships, new Ship($placed, $ship, $this->shipSizes[$ship]));

        EventManager::add_event(new Event($user, 'battleship_handler', 'place', array('placed' => $placed, 'blocked' => $blocked)));

        //CHECK IF EVERYONE IS READY
        if (count($ships) >= 10) {
            if ($user == $this->playerOne) {
                $this->playerOneReady = true;
            } else {
                $this->playerTwoReady = true;
            }

            if ($this->playerOneReady && $this->playerTwoReady) {
                $this->gameStarted = true;
                $room = $user->get_room();
                $rUsers = $room->get_players();
                foreach ($rUsers as $rUser) {
                    EventManager::add_event(new Event($rUser, 'battleship_handler', 'start', array('ready' => true)));
                }
                return;
            }
            EventManager::add_event(new Event($user, 'rooms_handler', 'receive_message', array('message' => 'Waiting for your Enemy to finish')));
        }
    }

    /**
     * check_hit
     * 
     * Diese Funktion schaut nach, ob der Schuss ein Schiff getroffen hat. 
     * 
     * 0 = Kein Schiff
     * 1 = Schiff
     * 2 = Getroffen
     * 3 = verfehlt
     * 4 = blockiert für Schiffe bei dem Platzieren
     * 
     * @param Integer $x x-Position
     * @param Integer $y y-Position
     * @param Array $field Spielfeld
     */
    private function check_hit($x, $y, &$field)
    {
        if ($field[$x . $y] == 0 || $field[$x . $y] == 4) {
            $field[$x . $y] = 3;
            return false;
        } else if ($field[$x . $y] == 1) {
            $field[$x . $y] = 2;
            return true;
        }
        return null;
    }

    //TODO: Falls ein Spieler neu connected müssen wir ihn auf den aktuellsten Stand bringen
    public function replace_missing_player($player)
    {
        if (is_null($this->playerOne)) {
            $this->playerOne = $player;
            return true;
        } else if (is_null($this->playerTwo)) {
            $this->playerTwo = $player;
            return true;
        } else if ($this->playerOne->disconnected()) {
            $this->playerOne = $player;
            return true;
        } else if ($this->playerTwo->disconnected()) {
            $this->playerTwo = $player;
            return true;
        }
        return false;
    }

    /**
     * fill_field
     * 
     * Hier werden die beiden Arrays, die die Felder verwalten, gefüllt. 
     * 
     * Die "0" steht für kein Schiff.
     */
    private function fill_field()
    {
        for ($y = 0; $y < 10; $y++) {
            for ($x = 0; $x < 10; $x++) {
                $this->playerOneField[$x . $y] = 0;
                $this->playerTwoField[$x . $y] = 0;
            }
        }
    }

    public function destroy_time()
    {
        return $this->lastMove + Battleship::DESTROY_TIME;
    }

}
