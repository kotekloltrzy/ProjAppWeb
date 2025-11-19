<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'moja_strona174748';
$login = '174748';
$pass = '174748';

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if ($mysqli->connect_error) {
    die('B³¹d po³¹czenia z baz¹: ' . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");
?>
