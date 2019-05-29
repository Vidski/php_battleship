<?php

class Battleship implements iHandler
{
    private $playerTurn;
    private $lastMove;

    private $playerOne;
    private $playerOneField;

    private $playerTwo;
    private $playerTwoField;

    public function __construct($playerOne, $playerTwo)
    {
        $this->playerOne = $playerOne;
        $this->playerTwo = $playerTwo;
        $this->playerOneField = array();
        $this->playerTwoField = array();
        $this->fill_field();
        $this->playerTurn = $playerOne;
    }

    public function action($messageObj, $user = null)
    {
        switch ($messageObj->content->action) {

            case 'shoot':
                if ($this->playerTurn != $user)
                    return null;

                $x = $messageObj->content->position->x;
                $y = $messageObj->content->position->y;

                $temp = $this->playerTurn;

                if($this->playerTurn == $this->playerOne)
                    $other_player = $this->playerOne;
                else
                    $other_player = $this->playerTwo;

                $result = $this->check_hit($x, $y);
                $this->playerTurn = $other_player;
                
                return $this->build_packet('send_message_for_shoot', 'shoot',array(
                    array(
                        'user' => $temp,
                        'positionX' => $x,
                        'positionY' => $y,
                        'field' => 'right',
                        'result' => $result
                    ),
                    array(
                        'user' => $other_player,
                        'positionX' => $x,
                        'positionY' => $y,
                        'field' => 'left',
                        'result' => $result
                    )
                ));

            case 'place':
                print_r($messageObj);
                return null;

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

    public function check_ship_placement()
    {
        print($this->playerOneField['11']);
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
        }
    }

    public function fill_field()
    {
        for ($y = 0; $y < 10; $y++) {
            for ($x = 0; $x < 10; $x++) {

                $this->playerOneField[$x . $y] = "1";
                $this->playerTwoField[$x . $y] = "1";
            }
        }
    }

}
