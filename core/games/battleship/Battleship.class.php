<?php

require 'Ship.class.php';

/**
 * Battleship.class.php beinhaltet die Spiellogik für das Spiel.
 *
 * @author  David Rydwanski, Stefan Hackstein
 */
class Battleship implements iGame, iHandler
{

    private $shipLimit = array("ship2" => 4, "ship3" => 3, "ship4" => 2, "ship5" => 1); //Das Limit wie oft man ein Typ von Schiff platzieren darf
    private const SHIP_LIMIT = 1; //Anzahl an Schiffen die man platizern darf

    private $shipSizes = array(
        "ship2V" => array('x' => 1, 'y' => 2),
        "ship3V" => array('x' => 1, 'y' => 3),
        "ship4V" => array('x' => 1, 'y' => 4),
        "ship5V" => array('x' => 1, 'y' => 5),
        "ship2H" => array('x' => 2, 'y' => 1),
        "ship3H" => array('x' => 3, 'y' => 1),
        "ship4H" => array('x' => 4, 'y' => 1),
        "ship5H" => array('x' => 5, 'y' => 1),
    ); //Die größen aller Schiffe

    private $gameStarted; //Hat das Spiel bereits begonnen?
    private $playerTurn; //Welcher Spieler grad am Zug ist
    private $lastMove; //Wird bei jeder Action aktualisiert ( $lastMove = time() )
    private const DESTROY_TIME = 600; //Falls in <Sekunden> keine Action passiert wird das Spiel gelöscht

    private $playerOne; //Spieler Eins
    private $playerOneReady; //Spieler Eins ist bereit (?)
    private $playerOneField; //Das Spielefeld von Spieler Eins
    private $playerOneShips; //Die Schiffe von Spieler Eins

    private $playerTwo; //Spieler Zwei
    private $playerTwoReady; //Spieler Zweis ist bereit (?)
    private $playerTwoField; //Das Spielefeld von Spieler Zwei
    private $playerTwoShips; //Die Schiffe von Spieler Zwei

    /**
     * __construct für Battleship.class
     *
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
     * 
     * Hier werden die Pakete von dem Client verarbeitet
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
                EventManager::add_event(new Event($user, 'rooms_handler', 'receive_message', array('message' => "Waiting for someone to reconnect!")));
                return;
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

            case 'remove':
                if ($this->gameStarted) {
                    return;
                }
                $this->handle_remove($messageObj, $user);
                break;

            default:
                print("\! Unknown Action !\n");
                print_r($messageObj);
                break;
        }
    }

    /**
     * handle_shoot
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
         * Setzen des Feldes und der Schiffe
         */
        $playerField = null;
        $targetField = null;
        $targetShips = null;
        $targetUser = null;
        switch ($this->playerTurn) {

            case $this->playerOne:
                $playerField = &$this->playerOneField;
                $targetField = &$this->playerTwoField;
                $targetShips = &$this->playerTwoShips;
                $targetUser = &$this->playerTwo;
                break;

            case $this->playerTwo:
                $playerField = &$this->playerTwoField;
                $targetField = &$this->playerOneField;
                $targetShips = &$this->playerOneShips;
                $targetUser = &$this->playerOne;
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
            $this->playerTurn = $targetUser;
        } else {
            foreach ($targetShips as $ship) {
                if ($ship->is_dead($x, $y)) {
                    $deadShip = $ship;
                    $deadShipPositions = $ship->get_position();
                    foreach ($deadShipPositions as $value) {
                        $targetField[$value] = 5;
                    }
                    break;
                }
            }
        }

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

        EventManager::add_event(new Event($user, 'battleship_handler', 'shoot', $pOne));
        EventManager::add_event(new Event($targetUser, 'battleship_handler', 'shoot', $pTwo));

