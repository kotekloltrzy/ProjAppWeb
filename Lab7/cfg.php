<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'moja_strona174748';
$login = '174748';
$pass = '174748';
$zalogowany = false;

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if ($mysqli->connect_error) {
    die('Blad polaczenia z baza: ' . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");
?>
