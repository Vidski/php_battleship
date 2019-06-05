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

                $targetField;
                switch ($this->playerTurn) {

                    case $this->playerOne:
                        $targetField = $this->playerTwoField;
                        break;

                    case $this->playerTwo:
                        $targetField = $this->playerOneField;
                        break;

                    default:
                        return null;
                }

                $result = $this->check_hit($x, $y, $targetField);

                if (is_null($result))
                    return null;

                if (!$result) {
                    $this->playerTurn = $other_player;
                }

                $pOne = array(
                    'x' => $x,
                    'y' => $y,
                    'field' => 'right',
                    'hit' => $result,
                    'myturn' => $user == $this->playerTurn
                );
                $pTwo = $pOne;
                $pTwo['field'] = 'left';
                $pTwo['myturn'] = $user != $this->playerTurn;

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

        
        //$this->playerOneShips[substr($ship, 0, -1)]++;
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

    public function fill_field()
    {
        for ($y = 0; $y < 10; $y++) {
            for ($x = 0; $x < 10; $x++) {
                $this->playerOneField[$x . $y] = 0;
                $this->playerTwoField[$x . $y] = 0;
            }
        }
    }

}
