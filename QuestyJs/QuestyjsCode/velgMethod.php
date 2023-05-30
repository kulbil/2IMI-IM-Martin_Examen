<?php
    include_once 'header.php';
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
        <div id="loginMethodBox">
            <div id="questyTitle"></div>
            <a href="login.php" id="loginButton"></a>
            <a href="signup.php" id="signupButton"></a>
        </div>  
        <a href="faq.php" id="faqLink">Faq</a>
    </div>

<?php
    include_once 'footer.php';
?>