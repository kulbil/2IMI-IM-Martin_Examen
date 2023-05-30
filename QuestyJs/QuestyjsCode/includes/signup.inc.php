<?php

//Skjekker om det har blitt sendt data fra formen i signup pagen.
if (!isset($_POST['submit'])) {
    header("location: ../signup.php");
    exit();
} else {

    $name = $_POST["name"];
    $uid = $_POST["uid"];
    $pwd = $_POST["pwd"];
    $pwdRepeat = $_POST["pwdRepeat"];

    //inkluderer funksjoner og database filene til denne filen.
    require_once 'dbh.inc.php';
    require_once 'functions.inc.php';

    /* 
        Går gjennom en rekke bruker definerte funksjoner for å skjekke etter errorer i brukerens input.

        emptyInputSignup(): skjekker om en av inputene er tomme.
        pwdMatch(): skjekker om inputen i "password" og "repeat password" matcher.
        uidTakenCheck(): skjekker om brukernavnet allerede er tatt.
    */
    if (emptyInputSignup($name, $uid, $pwd, $pwdRepeat) !== false) {
        header("location: ../signup.php?error=emptysignupinput");
        exit();
    }

    if (pwdMatch($pwd, $pwdRepeat) !== false) {
        header("location: ../signup.php?error=pswnotmatch");
        exit();
    }
    if (uidTakenCheck($uid) !== false) {
        header("location: ../signup.php?error=uidtaken");
        exit();
    }
    
    //Lagrer brukeren i databasen og sender dem til login siden.
    createUser($name, $uid, $pwd);

    header("location: ../login.php");
}
?>
