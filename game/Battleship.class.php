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

    private $playerTurn;
    private $lastMove; //Wird bei jeder Action aktualisiert ( $lastMove = time() )
    private $destroyTime = 600; //Falls in <Sekunden> keine Action passiert wird das Spiel gelöscht

    private $playerOne;
    private $playerOneField;
    private $playerOneShips;

    private $playerTwo;
    private $playerTwoField;
    private $playerTwoShips;

    /**
     * __construct für Battleship.class
     * @param  User $playerOne Spieler1
     * @param  User $playerTwo Spieler2
     */
    public function __construct($playerOne, $playerTwo)
    {
        $this->playerOne = $playerOne;
        $this->playerTwo = $playerTwo;
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

        switch ($messageObj->content->action) {

            case 'shoot':
                if ($this->playerTurn != $user) {
                    return null;
                }
                return $this->handle_shoot($messageObj->content->position->x, $messageObj->content->position->y, $user);

            case 'place':
                switch ($user) {

                    case $this->playerOne:
                        return $this->handle_place($messageObj->content->position->x, $messageObj->content->position->y, $messageObj->content->ship, $this->playerOneField, $this->playerOneShips);

                    case $this->playerTwo:
                        return $this->handle_place($messageObj->content->position->x, $messageObj->content->position->y, $messageObj->content->ship, $this->playerTwoField, $this->playerTwoShips);

                    default:
                        return null;
                }

            default:
                return null;
        }
    }

    public function handle_shoot($x, $y, $user)
    {
        $temp = $this->playerTurn;
        if ($this->playerTurn == $this->playerOne) {
            $other_player = $this->playerTwo;
        } else {
            $other_player = $this->playerOne;
        }

        $targetField;
        $targetShips;
        switch ($this->playerTurn) {

            case $this->playerOne:
                $targetField = $this->playerTwoField;
                $targetShips = $this->playerTwoShips;
                break;

            case $this->playerTwo:
                $targetField = $this->playerOneField;
                $targetShips = $this->playerOneShips;
                break;

            default:
                return null;
        }

        $result = $this->check_hit($x, $y, $targetField, $targetShips);

        if (is_null($result)) {
            return null;
        }

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

        $pOne = array(
            'x' => $x,
            'y' => $y,
            'field' => 'right',
            'hit' => $result,
            'myturn' => $user == $this->playerTurn,
            'ship' => is_null($deadShip) ? null : array(
                'id' => $deadShip->get_id(),
                'position' => $deadShip->get_position(),
            ),
        );

        $pTwo = $pOne;
        $pTwo['field'] = 'left';
        $pTwo['myturn'] = $user != $this->playerTurn;

        return $this->build_packet('send_messages', 'shoot', array('users' => array($temp, $other_player), 'message' => array($pOne, $pTwo)));
    }

    public function handle_place($posX, $posY, $ship, &$field, &$ships)
    {
        if ($field[$posX . $posY] == 0) {
            for ($y = $posY - 1; $y < $posY + $this->shipSizes[$ship]['y']; $y++) {
                for ($x = $posX - 1; $x < $posX + $this->shipSizes[$ship]['x'] + 1; $x++) {
                    if ($x < 0 || $y < 0 || $x > 9 || $y > 9) {
                        continue;
                    }
                    if ($field[$x . $y] == 1) {
                        return $this->build_packet('send_message', 'place', 'Cant place here');
                    }
                }
            }
        } else {
            return $this->build_packet('send_message', 'place', 'Cant place here');
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
        array_push($ships, new Ship($placed, $ship, $this->shipSizes[$ship]));

        return $this->build_packet('send_message', 'place', array('placed' => $placed, 'blocked' => $blocked));
    }

    public function check_hit($x, $y, &$field)
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

    public function build_packet($function, $action, $content)
    {
        return array(
            'handler' => 'battleship_handler',
            'function' => $function,
            'action' => $action,
            'content' => $content,
        );
    }

    public function set_player_two($player)
    {
        $this->playerTwo = $player;
    }

    public function fill_field()
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
        return $this->lastMove + $this->destroyTime;
    }

}
