<?php

class Event 
{
    private $packet;
    private $user;

    public function __construct($user, $handler, $function, $action, $content) 
    {
        $this->user = $user;
        $this->packet = $this->build_packet($handler, $function, $action, $content);
    }

    private function build_packet($handler, $function, $action, $content)
    {
        return array(
            'handler' => $handler,
            'function' => $function,
            'action' => $action,
            'content' => $content,
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