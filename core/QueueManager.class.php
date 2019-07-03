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
        if(!in_array($player, self::$playersInQueue)){
            print("test");

            array_push(self::$playersInQueue, $player);

            if(count(self::$playersInQueue) >= 2){
                remove_player($player);
                $newRoom = $this->new_room($player);
                $player->set_room($newRoom);
                $player2 = self::$playersInQueue[0];
                remove_player($player2);
                $player2->set_room($newRoom);
                $player->get_room()->new_game(new Battleship($player, $player2));
                $rUsers = $newRoom->get_players();
                foreach ($rUsers as $rUser) {
                    EventManager::add_event(new Event($user, 'rooms_handler', 'join_room', array('joined' => true, 'pin' => $newRoom->get_pin())));
                    EventManager::add_event(new Event($rUser, 'rooms_handler', 'receive_message', array('message' => $rUser->get_username() . ' joined the room.')));
                }
            }
        }
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

    PUBLIC function remove_player($player){
        if(in_array($player, self::$playersInQueue)){
            $key = array_search($player, $playersInQueue);
            unset($key);
            self::$playersInQueue = array_values(self::$playersInQueue);
        }
    }
}
