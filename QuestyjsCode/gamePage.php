<?php
    include "includes/functions.inc.php";
    include "header.php";
    if (!isset($_SESSION["userid"])) {
        header("location: ../index.php");
        exit();
    }
?>

<div id=gameWrapper>
    <div id="gameSCLeft">
        <div id="gameInfoWindow">
            <p>INFO WINDOW</p>
            <p>You started your dangerous journey and encountered a terrible foe!</p>
        </div>
        <div id="playerItemInfoWindow">
            <div id="playerInfo">
                <p id="roomPIIW">Room: </p>
                <div id="hpPIIWDiv">
                    <p id="hpPIIW">HP: </p>
                    <div id="hpBarPIIW">
                        <div id="hpBarBarPIIW"></div>
                    </div>
                </div>
                <p id="weaponPIIW">Weapon: </p>
            </div>
            <div id="itemInfo">
                <p id="healingPIIW">Healing: </p>
                <p id="zeusPIIW">Zeus: </p>
                <p id="strengthPIIW">Strength: </p>
            </div>
        </div>
    </div>
    <div id="gameSCRight">
        <div id="monsterWindow">
            <div id="monsterWindowTop">
                <div id="monsterName"></div>
                <div id="monsterStats">
                    <p id="healthMonSts">Health: </p>
                    <p id="strengthMonSts">Strength: </p>
                </div>
            </div>
            <div id="monsterWindowBottom">
                <img id="monsterSprite">
                <img id="monsterPlatform" src="../QuestyjsAssets/UI/Backgrounds/MonsterPlatform.png" alt="">
            </div>
        </div>
        <div id="selectWindow">
            <button class="menuButton" id="fightBut"></button>
            <button class="menuButton" id="itemsBut"></button>
            <button class="menuButton" id="runBut"></button>
            <button class="menuButton" id="quitBut"></button>
        </div>
    </div>
</div>

<?php
    echo '<form id="playerDataTransfer" action="includes/dataTransfer.inc.php" method="GET">';
    echo '<input id="php2jsData" type="hidden" name="php2js" value="'.$_SESSION['userplayerdata'].'"></input>';
    echo '<input id="php2jsUid" type="hidden" name="php2js" value="'.$_SESSION['useruid'].'"></input>';
    echo '<input id="js2phpData" type="hidden" name="js2phpData" value=""></input>';
    echo '<input id="js2phpHs" type="hidden" name="js2phpHs" value=""></input>';
    echo '</form>';
    
    echo '<form method="get" id="quitForm" action="includes/logout.inc.php">';
    echo '<input id="quitInput" type="hidden" name="quitInput" value=""></input>';
    echo '</form>';
?>

<?php
    include_once 'footer.php';
?> 