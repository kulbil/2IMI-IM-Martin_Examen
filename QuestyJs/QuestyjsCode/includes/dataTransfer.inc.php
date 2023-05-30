<?php
include "functions.inc.php";
session_start();

$_SESSION["userplayerdata"] = $_GET['js2phpData'];

if ($_SESSION["userhighscore"] < $_GET['js2phpHs']) {
    $_SESSION["userhighscore"] = $_GET['js2phpHs'];
}

updDb();
echo $_SESSION["userplayerdata"];
echo "<br>";
echo $_SESSION["userhighscore"];

header("location: ../gameoverPage.php");