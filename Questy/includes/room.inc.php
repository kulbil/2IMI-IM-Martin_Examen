<?php


class room {

    public $number;
    public $monster;
    public $weapon;
    
    function __construct($number, $monster, $weapon) {

        $this->number = $number;
        $this->monster = $monster;
        $this->weapon = $weapon;
        
        
        $weaponChance = rand(1, 5);
        if ($weaponChance == 5) {
            $this->weapon = $weapon;
        } else {
            $this->weapon = "none";
        }

        if ($this->monster->health <= 0) {
            nextRoom();
        }
        
    }
}
?>



