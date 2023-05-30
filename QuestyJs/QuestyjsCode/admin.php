<?php
    include 'header.php';
    include "includes/dbh.inc.php";

    if(!isset($_SESSION)) { 
        session_start(); 
    }
    if($_SESSION["userstatus"] != "admin") {
        header("location: index.php");
        exit();
    }
    $_SESSION['searchedInput'] = "";

?>

    <div id="adminContainer">
        <div id="adminBackground">
            <div id="adminListContainer">
                <input type="text" id="userSearch" placeholder="Search for username">
                <div id="userList">
                    <table id="dbTable">
                        <?php
                            echo "<tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Highscore</th>
                                <th>Status</th>
                                <th>Action</th>
                                </tr>";
                            $sql = "SELECT * FROM users WHERE NOT rank='admin'";
                            $result = $conn->query($sql);
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
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <a href="velgMethod.php" id="backButton"></a>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script>
        $("#dbTable").on("click", ".banBtn", function(){
            console.log(this.id);
            $.post("includes/admin.inc.php", {
                selectedRowId: this.id,
                selectedRowRank: this.value,
                buttonPressed: "ban"
            }, function(data) {
                $("#dbTable").html(data);
            });
        });

        $("#dbTable").on("click", ".deleteBtn", function(){
            console.log(this.id);
            $.post("includes/admin.inc.php", {
                selectedRowId: this.id,
                buttonPressed: "delete"
            }, function(data) {
                $("#dbTable").html(data);
            });
        });

        $("#userSearch").keyup(function(){
            $.post("includes/admin.inc.php", {
                searchedInput: this.value
            }, function(data) {
                $("#dbTable").html(data);
            });
        });

</script>
</body>
</html>

