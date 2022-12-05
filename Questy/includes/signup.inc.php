<?php

if (!isset($_POST['submit'])) {
    header("location: ../signup.php");
    exit();
} else {

    $name = $_POST["name"];
    $uid = $_POST["uid"];
    $pwd = $_POST["pwd"];
    $pwdRepeat = $_POST["pwdRepeat"];

    require_once 'dbh.inc.php';
    require_once 'functions.inc.php';

    if (emptyInputSignup($name, $uid, $pwd, $pwdRepeat) !== false) {
        header("location: ../signup.php?error=emptysignupinput");
        exit();
    };
    if (pwdMatch($pwd, $pwdRepeat) !== false) {
        header("location: ../signup.php?error=pswnotmatch");
        exit();
    }
    if (uidTakenCheck($uid) !== false) {
        header("location: ../signup.php?error=uidtaken");
        exit();
    }
    
    createUser($name, $uid, $pwd);

    header("location: ../login.php");
}
?>
