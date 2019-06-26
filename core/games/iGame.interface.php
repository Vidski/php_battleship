<?php

/**
 * iGame.interface.php Interface für neue Spiele
 *
 * @author  David Rydwanski, Stefan Hackstein
 */
interface iGame
{
    public function add_player($player);
    public function remove_player($player);
    public function missing_player();
    public function destroy_time();
}
