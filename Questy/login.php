<?php
    include_once 'header.php';
?>

    <div id="wrapper">

        <div id="loginBox">
            <form action="includes/login.inc.php" method="POST">
                <input type="text" name="uid" placeholder="Username">
                <input type="password" name="pwd" placeholder="Password">
                <button type="submit" name="submit">Sign up</button>
            </form>
        </div>

    </div>

<?php
    include_once 'footer.php';
?>