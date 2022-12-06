<?php
    include "includes/functions.inc.php";
    include "header.php";
?>

<?php
    //tar inn spiller og rom data
    $playerCharacter = unSerPlayer();
    $currentRoom = unserialize($_SESSION['currentRoom']);
?>

<div id=gameWrapper>

    <div id="gameSCLeft">
        <div id="gameInfoWindow" style="overflow: auto;">
            <?php
                //displayer alt av info som foregår i spillet
                echo "<p style='color: white;font-family: calibri;font-size: 3vh;margin-left: 10px;width: 100%;'>";
                if(!isset($_SESSION['infotext'])) {
                    $_SESSION['infotext'] = "INFO WINDOW";
                    echo $_SESSION['infotext'];   
                } else {
                    echo $_SESSION['infotext'];      
                } 
                echo "</p>";  
                
            ?>
        </div>
        <div id="playerItemInfoWindow">
            <?php
                //displayer character informasjon til spilleren
                echo '<p>Room: '.$playerCharacter->room.'</p>';
                echo '<p>HP: '.$playerCharacter->health.'</p>';
                echo '<p>Weapon: '.$playerCharacter->weapon->name.'('.$playerCharacter->weapon->damage.')</p>';
            ?>
        </div>
    </div>
    <div id="gameSCRight">
        <div id="monsterWindow">
            <div id="monsterWindowTop">
                <div id="monsterName">
                    <?php
                        echo $currentRoom->monster->name
                    ?>
                </div>
                <div id="monsterStats">
                    <?php
                        echo "Health: ". $currentRoom->monster->health;
                        echo "<br>";
                        echo "Strength: ". $currentRoom->monster->strength;
                    ?>
                </div>
            </div>
            <div id="monsterWindowBottom">
                
            </div>
        </div>
        <div id="selectWindow">
            <?php
                //forms som tar inn input fra spilleren og utfører funksjoner etter hva som blir trukker på
                //if statement som skjekker hva som skal bli displayed på knappene (actions eller items)
                if(isset($_SESSION['select']) && $_SESSION['select'] == "submitItems") {
                    echo '<form action="includes/actionSelect.inc.php" method="GET">';
                        echo  '<button name="option" value="submitItem1">Healing Potion X'.$playerCharacter->healing.'</button>';
                        echo  '<button name="option" value="submitItem2">Strength Potion</button>';
                        echo  '<button name="option" value="submitItem3">Zeus smite X'.$playerCharacter->zeus.'</button>';
                        echo  '<button name="option" value="back">Back</button>';
                    echo '</form>';
                } else {
                    echo '<form action="includes/actionSelect.inc.php" method="GET">';
                        echo '<button name="option" value="submitFight">Fight</button>';
                        echo '<button name="option" value="submitItems">Items</button>';
                        echo '<button name="option" value="submitRun">Run</button>';
                        echo '<button name="option" value="submitQuit">Save & Quit</button>';
                    echo '</form>';
                }
            ?>

        </div>
    </div>

</div>

<script>
    //skroller til bunnen av info vinduet når ny info blir lagt til
    var objDiv = document.getElementById("gameInfoWindow");
    objDiv.scrollTop = objDiv.scrollHeight;
</script>

<?php
    include_once 'footer.php';
?>