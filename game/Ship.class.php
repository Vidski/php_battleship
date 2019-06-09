<?php

/**
 * Ship.class.php
 * @author  David Rydwanski
 */
class Ship
{

    private $shipHealth;
    private $shipPosition;
    private $shipSize;
    private $shipId;

    public function __construct($shipPosition, $shipId, $shipSize)
    {
        $this->shipPosition = $shipPosition;
        $this->shipId = $shipId;
        $this->shipSize = $shipSize;
        $this->shipHealth = ($this->shipSize['x'] > $this->shipSize['y']) ? $this->shipSize['x'] : $this->shipSize['y'];
    }

    public function is_dead($x, $y)
    {
        if (in_array($x . $y, $this->shipPosition)) {
            $this->shipHealth--;
            if ($this->shipHealth <= 0) {
                //VERSUNKEN
                return true;
            }
        }
        return false;
    }

    public function get_position()
    {
        return $this->shipPosition;
    }

    public function get_id()
    {
        return $this->shipId;
    }

}
