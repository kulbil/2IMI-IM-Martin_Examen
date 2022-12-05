<?php
    include_once 'header.php';
?>
    
<?php
    if(isset($_SESSION["useruid"])) {
        header("location: gamePage.php");
    } else {
        header("location: velgMethod.php");
    }
?>

<?php
    include_once 'footer.php';
?>

