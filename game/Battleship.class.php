<?php

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
    private $lastMove;

    private $playerOne;
    private $playerOneField;
    private $playerOneShips = array("ship2" => 0, "ship3" => 0, "ship4" => 0, "ship5" => 0);

    private $playerTwo;
    private $playerTwoField;
    private $playerTwoShips = array("ship2" => 0, "ship3" => 0, "ship4" => 0, "ship5" => 0);

    public function __construct($playerOne, $playerTwo)
    {
        $this->playerOne = $playerOne;
        $this->playerTwo = $playerTwo;
        $this->playerOneField = array();
        $this->playerTwoField = array();
        $this->playerTurn = $playerOne;
        $this->fill_field();
    }

    public function action($messageObj, $user = null)
    {
        switch ($messageObj->content->action) {

            case 'shoot':
                if ($this->playerTurn != $user) {
                    return null;
                }

                $x = $messageObj->content->position->x;
                $y = $messageObj->content->position->y;

                $temp = $this->playerTurn;
                if ($this->playerTurn == $this->playerOne) {
                    $other_player = $this->playerTwo;
                } else {
                    $other_player = $this->playerOne;
                }

                $result = $this->check_hit($x, $y);
                if (!$result) {
                    $this->playerTurn = $other_player;
                }

                $pOne = array(
                    'x' => $x,
                    'y' => $y,
                    'field' => 'right',
                    'hit' => $result,
                );
                $pTwo = $pOne;
                $pTwo['field'] = 'left';

                return $this->build_packet('send_messages', 'shoot', array('users' => array($temp, $other_player), 'message' => array($pOne, $pTwo)));

            case 'place':
                $x = $messageObj->content->position->x;
                $y = $messageObj->content->position->y;
                $ship = $messageObj->content->ship;
                $data = null;
                switch ($user) {

                    case $this->playerOne:
                        $data = $this->check_ship_placement($x, $y, $ship, $this->playerOneField);
                        break;

        
                    case $this->playerTwo:
                        $data = $this->check_ship_placement($x, $y, $ship, $this->playerTwoField);
                        break;
                }
                return $data;

            default:
                return null;
        }
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

    public function check_ship_placement($posX, $posY, $ship, &$field)
    {   
        if ($field[$posX . $posY] == 0) {
            for ($y = $posY - 1; $y < $posY + $this->shipSizes[$ship]['y']; $y++) {
                for ($x = $posX - 1; $x < $posX + $this->shipSizes[$ship]['x'] + 1; $x++) {
                    if ($x < 0 || $y < 0 || $x > 9 || $y > 9) {
                        continue;
                    }
                    print($field[$x . $y]);
                    if ($field[$x . $y] == "1") {
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
                $field[$x . $y] = "4";
                array_push($blocked, $x . $y);
            }
        }

        //SCHIFF PLATZIEREN
        $placed = array();
        for ($y = $posY; $y < $posY + $this->shipSizes[$ship]['y']; $y++) {
            for ($x = $posX; $x < $posX + $this->shipSizes[$ship]['x']; $x++) {
                $field[$x . $y] = "1";
                array_push($placed, $x . $y);
            }
        }
        //DEBUGGING
        // $st = "";
        // for ($y = 0; $y < 10; $y++) {
        //     for ($x = 0; $x < 10; $x++) {
        //         $st .= $field[$x . $y] . " ";
        //     }
        //     $st .= "\n";
        // }
        // print($st);

        return $this->build_packet('send_message', 'place', array('placed' => $placed, 'blocked' => $blocked));
    }

    public function check_hit($x, $y)
    {
        switch ($this->playerTurn) {

            case $this->playerOne:
                if ($this->playerTwoField[$x . $y] == "0") {
                    $this->playerTwoField[$x . $y] = "3";
                    return false;
                } else if ($this->playerTwoField[$x . $y] == "1") {
                    $this->playerTwoField[$x . $y] = "2";
                    return true;
                }
                break;

            case $this->playerTwo:
                if ($this->playerOneField[$x . $y] == "0") {
                    $this->playerOneField[$x . $y] = "3";
                    return false;
                } else if ($this->playerOneField[$x . $y] == "1") {
                    $this->playerOneField[$x . $y] = "2";
                    return true;
                }
                break;

            default:
                return false;
        }
    }

    public function fill_field()
    {
        for ($y = 0; $y < 10; $y++) {
            for ($x = 0; $x < 10; $x++) {
                $this->playerOneField[$x . $y] = "0";
                $this->playerTwoField[$x . $y] = "0";
            }
        }
    }

}
