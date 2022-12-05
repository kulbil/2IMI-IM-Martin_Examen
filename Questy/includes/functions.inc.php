<?php
include "dbh.inc.php";

include "player.inc.php";
include "monster.inc.php";
include "weapons.inc.php";
include "room.inc.php";
include "assetList.inc.php";



function emptyInputSignup($name, $uid, $pwd, $pwdRepeat) {
    if (empty($name) || empty($uid) || empty($pwd) || empty($pwdRepeat)) {
        return true;
    } else {
        return false;
    }
}

function pwdMatch($pwd, $pwdRepeat) {
    if($pwd !== $pwdRepeat) {
        return true;
    } else {
        return false;
    }
}



function uidTakenCheck($uid) {

    $sql = "SELECT * FROM users WHERE uid = ?;";
    $stmt = $GLOBALS['conn']->prepare($sql);

    $stmt->bind_param("s", $uid);
    $stmt->execute();

    $resultData = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    } else {
        return false;
    }
}

function createUser($name, $uid, $pwd) {

    $playerData = array(0, 100, 0, 5, 1);

    $playerDataSer = serialize($playerData);


    $sql = "INSERT INTO users (name, uid, userpwd, playerdata, highscore, joindate) VALUES (?, ?, ?, ?, 0, now());";
    $stmt = $GLOBALS['conn']->prepare($sql);
    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    $stmt->bind_param("ssss", $name, $uid, $hashedPwd, $playerDataSer);
    $stmt->execute();
}

function emptyInputLogin($uid, $pwd) {
    if (empty($uid) || empty($pwd)) {
        return true;
    } else {
        return false;
    }
}

function loginUser($uid, $pwd) {
    $uidCheck = uidTakenCheck($uid);

    if($uidCheck === false) {
        header("location: ../login.php?error=nouserfound");
        exit();
    }

    $hashedPwd = $uidCheck['userpwd'];
    $pwdCheck = password_verify($pwd, $hashedPwd);

    if($pwdCheck === false) {
        header("Location: ../login.php?error=wrongpassword");
        exit();
    } else if (password_verify($pwd, $hashedPwd)) {
        echo "you are logged in";
        
        session_start();
        $_SESSION["userid"] = $uidCheck['id'];
        $_SESSION["useruid"] = $uidCheck['uid'];
        $_SESSION["userplayerdata"] = $uidCheck['playerdata'];
        $_SESSION["userhighscore"] = $uidCheck['highscore'];
        //Player data
        //[Room Number, Health, Weapon, Healing potion, Zeus Potion, High Score]

        createPlayer();
        createRoom();

        header("location: ../index.php");
        exit();
    }
}

function logoutUser() {

    updDb();

    session_start();
    session_unset();
    session_destroy();

    header("location: ../index.php");
}


function createPlayer() { 
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
    //Jeg beklager så mye fortids meg. Dette problemet var ikke så vanskelig som vi trodde.
    $playerData = unserialize($_SESSION["userplayerdata"]);
    $playerCharacter = new player($playerData[0], $playerData[1], $GLOBALS['weaponList'][$playerData[2]], $playerData[3], $playerData[4]);
    $_SESSION['playerCharacter'] = serialize($playerCharacter);
}

function unSerPlayer() {
    return(unserialize($_SESSION['playerCharacter']));
}

function updDb() {
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
    
    $userPlayerData = $_SESSION["userplayerdata"];
    $playerId = $_SESSION["userid"];

    $sql = "UPDATE users SET playerdata=? where id=?";
    $stmt = $GLOBALS['conn']->prepare($sql);

    $stmt->bind_param("si", $userPlayerData, $playerId);
    $stmt->execute();
}

