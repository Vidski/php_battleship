<?php

class Event 
{
    private $packet;
    private $user;

    public function __construct($user, $handler, $action, $content) 
    {
        $this->user = $user;
        $this->packet = $this->build_packet($handler, $action, $content);
    }

    private function build_packet($handler, $action, $content)
    {
        return array(
            'handler' => $handler,
            'action' => $action,
            'content' => $content
        );
    }

    public function get_user() 
    {
        return $this->user;
    }

    public function get_packet() 
    { 
        return $this->packet;
    }

}