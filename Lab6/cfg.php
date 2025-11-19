<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'moja_strona';

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if ($mysqli->connect_error) {
    die('B³¹d po³¹czenia z baz¹: ' . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");
?>
