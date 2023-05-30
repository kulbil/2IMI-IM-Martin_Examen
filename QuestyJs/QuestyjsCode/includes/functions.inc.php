<?php
//inkluderer alle nødvendige filer.
include "dbh.inc.php";


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

    $playerDataSer = json_encode($playerData);


    $sql = "INSERT INTO users (name, uid, userpwd, playerdata, highscore, joindate, rank) VALUES (?, ?, ?, ?, 0, now(), 'user');";
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
        
        if(!isset($_SESSION)) { 
            session_start(); 
        }
        
        if($uidCheck['rank'] == "user") {
            $_SESSION["userid"] = $uidCheck['id'];
            $_SESSION["useruid"] = $uidCheck['uid'];
            $_SESSION["userplayerdata"] = $uidCheck['playerdata'];
            $_SESSION["userhighscore"] = $uidCheck['highscore'];
            $_SESSION["userstatus"] = "user";
            //Player data
            //[Room Number, Health, Weapon, Healing potion, Zeus Potion, High Score]
    
            header("location: ../index.php");
            exit();
        } else if ($uidCheck['rank'] == "banned") {
            $_SESSION["userstatus"] = "banned";
            header("location: ../banned.php");
        } else if ($uidCheck['rank'] == "admin") {
            $_SESSION["userstatus"] = "admin";
            header("location: ../admin.php");
        }

    }
}

//logger ut brukeren
function logoutUser() {
    updDb();

    if(!isset($_SESSION)) { 
        session_start(); 
    }
    session_destroy();
    header("location: ../index.php");
}

//oppdaterer databasen med nåverende statistikk med bruk av prepared statements
function updDb() {
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
    
    $userPlayerData = $_SESSION["userplayerdata"];
    $playerId = $_SESSION["userid"];
    $highscore = $_SESSION["userhighscore"];

    $sql = "UPDATE users SET playerdata=?, highscore=? where id=?";
    $stmt = $GLOBALS['conn']->prepare($sql);

    $stmt->bind_param("sii", $userPlayerData, $highscore, $playerId);
    $stmt->execute();
}

?>