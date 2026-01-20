<?php
session_start();
/***********************************************************************
 *  PLIK: index.php
 *  OPIS: Główna strona ładująca odpowiednie podstrony HTML oraz te
 *        znajdujące się w bazie danych (funkcja PokazPodstrone()).
 ***********************************************************************/

include("cfg.php");

// Ukrycie nieistotnych warningów
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

/***************
 * WALIDACJA GET
 ***************/
$idp = filter_input(INPUT_GET, 'idp', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

/*********************************************
 * WYBÓR PLIKÓW HTML (podstrony statyczne)
 *********************************************/
switch ($idp) {
    case '':
    case null:
        $strona = 'html/glowna.html';
        break;
    case 'lokacja':
        $strona = 'html/lokacja.html';
        break;
    case 'historia':
        $strona = 'html/historia.html';
        break;
    case 'ciekawostki':
        $strona = 'html/ciekawostki.html';
        break;
    case 'faunaFlora':
        $strona = 'html/faunaFlora.html';
        break;
    case 'filmy':
        $strona = 'html/filmy.html';
        break;
    default:
        $strona = 'html/glowna.html';
        break;
}

// Jeżeli plik nie istnieje – powrót do strony głównej
if (!file_exists($strona)) {
    $strona = 'html/glowna.html';
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Projekt 1">
    <meta name="keywords" content="HTML5, CSS3, JavaScript">
    <meta name="author" content="Michał Broda">

    <link rel="stylesheet" href="css/style.css">
    <img class="flaga" src="img/flaga.png">

    <title>Najpiękniejsze miejsce na świecie</title>

    <header class="header header1">
        Najpiękniejsze miejsce na świecie
    </header>

    <div class="divbutton">
        <a href="index.php?id=1"><button class="button button1"> Strona główna </button></a>
        <a href="index.php?id=2"><button class="button button2"> Lokacja </button></a>
        <a href="index.php?id=3"><button class="button button3"> Historia </button></a>
        <a href="index.php?id=4"><button class="button button4"> Ciekawostki </button></a>
        <a href="index.php?id=5"><button class="button button5"> Fauna i Flora </button></a>
        <a href="index.php?id=6"><button class="button button14"> Filmy </button></a>
        <a href="index.php?id=7"><button class="button button15"> Sklep </button></a>
    </div>
</head>

<body onload="startClock()">

    <!-- Wczytanie strony statycznej -->
    <?php include($strona); ?>

    <!-- Wczytanie strony dynamicznej z bazy danych -->
    <?php
        include("showpage.php");

        // Walidacja GET['id']
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) ?? 1;

        echo PokazPodstrone($id);
    ?>

    <!-- Skrypty JS -->
    <script src="js/kolorujtlo.js"></script>
    <script src="js/timedate.js"></script>

    <!-- Przejście do formularza kontaktowego -->
    <div class="formularz">
        <a href="contact.php">
            <button>Wysyłanie wiadomości e-mail</button>
        </a>
    </div>

</body>
</html>
