<?php

/**
 * QueueManager.class.php ist ein singleton der die Spielersuche verwaltet.
 *
 * @author  David Rydwanski, Stefan Hackstein
 */
class QueueManager
{

    private static $instance; //QueueManager instance
    private static $playersInQueue; //Array an Spielern, die in der Queue sind

    private function __construct()
    {
        self::$playersInQueue = array();
    }

    /**
     * init
     * 
     * Eine neue Instance initialisieren.
     */
    public static function init()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * add_player
     *
     * Fügt ein Player dem Array hinzu.
     * 
     * @param User Der neue User
     */
    public function add_player($player)
    {
        array_push(self::$playersInQueue, $player);
    }

    /**
     * getPlayersInQueue
     * 
     * @return Array Gibt den Array an Spielern zurück.
     */
    public function getPlayersInQueue()
    {
        return self::$playersInQueue;
    }

}
