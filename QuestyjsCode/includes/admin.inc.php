<?php

include "dbh.inc.php";
if(!isset($_SESSION)) { 
    session_start(); 
}


if(isset($_POST['searchedInput'])) {
    $_SESSION['searchedInput'] = $_POST['searchedInput'];
}

if(isset($_POST['buttonPressed'])) {
    $buttonPressed = $_POST['buttonPressed'];
    $selectedRowId = $_POST['selectedRowId'];
    if(isset($_POST['selectedRowRank'])) {
        $selectedRowRank = $_POST['selectedRowRank'];
    }
}


if(isset($buttonPressed)) {
    if($buttonPressed == "ban") {
        if ($selectedRowRank == "user") {
            $sql = "UPDATE users SET rank='banned' WHERE id='".$selectedRowId."';";
        } elseif ($selectedRowRank == "banned") {
            $sql = "UPDATE users SET rank='user' WHERE id='".$selectedRowId."';";
        }
    } else if($buttonPressed == "delete") {
        $sql = "DELETE FROM users WHERE id='".$selectedRowId."';";
    }
    $result = $conn->query($sql);
}


if(isset($_SESSION['searchedInput'])) {
    $searchedInput = $_SESSION['searchedInput'];
}

$sql = "SELECT * FROM users WHERE NOT rank='admin' AND uid like '%$searchedInput%' OR NOT rank='admin' AND name like '%$searchedInput%'";
$result = $conn->query($sql);
echo "<tr>
<th>Id</th>
<th>Name</th>
<th>Username</th>
<th>Highscore</th>
<th>Status</th>
<th>Action</th>
</tr>";
while ($row = $result -> fetch_assoc()) {
    echo "<tr>
        <td>".$row['id']."</td>
        <td>".$row['name']."</td>
        <td>".$row['uid']."</td>
        <td>".$row['highscore']."</td>
        <td>".$row['rank']."</td>
        <td class='buttonColumn'><button class='banBtn ".$row['rank']."' id=".$row['id']." value=".$row['rank']."></button></td>
        <td class='buttonColumn'><button class='deleteBtn' id=".$row['id']."></button></td>

        </tr>";
};