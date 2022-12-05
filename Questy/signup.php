<?php
    include_once 'header.php';
?>

    <div id="wrapper">

        <div id="signupBox">
            <form action="includes/signup.inc.php" method="POST">
                <input type="text" name="name" placeholder="Real Name">
                <input type="text" name="uid" placeholder="Username">
                <input type="password" name="pwd" placeholder="Password">
                <input type="password" name="pwdRepeat" placeholder="Repeat Password">
                <button type="submit" name="submit">Sign up</button>
            </form>
        </div>

    </div>

<?php
    include_once 'footer.php';
?>

