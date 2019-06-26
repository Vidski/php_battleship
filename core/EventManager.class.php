<?php

require 'Stack.class.php';
require 'Event.class.php';

/**
 * EventManager.class.php Ist ein singleton der alle Events verwaltet
 *
 * @author  David Rydwanski, Stefan Hackstein
 */
class EventManager
{

    private static $instance; //EventManager instance
    private static $events; //Stack an Events


    private function __construct()
    {
        self::$events = new Stack();
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
     * add_event
     *
     * Fügt ein Event dem Stack hinzu.
     * 
     * @param Event Das neue Event
     */
    public function add_event($event)
    {
        self::$events->push($event);
    }

    /**
     * events
     * 
     * @return Stack Gibt den Stack an Events zurück.
     */
    public function events()
    {
        return self::$events;
    }

}
