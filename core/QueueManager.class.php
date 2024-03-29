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

                $rejoinRoom = false;
                $joinPlayer = 0;
                $hostPlayer = 0;
                if (!is_null(self::$playersInQueue[0]->get_room()) && !self::$playersInQueue[0]->get_room()->is_empty()) {
                    $rejoinRoom = true;
                    $hostPlayer = 0;
                    $joinPlayer = 1;
                } else if (!is_null(self::$playersInQueue[1]->get_room()) && !self::$playersInQueue[1]->get_room()->is_empty()) {
                    $rejoinRoom = true;
                    $hostPlayer = 1;
                    $joinPlayer = 0;
                }

                if ($rejoinRoom) {
                    $newRoom = self::$playersInQueue[$hostPlayer]->get_room();
                    if ($newRoom->get_game()->missing_player()) {
                        EventManager::add_event(new Event(self::$playersInQueue[$joinPlayer], 'rooms_handler', 'join_room', array('joined' => true, 'pin' => $newRoom->get_pin())));
                        $newRoom->add_player(self::$playersInQueue[$joinPlayer]);
                        $newRoom->get_game()->add_player(self::$playersInQueue[$joinPlayer]);
                    }
                } else {
                    $player2 = self::$playersInQueue[0];

                    $game = new Battleship($player, $player2);
                    $newRoom = $roomHandler->new_room($player, $game->get_max_players());
                    $newRoom->set_public(true);
                    $newRoom->new_game($game);
                    $newRoom->add_player($player2);

                    $rUsers = $newRoom->get_players();
                    EventManager::add_event(new Event($player, 'rooms_handler', 'create_room', array('pin' => $newRoom->get_pin())));
                    EventManager::add_event(new Event($player2, 'rooms_handler', 'join_room', array('joined' => true, 'pin' => $newRoom->get_pin())));
                }

                $rUsers = $newRoom->get_players();
                foreach ($rUsers as $rUser) {
                    EventManager::add_event(new Event($rUser, 'rooms_handler', 'receive_message', array('message' => $player->get_username() . ' joined the room.')));
                    EventManager::add_event(new Event($rUser, 'rooms_handler', 'receive_message', array('message' => self::$playersInQueue[0]->get_username() . ' vs ' . self::$playersInQueue[1]->get_username())));
                }
                self::remove_player(self::$playersInQueue[1]);
                self::remove_player(self::$playersInQueue[0]);
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
