<?php
include "functions.inc.php";

if(!isset($_SESSION)) { 
    session_start(); 
}

$_SESSION["userplayerdata"] = $_GET['quitInput'];


if(isset($_GET['quitInput'])) {
    logoutUser();
} else {
    header("location: ../index.php");
}


