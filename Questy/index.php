<?php
    include_once 'header.php';
?>
    
<?php
    /*
        Skjekker om det er en session variabel som inneholder brukernavnet til brukeren.
        Hvis det ikke er det blir brukeren sendt tilbake til startsiden hvor man velger innloggingsmetode.
        Hvis det er en variabel så blir brukeren sendt til siden hvor spillet blir spilt.
    */ 
    
    if(isset($_SESSION["useruid"])) { 
        header("location: gamePage.php");
    } else {
        header("location: velgMethod.php");
    }
?>

<?php
    include_once 'footer.php';
?>

