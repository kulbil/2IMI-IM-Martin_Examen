<?php

//player object

class player {

    public $room;
    public $health;
    public $weapon;
    public $healing;
    public $zeus;

    function __construct($room, $health, $weapon, $healing, $zeus) {
        $this->room = $room;
        $this->health = $health;
        $this->weapon = $weapon;
        $this->healing = $healing;
        $this->zeus = $zeus;
    }
}

?>
