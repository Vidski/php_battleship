<?php

class User
{

    private $id;
    private $socket;
    private $handshake;

    private $username;

    public function __construct($id, $socket)
    {
        $this->id = $id;
        $this->socket = $socket;
        $this->handshake = false;
        $this->username = "Player";
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_socket()
    {
        return $this->socket;
    }

    public function handshake() 
    {
        $this->handshake = true;
    }

    public function did_handshake()
    {
        return $this->handshake;
    }

    public function set_username($username)
    {
        $length = strlen($username);
        if ($length < 16 && $length > 0)
            $this->username = htmlspecialchars($username);
    }

    public function get_username()
    {
        return $this->username;
    }

}