function updatePlayer($param, $value) {
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    }

    $userPlayerData = unserialize($_SESSION["userplayerdata"]);
    $currentPlayerStats = array($userPlayerData[0], $userPlayerData[1], $userPlayerData[2], $userPlayerData[3], $userPlayerData[4]);

    $playerCharacter = unSerPlayer();
    switch ($param) {
        case "room":
            $currentPlayerStats[0] = $value;
            $playerCharacter->room = $value;
        break;
        case "health":
            $currentPlayerStats[1] = $value;
            $playerCharacter->health = $value;
        break;
        case "weapon":
            $currentPlayerStats[2] = $value;
            $playerCharacter->weapon = $GLOBALS['weaponList'][$value];
        break;
        case "healing":
            $currentPlayerStats[3] = $value;
            $playerCharacter->healing = $value;
        break;
        case "zeus":
            $currentPlayerStats[4] = $value;
            $playerCharacter->zeus = $value;
        break;
    }
    
    $_SESSION['userplayerdata'] = serialize($currentPlayerStats);
    $_SESSION['playerCharacter'] = serialize($playerCharacter);
}

function createRanMonster() {
    $createdMonster = $GLOBALS['monsterList'][rand(0, 2)];
    return($createdMonster);
}

function createRanWeapon() {
    $createdWeapon = $GLOBALS['weaponList'][rand(0, 2)];
    return($createdWeapon);
}

function createRoom() {
    $currentRoom = new room((unSerPlayer()->room + 1), createRanMonster(), createRanWeapon());
    $_SESSION['currentRoom'] = serialize($currentRoom);
}

function fightSubmit() {
    $room = unserialize($_SESSION['currentRoom']);
    $player = unSerPlayer();

    $room->monster->health -= $player->weapon->damage;
    $_SESSION['infotext'] = $_SESSION['infotext']."<br>"."Player did ".$player->weapon->damage." damage";

    if($room->monster->health > 0) {
        $_SESSION['currentRoom'] = serialize($room);
        monsterTurn();
    } else {
        $_SESSION['infotext'] = $_SESSION['infotext']."<br>".$room->monster->name." døde";
        updatePlayer("room", $room->number);
        newRoom();
    }
}

function runSubmit() {

}

function itemsSubmit() {
    
}

function healingSubmit() {
    if(unSerPlayer()->healing > 0) {
        updatePlayer("healing", (unSerPlayer()->healing -= 1));
        if((unSerPlayer()->healing + 50) > 100) {
            updatePlayer("health", (unSerPlayer()->health = 100));
        } else {
            updatePlayer("health", (unSerPlayer()->health += 50));
        }
        $_SESSION['infotext'] = $_SESSION['infotext']."<br>Player used a Healing Potion and healed 50 health!";
        monsterTurn();
    }
}

function zeusSubmit() {
    $room = unserialize($_SESSION['currentRoom']);
    $monster = unserialize($_SESSION['currentRoom'])->monster;
    if(unSerPlayer()->zeus > 0) {
        updatePlayer("zeus", (unSerPlayer()->zeus -= 1));
        $_SESSION['infotext'] = $_SESSION['infotext']."<br>Player used a Zeus!";

        $_SESSION['infotext'] = $_SESSION['infotext']."<br>".$monster->name." døde";
        updatePlayer("room", $room->number);
        newRoom();

    }
}

function strengthSubmit() {
    //Ikke noe function enda oops...
}

function monsterTurn() {
    $monster = unserialize($_SESSION['currentRoom'])->monster;
    $player = unSerPlayer();
    
    updatePlayer("health", ($player->health -= $monster->strength));
    $_SESSION['infotext'] = $_SESSION['infotext']."<br>".$monster->name." did ".$monster->strength." damage";

    if($player->health <= 0) {
        gameOver();
    }
}

function newroom() {
    unset($_SESSION['currentRoom']);
    createRoom();
}

function gameOver() {
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    }
    
    $player = unSerPlayer();
    $highScore = $player->room;
    $playerId = $_SESSION["userid"];
    $oldHighscore = $_SESSION["userhighscore"];
    
    
    if ((isset($oldHighscore)) && ($oldHighscore < $highScore)) {
        $sql = "UPDATE users SET highscore=? where id=?";
        $stmt = $GLOBALS['conn']->prepare($sql);
    
        $stmt->bind_param("ii", $highScore, $playerId);
        $stmt->execute();
    }

    updatePlayer("room", 0);
    updatePlayer("health", 100);
    updatePlayer("weapon", 0);
    updatePlayer("healing", 5);
    updatePlayer("zeus", 1);

    logoutUser();
    
}

?>