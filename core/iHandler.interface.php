<?php

/**
 * iHandler.interface.php Interface für Handlers
 *
 * @author  David Rydwanski, Stefan Hackstein
 */
interface iHandler
{
    /**
     * action
     * 
     * Hier werden die Pakete von dem Client verarbeitet
     *
     * @param  Array $messageObj Das Packet von dem Client
     * @param  User $user Der User (Client)
     */
    public function action($messageObj, $user = null);
}
