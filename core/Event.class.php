<?php

/**
 * Event.class.php beinhaltet ein Paket welches an den Client verschickt wird.
 * 
 * @author  David Rydwanski, Stefan Hackstein
 */
class Event
{

    private $packet; //Das Paket als Array
    private $user; //Das Ziel (Client)

    public function __construct($user, $handler, $action, $content)
    {
        $this->user = $user;
        $this->packet = $this->build_packet($handler, $action, $content);
    }

    /**
     * build_packet
     * 
     * Baut das Paket welches später zum Client geschickt wird.
     * 
     * @param String $handler
     * @param String $action
     * @param Object $content 
     */
    private function build_packet($handler, $action, $content)
    {
        return array(
            'handler' => $handler,
            'action' => $action,
            'content' => $content,
        );
    }

    /**
     * get_user
     * 
     * @return User
     */
    public function get_user()
    {
        return $this->user;
    }

    /**
     * get_packet
     * 
     * @return Array
     */
    public function get_packet()
    {
        return $this->packet;
    }

}