        foreach ($targetShips as $ship) {
            if ($ship->is_alive()) {
                return;
            }
        }
        //Spiel ist vorbei, ein Gewinner wurde gefunden!
        EventManager::add_event(new Event($user, 'rooms_handler', 'receive_message', array('message' => $user->get_username() . " won!")));
        EventManager::add_event(new Event($targetUser, 'rooms_handler', 'receive_message', array('message' => $user->get_username() . " won!")));
        EventManager::add_event(new Event($user, 'battleship_handler', 'winner', array('title' => "🏆 Winner Winner, Chicken Dinner!", 'body' => 'You won, good job!')));
        EventManager::add_event(new Event($targetUser, 'battleship_handler', 'winner', array('title' => $user->get_username() . " won!", 'body' => 'Maybe next time, loser 🤣')));
    }

    /**
     * handle_place
     *
     * In dieser funktion wird das platzieren von Schiffen verwaltet.
     *
     * @param  Array $messageObj Das Packet von dem Client
     * @param  User $user Der User (Client)
     */
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

        //Überprüfen ob der Spieler Schiff Platizeren darf (das Limit noch nicht erreicht hat)
        $counter = 0;
        $subShip = substr($ship, 0, 5);
        foreach ($ships as $sid) {
            if (substr($sid->get_id(), 0, 5) == $subShip) {
                $counter++;
            }
        }
        if ($counter >= $this->shipLimit[$subShip]) {
            EventManager::add_event(new Event($user, 'battleship_handler', 'limit', array('ship' => $subShip)));
            return;
        }

        //Validierung der Platzierung
        if ($field[$posX . $posY] == 0) {
            for ($y = $posY - 1; $y < $posY + $this->shipSizes[$ship]['y'] + 1; $y++) {
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
            EventManager::add_event(new Event($user, 'rooms_handler', 'receive_message', array('message' => 'Can\'t place here.')));
            return;
        }

        //Blockieren
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

        //Schiff platzieren
        $placed = array();
        for ($y = $posY; $y < $posY + $this->shipSizes[$ship]['y']; $y++) {
            for ($x = $posX; $x < $posX + $this->shipSizes[$ship]['x']; $x++) {
                $field[$x . $y] = 1;
                array_push($placed, $x . $y);
            }
        }

        array_push($ships, new Ship($placed, $ship, $this->shipSizes[$ship]));
        EventManager::add_event(new Event($user, 'battleship_handler', 'place', array('placed' => $placed, 'blocked' => $blocked)));

        //Überprüfe ob alle Spieler bereit sind
        if (count($ships) >= Battleship::SHIP_LIMIT) {
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
            EventManager::add_event(new Event($user, 'rooms_handler', 'receive_message', array('message' => 'Waiting for your enemy')));
            EventManager::add_event(new Event($user, 'battleship_handler', 'ready', array('ready' => true)));
        }

        //Falls das Limit erreicht wurde, UI Element verstecken
        if ($counter + 1 >= $this->shipLimit[$subShip]) {
            EventManager::add_event(new Event($user, 'battleship_handler', 'limit', array('ship' => $subShip)));
        }
    }

    /**
     * handle_remove
     *
     * Diese Funktion wird aufgerufen wenn ein Spieler ein Schiff welches er platziert hat,
     * entfernen bzw. erneut platzieren möchte.
     *
     * @param Array $messageObj
     * @param User $user
     */
    private function handle_remove($messageObj, $user)
    {
        $ships = null;
        $field = null;
        if ($user == $this->playerOne) {
            $ships = &$this->playerOneShips;
            $field = &$this->playerOneField;
        } else if ($user == $this->playerTwo) {
            $ships = &$this->playerTwoShips;
            $field = &$this->playerTwoField;
        } else {
            return;
        }

        $posX = $messageObj->content->position->x;
        $posY = $messageObj->content->position->y;

        $selectedShip = null;
        foreach ($ships as $key => $ship) {
            if ($ship->check_if_me($posX, $posY)) {
                $selectedShip = $ship;
                unset($ships[$key]);
                break;
            }
        }

        if (is_null($selectedShip)) {
            return;
        }

        $shipId = $selectedShip->get_id();
        $shipPosi = $selectedShip->get_position();
        $freeFields = array();
        for ($y = $shipPosi[0][1] - 1; $y < $shipPosi[0][1] + $this->shipSizes[$shipId]['y'] + 1; $y++) {
            for ($x = $shipPosi[0][0] - 1; $x < $shipPosi[0][0] + $this->shipSizes[$shipId]['x'] + 1; $x++) {
                if ($x < 0 || $y < 0 || $x > 9 || $y > 9) {
                    continue;
                }
                $field[$x . $y] = 0;
                array_push($freeFields, $x . $y);
            }
        }
        EventManager::add_event(new Event($user, 'battleship_handler', 'remove', array('id' => $shipId, 'position' => $freeFields)));
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
     * 5 = zerstört
     * 6 = BLOCKIERT BEIM SHOOTEN
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
            if ($x + 1 >= 0 && $x + 1 < 10 && $y - 1 >= 0 && $y - 1 < 10) {
                $field[$x + 1 . $y - 1] = 6;
            }
            if ($x + 1 >= 0 && $x + 1 < 10 && $y + 1 >= 0 && $y + 1 < 10) {
                $field[$x + 1 . $y + 1] = 6;
            }
            if ($x - 1 >= 0 && $x - 1 < 10 && $y - 1 >= 0 && $y - 1 < 10) {
                $field[$x - 1 . $y - 1] = 6;
            }
            if ($x - 1 >= 0 && $x - 1 < 10 && $y + 1 >= 0 && $y + 1 < 10) {
                $field[$x - 1 . $y + 1] = 6;
            }
            return true;
        }
        return null;
    }

    /**
     * add_player
     *
     * Fügt einen Spieler dem Spiel hinzu..
     *
     * @param User $player Spieler
     */
    public function add_player($player)
    {
        $playerShips = null;
        $playerField = null;
        $targetField = null;
        if (is_null($this->playerOne) || $this->playerOne->disconnected()) {
            $this->playerOne = $player;
            $playerShips = &$this->playerOneShips;
            $playerField = &$this->playerOneField;
            $targetField = &$this->playerTwoField;
        } else if (is_null($this->playerTwo) || $this->playerTwo->disconnected()) {
            $this->playerTwo = $player;
            $playerField = &$this->playerTwoField;
            $playerShips = &$this->playerTwoShips;
            $targetField = &$this->playerOneField;
        } else {
            return;
        }

        if ($this->playerTurn->disconnected()) {
            $this->playerTurn = $player;
        }
        $this->reconnect_player($player, $playerShips, $playerField, $targetField);
    }

    /**
     * reconnect_player
     *
     * Diese Funktion wird beim erneuten verbinden von einem Spieler aufgerufen.
     * Diese Funktion bringt den neu verbundenen Spieler auf den aktuellsten Stand.
     *
     * @param User $player Der Spieler
     * @param Array &$playerShips Die Schiffe von dem Spieler
     * @param Array &$playerField Das Spielfeld von dem Spieler
     * @param Array &$targetField Das Spielfeld von dem Gegner
     */
    private function reconnect_player($player, &$playerShips, &$playerField, &$targetField)
    {
        if (!$this->gameStarted) {
            if (count($playerShips) <= 0) {
                return;
            }
            $this->send_limit($player, $playerShips);
            EventManager::add_event(new Event($player, 'battleship_handler', 'reconnect', array(
                'own_field' => $playerField,
                'game_started' => false,
            )));
            return;
        }

        $temp = $targetField;
        foreach ($targetField as $key => $value) {
            $value != 4 ? $temp[$key] = $value : $temp[$key] = 0;
        }

        EventManager::add_event(new Event($player, 'battleship_handler', 'reconnect', array(
            'own_field' => $playerField,
            'enemy_field' => $temp,
            'game_started' => $this->gameStarted,
            'my_turn' => $this->playerTurn == $player,
        )));
    }

    /**
     * fill_field
     *
     * Hier werden die beiden Arrays, die die Felder verwalten, gefüllt.
     *
     * Die "0" steht für leeres Feld.
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

    /**
     * send_limit
     *
     * Diese Funktion sagt dem Client welche Schiffe er noch platzieren kann/darf.
     *
     * @param User $player Der Spieler/Client
     * @param Array $ships Die schiffe von dem Spieler/Client
     */
    private function send_limit($player, $ships)
    {
        $counter = 0;
        foreach ($this->shipLimit as $key => $value) {
            foreach ($ships as $sid) {
                if (substr($sid->get_id(), 0, 5) == $key) {
                    $counter++;
                }
            }
            if ($counter >= $value) {
                EventManager::add_event(new Event($player, 'battleship_handler', 'limit', array('ship' => $key)));
                $counter = 0;
            }
        }
    }

    /**
     * remove_player
     *
     * Wird aufgerufen wenn ein Spieler die verbindung getrennt hat.
     * Überprüft welche Spieler das Spiel verlassen hat, und setzten diesen dann auf NULL.
     *
     * @param User $player Der Spieler(Socket) zum entfernen
     */
    public function remove_player($player)
    {
        if ($this->playerOne == $player) {
            $this->playerOne = null;
        } else {
            $this->playerTwo = null;
        }
    }

    /**
     * missing_player
     *
     * Gibt zurück ob ein Spieler fehlt.
     *
     * @return bool
     */
    public function missing_player()
    {
        return is_null($this->playerOne) || $this->playerOne->disconnected() || is_null($this->playerTwo) || $this->playerTwo->disconnected() ? true : false;
    }

    /**
     * destroy_time
     *
     * Gibt zurück wann das Spiel wegen inactivity gelöscht werden soll.
     *
     * @return int
     */
    public function destroy_time()
    {
        return $this->lastMove + Battleship::DESTROY_TIME;
    }

}
