<?php
include "functions.inc.php";
session_start();

//henter hvilken 'option' brukeren valgte
$selectedOption = $_GET['option'];

switch ($selectedOption) {
    case "submitFight":
        fightSubmit();
    break;
    case "submitItems":
        itemsSubmit();
    break;
    case "submitRun":
        runSubmit();
    break;
    case "submitQuit":
        logoutUser(); 
    break;

    //Items
    case "submitItem1":
        healingSubmit();
    break;
    case "submitItem2":
        strengthSubmit();
    break;
    case "submitItem3":
        zeusSubmit();
    break;
}

$_SESSION['select'] = $selectedOption;
header("location: ../index.php");
?>

