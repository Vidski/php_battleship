<?php

class Player
{

    private $playerUsername;
    private $playerSocket;

    public function __construct($playerUsername, $playerSocket) {
        $this->playerUsername = $playerUsername;
        $this->playerSocket = $playerSocket;
    }

}