<?php

//monster object

class monster {

    public $name;
    public $strength;
    public $health;
    //public $sprite;

    function __construct($name, $strength, $health) {
        $this->name = $name;
        $this->strength = $strength;
        $this->health = $health;
        //$this->sprite = $sprite;
    }

}
?>
