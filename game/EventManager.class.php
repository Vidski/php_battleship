<?php

require 'Stack.class.php';
require 'Event.class.php';

class EventManager
{

    private static $instance;
    private static $events;

    private function __construct()
    {
        self::$events = new Stack();
    }

    public static function init()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function add_event($event)
    {
        self::$events->push($event);
    }

    public function events()
    {
        return self::$events;
    }

}
