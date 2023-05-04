<?php

//Skjekker om det har blitt sendt data fra formen i login pagen.
if (!isset($_POST['submit'])) {
    header("location: ../login.php");
    exit();
} else {

    $uid = $_POST["uid"];
    $pwd = $_POST["pwd"];

    require_once 'dbh.inc.php';
    require_once 'functions.inc.php';

    if(emptyInputLogin($uid, $pwd) !== false) {
        header("Location: ../login.php?error=emptylogininput");
        exit();
    }

    loginUser($uid, $pwd);
}
?>