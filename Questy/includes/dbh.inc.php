<?php
//egen side for håndtering av databasetilkobling

$serverName = "localhost";
$dbUsername = "root";
$password = "";
$dbName = "questydb";

$conn = mysqli_connect($serverName, $dbUsername, $password, $dbName);
?>