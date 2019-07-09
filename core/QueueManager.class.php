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
     * Wenn der Array dadurch größer gleich 2 ist, wird ein neuer Raum erstellt und es wird ein Raum erstellt.
     *
     * @param User Der neue User
     */
    public function add_player($roomHandler, $player)
    {
        if (!in_array($player, self::$playersInQueue)) {
            array_push(self::$playersInQueue, $player);
            if (count(self::$playersInQueue) >= 2) {

                self::remove_player($player);
                $newRoom = $roomHandler->new_room($player);
                $player->set_room($newRoom);
                $player2 = self::$playersInQueue[0];
                self::remove_player($player2);
                $player2->set_room($newRoom);
                $player->get_room()->new_game(new Battleship($player, $player2));
                $newRoom->add_player($player2);
                $rUsers = $newRoom->get_players();
                EventManager::add_event(new Event($player, 'rooms_handler', 'create_room', array('pin' => $newRoom->get_pin())));
                EventManager::add_event(new Event($player2, 'rooms_handler', 'join_room', array('joined' => true, 'pin' => $newRoom->get_pin())));

                foreach ($rUsers as $rUser) {
                    EventManager::add_event(new Event($rUser, 'rooms_handler', 'receive_message', array('message' => $player->get_username() . ' created the room.')));
                    EventManager::add_event(new Event($rUser, 'rooms_handler', 'receive_message', array('message' => $player2->get_username() . ' joined the room.')));

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

    /**
     * remove_player($player)
     *
     * Hier wir der Spieler aus dem Array entfernt.
     * Wird aufgerufen, wenn ein Spiel gefunden wurde, der Spieler Disconnected oder er die Suche abbricht.
     *
     * @param User $player Spieler der entfernt werden soll.
     */
    public function remove_player($player)
    {
        if (in_array($player, self::$playersInQueue)) {
            $key = array_search($player, self::$playersInQueue);
            unset(self::$playersInQueue[$key]);
            self::$playersInQueue = array_values(self::$playersInQueue);
        }
    }
}
