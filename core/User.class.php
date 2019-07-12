<?php

/**
 * User.class.php Speichert Spieler Informationen
 *
 * @author  David Rydwanski, Stefan Hackstein
 */
class User
{

    private $id; //Die id des Users
    private $socket; //Der Socket des Users
    private $handshake; //Wurde mit dem Server gehandshaked
    private $disconnected; //User ist nicht mehr verbunden

    private $username; //Der Name des Users
    private $room; //Der Raum im welchem der User befindet 


    public function __construct($id, $socket)
    {
        $this->id = $id;
        $this->socket = $socket;
        $this->handshake = false;
        $this->disconnected = false;

        $this->username = "Player";
        $this->room = null;
    }

    /**
     * get_id
     *
     * Gibt die id des Users zurück
     *
     * @return ID
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * get_socket
     *
     * Gibt den Socket des Users zurück.
     *
     * @return Socket
     */
    public function get_socket()
    {
        return $this->socket;
    }

    /**
     * handshake
     *
     * Wurde mit dem Server gehandshaked
     */
    public function handshake()
    {
        $this->handshake = true;
    }

    /**
     * did_handshake
     *
     * Gibt zurück ob der User mit dem Server bereits gehandshaked ist
     *
     * @return bool
     */
    public function did_handshake()
    {
        return $this->handshake;
    }

    /**
     * disconnect
     *
     * Die Verbindung zum User wurde unterbrochen.
     *
     * @return bool
     */
    public function disconnect()
    {
        $this->disconnected = true;
    }

    /**
     * disconnected
     *
     * Gibt zurück ob der User noch verbunden ist
     *
     * @return bool
     */
    public function disconnected()
    {
        return $this->disconnected;
    }

    /**
     * set_username
     *
     * Setzt den Username der Users
     *
     * @param String $username Der neue Name
     */
    public function set_username($username)
    {
        $length = strlen($username);
        if ($length < 16 && $length > 0) {
            $this->username = htmlspecialchars($username);
        }
    }

    /**
     * get_username
     *
     * Gibt den Namen des Users zurück
     *
     * @return String
     */
    public function get_username()
    {
        return $this->username;
    }

    /**
     * set_room
     *
     * Setzt den Raum im welchen sich der User befindet
     * 
     * @param Room $room Der Raum
     */
    public function set_room($room)
    {
        $this->room = $room;
    }

    /**
     * get_room
     *
     * Gibt den Raum des Users zurück
     *
     * @return Room
     */
    public function get_room()
    {
        return $this->room;
    }

}
