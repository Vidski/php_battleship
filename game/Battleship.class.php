<?php

class Battleship implements iHandler
{
    private $shipLimit = array(2 => 4, 3 => 3, 4 => 2, 5 => 1);
    private $shipSizes = array(
        "ship2V"=> array(x => 1, y => 2),
        "ship3V"=> array(x => 1, y => 3),
        "ship4V"=> array(x => 1, y => 4),
        "ship5V"=> array(x => 1, y => 5),
        "ship2H"=> array(x => 2, y => 1),
        "ship3H"=> array(x => 3, y => 1),
        "ship4H"=> array(x => 4, y => 1),
        "ship5H"=> array(x => 5, y => 1),
    );

    private $playerTurn;
    private $lastMove;

    private $playerOne;
    private $playerOneField;
    private $playerOneShips = array(2 => 0, 3 => 0, 4 => 0, 5 => 0);

    private $playerTwo;
    private $playerTwoField;
    private $playerTwoShips = array(2 => 0, 3 => 0, 4 => 0, 5 => 0);

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

                return $this->build_packet('send_message_room', 'shoot', array(
                    'users' => $user->get_room()->get_players(),
                    'positionX' => $x,
                    'positionY' => $y,
                    'userid' => $this->playerTurn->get_id(),
                    'ergebnis' => $this->check_hit($x, $y)
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

    public function check_ship_placement($x, $y, $ship)
    {
        switch ($this->playerTurn) {

            case $this->playerOne:
                if ($this->playerOneField[$x . $y] == 0) {
                    for ($y = 0; $y < 10; $y++) {
                        for ($x = 0; $x < 10; $x++) {
                            $this->playerOneField[$x . $y] = "1";
                            $this->playerTwoField[$x . $y] = "1";
                        }
                    }
                }
            break;
            
            case $this->playerTwo:
                
            break;

            default:
                # code...
                break;
        }
        print($this->playerOneField['11']);
    }

    public function check_hit($x, $y)
    {
        switch ($this->playerTurn) {

            case $this->playerOne:
                $this->playerTurn = $this->playerTwo;
                if ($this->playerTwoField[$x . $y] == "0") {
                    $this->playerTwoField[$x . $y] = "3";
                    return false;
                } else if ($this->playerTwoField[$x . $y] == "1") {
                    $this->playerTwoField[$x . $y] = "2";
                    return true;
                }
                break;

            case $this->playerTwo:
                $this->playerTurn = $this->playerOne;
                if ($this->playerOneField[$x . $y] == "0") {
                    $this->playerOneField[$x . $y] = "3";
                    return false;
                } else if ($this->playerOneField[$x . $y] == "1") {
                    $this->playerOneField[$x . $y] = "2";
                    return true;
                }
                break;

            default:
                return null;
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
