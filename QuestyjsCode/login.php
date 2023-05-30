<?php
    include_once 'header.php';

    /* 
        Form som tar inn input dataen som brukeren skriver inn og sender den til login.inc.php
        hvor selve skjekkene vil bli gjort.
    */

    if (isset($_GET['error'])) {
        switch ($_GET['error']) {
            case "emptylogininput":
                echo '<script>alert("You need to fill out every box!")</script>';
                break;
            case "nouserfound":
                echo '<script>alert("No user found!")</script>';
                break;
        }
    }

    if(!isset($_SESSION)) { 
        session_start();
    }
    if(isset($_SESSION["userstatus"])) {
        if ($_SESSION["userstatus"] == "admin" || $_SESSION["userstatus"] == "banned") {
            $_SESSION["userstatus"] = "";
        }
    }
?>

    <div id="wrapper">
        <div id="loginBox">
            <form action="includes/login.inc.php" method="POST">
                <input type="text" name="uid" placeholder="Username">
                <input type="password" name="pwd" placeholder="Password">
                <button type="submit" name="submit"></button>
            </form>
        </div>
        <a href="velgMethod.php" id="backButton"></a>
        <a href="faq.php" id="faqLink">Faq</a>
    </div>

<?php
    include_once 'footer.php';
?>