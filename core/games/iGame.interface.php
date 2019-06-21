<?php

/**
 * iGame.interface.php
 *
 * @author  David Rydwanski
 */
interface iGame
{
    public function add_player($player);
    public function remove_player($player);
    public function missing_player();
    public function destroy_time();
}
