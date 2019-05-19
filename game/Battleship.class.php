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
        $this->playerTWo = $playerTwo;
        $this->playerOneField = array();
        $this->playerTwoField = array();
        $this->fill_field();
    }

    public function action($messageObj, $user = null)
    {

    }

    public function build_packet($function, $action, $content)
    {

    }

    public function check_ship_placement() 
    {
        print($this->playerOneField['11']);
    }

    public function fill_field()
    {
        for ($y=0; $y < 10; $y++) { 
            for ($x=0; $x < 10; $x++) { 
                $this->playerOneField[xy] = "0";
                $this->playerTwoField[xy] = "0";
            }
        }
    }

}
