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
    }

    public function action($messageObj, $user = null)
    {
        switch ($messageObj->content->action) {

            case 'shoot':
<<<<<<< HEAD
                return $this->build_packet('send_message_room', 'shoot', array('users' => $user->get_room()->get_players()));
            
            case 'place':
                return null;
=======
                $x = $messageObj->content->position->x;
                $y = $messageObj->content->position->y;
                $player = $user;
                if ($this->check_hit($x, $y, $player)) {
                    return $this->build_packet('send_message_room', 'shoot', array('users' => $user->get_room()->get_players(), 'Position X' => $x, 'Position Y' => $y));
                }
                break;
>>>>>>> 90fd5df0ad770e35567911c236dfbec5f17cf629
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

<<<<<<< HEAD
    public function place_ship($x, $y, $ship, $user) 
    {
        if ($user == $playerOne) {
            if ($playerOneField[xy] == "0") {
                
            }
        }
    }

    public function check_ship_placement() 
=======
    public function check_ship_placement()
>>>>>>> 90fd5df0ad770e35567911c236dfbec5f17cf629
    {
        print($this->playerOneField['11']);
    }

    public function check_hit($x, $y, $player)
    {
        return true;
    }

    public function fill_field()
    {
        for ($y = 0; $y < 10; $y++) {
            for ($x = 0; $x < 10; $x++) {
                $this->playerOneField[$x.$y] = "0";
                $this->playerTwoField[$x.$y] = "0";
            }
        }
    }

}
