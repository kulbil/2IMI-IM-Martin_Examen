<?php
    include_once 'header.php';

    /* 
        Form som tar inn input dataen som brukeren skriver inn og sender den til login.inc.php
        hvor selve skjekkene vil bli gjort.
    */

?>

    <div id="wrapper">

        <div id="loginBox">
            <form action="includes/login.inc.php" method="POST">
                <input type="text" name="uid" placeholder="Username">
                <input type="password" name="pwd" placeholder="Password">
                <button type="submit" name="submit">Log In</button>
            </form>
        </div>

    </div>

<?php
    include_once 'footer.php';
?>