<?php
if (!isset($_SESSION['count'])) {
    $_SESSION['count'] = 0;
}

/* ===== FUNKCJE KOSZYKA ===== */

function addToCard($id_prod, $ile_sztuk) {
    $_SESSION['count']++;
    $nr = $_SESSION['count'];

    $_SESSION["{$nr}_0"] = $nr;
    $_SESSION["{$nr}_1"] = $id_prod;
    $_SESSION["{$nr}_2"] = $ile_sztuk;
    $_SESSION["{$nr}_3"] = time();
}

function removeFromCard($nr) {
    unset($_SESSION["{$nr}_0"]);
    unset($_SESSION["{$nr}_1"]);
    unset($_SESSION["{$nr}_2"]);
    unset($_SESSION["{$nr}_3"]);
}

// Funckja czyszcząca koszyk
function clearCard() {
    for ($i = 1; $i <= $_SESSION['count']; $i++) {
        removeFromCard($i);
    }
    $_SESSION['count'] = 0;
}

function editCard($nr, $nowa_ilosc) {
    if (isset($_SESSION["{$nr}_2"])) {
        $_SESSION["{$nr}_2"] = $nowa_ilosc;
    }
}

function showCard($mysqli) {
    // Sprawdzanie koszyka
    $pusty = true;
    for ($i = 1; $i <= $_SESSION['count']; $i++) {
        if (isset($_SESSION["{$i}_1"])) {
            $pusty = false;
            break;
        }
    }

    if ($pusty) {
        echo "<h3>Koszyk jest pusty</h3>";
        return;
    }

    $suma = 0;
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>
    <tr style='background: #eee;'><th>Produkt</th><th>Cena brutto</th><th>Ilość</th><th>Wartość</th><th>Akcja</th></tr>";

    for ($i = 1; $i <= $_SESSION['count']; $i++) {
        if (!isset($_SESSION["{$i}_1"])) continue;

        $id = $_SESSION["{$i}_1"];
        $ilosc = $_SESSION["{$i}_2"];

        $res = $mysqli->query("SELECT tytul, cena_netto, podatek_vat FROM produkty WHERE id=$id");
        if (!$res || $res->num_rows == 0) continue;
        $p = $res->fetch_assoc();

        $cena_brutto = $p['cena_netto'] + ($p['cena_netto'] * $p['podatek_vat'] / 100);
        $wartosc = $cena_brutto * $ilosc;
        $suma += $wartosc;

        echo "<tr>
            <td>{$p['tytul']}</td>
            <td>".number_format($cena_brutto,2)." zł</td>
            <td>
                <form method='post' style='display:inline;'>
                    <input type='number' name='ilosc' value='$ilosc' min='1' style='width:50px;'>
                    <input type='hidden' name='nr' value='$i'>
                    <button name='edit'>Zmień</button>
                </form>
            </td>
            <td>".number_format($wartosc,2)." zł</td>
            <td><a href='index.php?id=8&del=$i' style='color:red;'>Usuń</a></td>
        </tr>";
    }

    echo "<tr>
        <td colspan='3' align='right'><b>Suma do zapłaty:</b></td>
        <td colspan='2'><b>".number_format($suma,2)." zł</b></td>
    </tr>";
    echo "</table>";

    // Przycisk KUP pod tabelą
    echo "
    <div style='margin-top: 20px; text-align: right;'>
        <form method='post'>
            <button name='buy' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>
                Złóż zamówienie (Kupuję)
            </button>
        </form>
    </div>";
}

/* ===== OBSŁUGA AKCJI ===== */

$komunikat = "";

if (isset($_GET['add'])) {
    $id_do_dodania = isset($_GET['id_prod']) ? intval($_GET['id_prod']) : 0;
    $ile_sztuk = isset($_GET['ilosc']) ? intval($_GET['ilosc']) : 1;
    if ($id_do_dodania > 0) {
        addToCard($id_do_dodania, $ile_sztuk);
    }
}

if (isset($_GET['del'])) {
    removeFromCard($_GET['del']);
}

if (isset($_POST['edit'])) {
    editCard($_POST['nr'], $_POST['ilosc']);
}

// OBSŁUGA PRZYCISKU KUP
if (isset($_POST['buy'])) {
    clearCard();
    $komunikat = "<h2 style='color: green;'>Zakup udany! Dziękujemy za zamówienie.</h2>";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Koszyk</title>
</head>
<body>

<div style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: Arial;">
    <h2>Twój koszyk</h2>

    <?php 
    if ($komunikat != "") {
        echo $komunikat;
    } else {
        showCard($mysqli); 
    }
    ?>

    <br><br>
    <a href="index.php?id=7" style="text-decoration: none; color: #007bff;">← Wróć do sklepu</a>
</div>

</body>
</html>