<?php
/***********************************************************************
 *  PLIK: cfg.php
 *  OPIS: Konfiguracja bazy danych i danych logowania.
 ***********************************************************************/

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'moja_strona174748';

$login = '174748';
$pass  = '174748';
$zalogowany = false;

/***********************
 * POŁĄCZENIE Z BAZĄ
 ***********************/
$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if ($mysqli->connect_error) {
    die('Błąd połączenia z bazą: ' . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");
$mail_to = 'kotekloltrzylimbus@gmail.com';
?>
