<?php
if (isset($_POST['imie'])&& isset($_POST['wiek'])){
    $name = htmlspecialchars($_POST['imie']);
    $age = htmlspecialchars($_POST['wiek']);

    echo "<h2>Witaj, $name, twój wiek to $age</h2>";
    }else{
        echo "Nie podano danych";
}
?>