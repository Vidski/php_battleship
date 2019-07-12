<?php

/**
 * Ship.class.php beinhaltet Informationen von dem Schiff
 *
 * @author  David Rydwanski, Stefan Hackstein
 */
class Ship
{

    private $shipHealth; //Das Leben von dem Schiff (Die Größe)
    private $shipPosition; //Die Position von dem Schiff
    private $shipSize; //Die Größe von dem Schiff
    private $shipId; //Die ShipID (z.B. ship5V) wichtig für den Client

    public function __construct($shipPosition, $shipId, $shipSize)
    {
        $this->shipPosition = $shipPosition;
        $this->shipId = $shipId;
        $this->shipSize = $shipSize;
        $this->shipHealth = ($this->shipSize['x'] > $this->shipSize['y']) ? $this->shipSize['x'] : $this->shipSize['y'];
    }

    /**
     * is_dead
     *
     * Diese Funktion gibt zurück ob das Schiff den Schuss überlebt hat.
     *
     * @param integer $x X-Position
     * @param integer $y Y-Position
     * @return bool Überlebt(true/false)
     */
    public function is_dead($x, $y)
    {
        if (in_array($x . $y, $this->shipPosition)) {
            $this->shipHealth--;
            return $this->shipHealth <= 0;
        }
        return false;
    }

    /**
     * check_if_me
     *
     * Diese Funktion gibt zurück ob der Schuss das Schiff getroffen hat.
     *
     * @param integer $x X-Position
     * @param integer $y Y-Position
     * @return bool Getroffen(true/false)
     */
    public function check_if_me($x, $y)
    {
        return in_array($x . $y, $this->shipPosition);
    }

    /**
     * is_alive
     *
     * Diese Funktion gibt zurück ob das Schiff am leben ist.
     *
     * @return bool Lebt(true/false)
     */
    public function is_alive()
    {
        return $this->shipHealth > 0;
    }

    /**
     * get_position
     *
     * Diese Funktion gibt die Position des Schiffes zurück.
     *
     * @return Array Positionen
     */
    public function get_position()
    {
        return $this->shipPosition;
    }

    /**
     * get_position_formatted
     *
     * Diese Funktion gibt die formattierte Position des Schiffes zurück,
     * diese ist leichter für den Client zu lesen.
     *
     * @return Array Positionen
     */
    public function get_position_formatted()
    {
        $arr = array();

        foreach ($this->shipPosition as $val) {
            array_push($arr, array('x' => $val[0], 'y' => $val[1]));
        }

        return $arr;
    }

    /**
     * get_id
     *
     * Diese Funktion gibt die ID des Schiffes zurück.
     *
     * @return String Die ID des Schiffes
     */
    public function get_id()
    {
        return $this->shipId;
    }

}
