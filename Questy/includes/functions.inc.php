<?php
//inkluderer alle nødvendige filer.
include "dbh.inc.php";

include "player.inc.php";
include "monster.inc.php";
include "weapons.inc.php";
include "room.inc.php";
include "assetList.inc.php";


//skjekker om alle inputene i signup pagen er fylt ut.
function emptyInputSignup($name, $uid, $pwd, $pwdRepeat) {
    if (empty($name) || empty($uid) || empty($pwd) || empty($pwdRepeat)) {
        return true;
    } else {
        return false;
    }
}


//Skjekker om begge passord inputene matcher
function pwdMatch($pwd, $pwdRepeat) {
    if($pwd !== $pwdRepeat) {
        return true;
    } else {
        return false;
    }
}

//Skjekker om brukernavnet som er sendt inn er lik en i databasen
function uidTakenCheck($uid) {

    //Sql kode som velger alt i en row hvor brukeren matcher parameteret
    //dette er en prepared statement
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


        //lager en player object 
        createPlayer();
        createRoom();

        header("location: ../index.php");
        exit();
    }
}

//logger ut brukeren
function logoutUser() {

    updDb();

    session_start();
    session_unset();
    session_destroy();

    header("location: ../index.php");
}


//lager en player med dataen henta fra databasen
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

//funksjon som unserializer session variabled med player objectet
//Sessions kan ikke lagre objecter i seg selv så man må serialize det
function unSerPlayer() {
    return(unserialize($_SESSION['playerCharacter']));
}

//oppdaterer databasen med nåverende statistikk med bruk av prepared statements
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

//oppdaterer spilleren sine stats
//$param velger hvilken stat man skal oppdatere
//$value bestemmer hva du skal endre staten till
function updatePlayer($param, $value) {
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    }

    //lager variabler for all relevant info
    $userPlayerData = unserialize($_SESSION["userplayerdata"]);
    $currentPlayerStats = array($userPlayerData[0], $userPlayerData[1], $userPlayerData[2], $userPlayerData[3], $userPlayerData[4]);
    $playerCharacter = unSerPlayer();

    //Switch statement som finner ut av hva $param er
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
    
    //Overriter den gamle dataen med den nye
    $_SESSION['userplayerdata'] = serialize($currentPlayerStats);
    $_SESSION['playerCharacter'] = serialize($playerCharacter);
}

//lager et random monster fra "monsterList" globalen
function createRanMonster() {
    $createdMonster = $GLOBALS['monsterList'][rand(0, 2)];
    return($createdMonster);
}

//lager et random weapon fra "weaponList" globalen
function createRanWeapon() {
    $createdWeapon = $GLOBALS['weaponList'][rand(0, 2)];
    return($createdWeapon);
}

//lager et random rom
function createRoom() {
    $currentRoom = new room((unSerPlayer()->room + 1), createRanMonster(), createRanWeapon());
    $_SESSION['currentRoom'] = serialize($currentRoom);
}

//utfører "fight" handlingen som tar damage på monsteret
function fightSubmit() {
    $room = unserialize($_SESSION['currentRoom']);
    $player = unSerPlayer();

    $room->monster->health -= $player->weapon->damage;
    $_SESSION['infotext'] = $_SESSION['infotext']."<br>"."Player did ".$player->weapon->damage." damage";

    //skjekker om monsteret er død
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
    //ikke implementa enda
}

function itemsSubmit() {

}

//utfører "healing" handlingen som tar opp en healing potion og healer brukeren 50 liv
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

//utfører "zeus" handlingen som tar opp en zeus potion og dreper monsteret
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

//kjører monsteret sin tur imot spilleren
function monsterTurn() {
    $monster = unserialize($_SESSION['currentRoom'])->monster;
    $player = unSerPlayer();
    
    updatePlayer("health", ($player->health -= $monster->strength));
    $_SESSION['infotext'] = $_SESSION['infotext']."<br>".$monster->name." did ".$monster->strength." damage";

    //skjekker om spilleren dør etter monsteret har angrepet
    if($player->health <= 0) {
        gameOver();
    }
}

//lager et nytt rom etter å ha slettet det forrige
function newroom() {
    unset($_SESSION['currentRoom']);
    createRoom();
}

//resetter alt av spiller stats og lagrer det i databasen.
//lagrer også highscore i databasen
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