<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

if ($_GET['idp'] == '') {
    $strona = 'html/glowna.html';
} elseif ($_GET['idp'] == 'lokacja') {
    $strona = 'html/lokacja.html';
} elseif ($_GET['idp'] == 'historia') {
    $strona = 'html/historia.html';
} elseif ($_GET['idp'] == 'ciekawostki') {
    $strona = 'html/ciekawostki.html';
} elseif ($_GET['idp'] == 'faunaFlora') {
    $strona = 'html/faunaFlora.html';
} elseif ($_GET['idp'] == 'filmy') {
    $strona = 'html/filmy.html';
} else {
    $strona = 'html/glowna.html';
}


if (!file_exists($strona)) {
    $strona = 'html/glowna.html';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Projekt 1">
    <meta name="keywords" content="HTML5, CSS3, JavaScript">
    <meta name="author" content="Michał Broda">
    <link rel="stylesheet" href="css/style.css">
    <img class="flaga" src="img/flaga.png">
    <title>Najpiękniejsze miesjce w świecie</title>
    <header class="header header1"> Najpiękniejsze miesjce w świecie </header>
    <div class="divbutton">
        <a href="index.php?idp=glowna"><button class="button button1"> Strona główna</button></a>
        <a href="index.php?idp=lokacja"><button class="button button2"> Lokacja</button></a>
        <a href="index.php?idp=historia"><button class="button button3"> Historia</button></a>
        <a href="index.php?idp=ciekawostki"><button class="button button4"> Ciekawostki</button></a>
        <a href="index.php?idp=faunaFlora"><button class="button button5"> Fauna i Flora</button></a>
        <a href="index.php?idp=filmy"><button class="button button14"> Filmy</button></a>
    </div>
</head>

<body onload="startClock()">

    <?php include($strona); ?>

    <script src="js/kolorujtlo.js" type="text/JavaScript"></script>
    <script src="js/timedate.js" type="text/JavaScript"></script>
    
</body>
</html>
