<?php
    include_once 'header.php';
    if(!isset($_SESSION)) { 
        session_start(); 
    }
    if($_SESSION["userstatus"] != "banned") {
        header("location: index.php");
        exit();
    }
?>

    BANNED XDDDD

<?php
    include_once 'footer.php';
?>