<?php

require 'Chat.class.php';

class Game  
{
    private $server;
    
    private $chatHandler;

    public function __construct($server) {
        $this->server = $server;
        $this->chatHandler = new Chat();
    }

    public function chat_action($msgObj) {
        return $this->chatHandler->action($msgObj);
    }

}
