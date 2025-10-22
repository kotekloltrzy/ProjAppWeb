<?php
session_start();
$nr_indeksu = '174748';
$nrGrupy = 'ISI2';
$a = 10;
$b = 5;
echo 'Michał Broda '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';
echo 'Zastosowanie metody include() <br />';
include 'Tekst.txt';
echo '<br /><br />Zastosowanie warunków if, else, elseif<br />';
if ($a == $b){
    echo 'Miłego dnia!<br />';
} elseif ($a < $b) {
    echo 'Dobranoc! <be />';
} else {
    echo 'Miłego wieczoru! <br />';
}
echo '<br />Zastosowanie warunku switch<br />';
switch ($nrGrupy) {
  case "ISI1":
    echo 'Jesteś w grupie 1<br />';
    break;
  case "ISI2":
    echo 'Jesteś w grupie 2<br />';
    break;
  case "ISI3":
    echo 'Jesteś w grupie 3<br />';
    break;
  default:
    echo 'Nie jesteś w żadnej grupie<br />';
}
echo '<br />Zastosowanie pętli while() i for()<br />';
$i = 0;
while ($i < 6) {
    echo $i.' ';
    $i++;
}
for ($x = 0; $x <= $a; $x=$x+2){
    echo '<br />Numer x to:'.$x;
}
echo '<br /><br /> Zastosowanie typów zmiennych $_GET';

if(isset($_GET['name'])&& isset($_GET['age'])){
    $name = htmlspecialchars($_GET['name']);
    $age = htmlspecialchars($_GET['age']);

    echo'<p>Witaj,'.$name.'!</p>';
    echo'<p>Masz,'.$age.'lat</p>';
    } else {
    echo'<p>Nie przesłałeś danych</p>';
}
echo '<a href="html/formularz.html"> Zastosowanie typów zmiennych $_POST</a>';

echo '<br /><br />Zastosowanie typów zmiennych $_SESSION<br />';
$_SESSION['Login'] = 'Nek';
if (isset($_SESSION['Login'])){
    echo 'Witaj, '.$_SESSION['Login'].'!';
    } else {
        echo 'Nie masz ustawionego logina sesji';
}
?>