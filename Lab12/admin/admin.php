<?php
/***********************************************************************
 *  PLIK: admin.php
 *  OPIS: Panel CMS – zarządzanie podstronami oraz kategoriami produktów
 ***********************************************************************/

session_start();
require_once("../cfg.php");

/***********************************************************************
 *  LOGOWANIE
 ***********************************************************************/
if (isset($_POST['x1_submit'])) {
    if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
        $_SESSION['zalogowany'] = true;
    } else {
        echo FormularzLogowania("Błędny login lub hasło");
        exit();
    }
}

if (!isset($_SESSION['zalogowany'])) {
    echo FormularzLogowania();
    exit();
}

/***********************************************************************
 *  FORMULARZ LOGOWANIA
 ***********************************************************************/
function FormularzLogowania($komunikat = "")
{
    return '
    <h1>Panel CMS</h1>
    <form method="post">
        Email:<br>
        <input type="text" name="login_email"><br><br>
        Hasło:<br>
        <input type="password" name="login_pass"><br><br>
        <input type="submit" name="x1_submit" value="Zaloguj">
        <div style="color:red;">' . $komunikat . '</div>
    </form>';
}

/***********************************************************************
 *  PODSTRONY – LISTA
 ***********************************************************************/
function ListaPodstron()
{
    global $mysqli;

    $result = $mysqli->query("SELECT id, page_title FROM page_list");

    echo "<h3>Lista podstron</h3><table border='1'>";
    echo "<tr><th>ID</th><th>Tytuł</th><th>Akcja</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['page_title']}</td>
            <td>
                <a href='admin.php?edit={$row['id']}'>Edytuj</a> |
                <a href='admin.php?delete={$row['id']}'>Usuń</a>
            </td>
        </tr>";
    }

    echo "</table>";
}

/***********************************************************************
 *  Sprawdzanie dostępności produktów
 ***********************************************************************/

function CzyDostepny($p) {
    if (
        $p['status_dostepnosci'] == 1 &&
        $p['ilosc_magazynowa'] > 0 &&
        strtotime($p['data_wygasniecia']) > time()
    ) {
        return "Dostępny";
    } else {
        return "Niedostępny";
    }
}

/***********************************************************************
 *  Wyświetlanie listy kategorii
 ***********************************************************************/

function SelectKategorie($selected = 0) {
    global $mysqli;
    $res = $mysqli->query("SELECT id, nazwa, matka FROM kategorie ORDER BY matka, nazwa");

    $out = "<select name='kategoria'>";
    $out .= "<option value='0'>-- brak --</option>";

    while ($r = $res->fetch_assoc()) {
        $prefix = $r['matka'] != 0 ? "&nbsp;&nbsp;↳ " : "";
        $sel = ($r['id'] == $selected) ? "selected" : "";
        $out .= "<option value='{$r['id']}' $sel>$prefix{$r['nazwa']}</option>";
    }

    $out .= "</select>";
    return $out;
}

/***********************************************************************
 *  Pokazywanie produktów
 ***********************************************************************/

function PokazProdukty() {
    global $mysqli;

    $res = $mysqli->query("
        SELECT p.*, k.nazwa AS kat_nazwa
        FROM produkty p
        LEFT JOIN kategorie k ON p.kategoria = k.id
        ORDER BY p.id DESC
    ");

    echo "<table border='1'>
    <tr>
        <th>ID</th>
        <th>Tytuł</th>
        <th>Zdjęcie</th>
        <th>Kategoria</th>
        <th>Cena</th>
        <th>Ilość</th>
        <th>Status</th>
        <th>Data dodania</th>
        <th>Data edycji</th>
        <th>Data wygaśnięcia</th>
        <th>Akcja</th>
    </tr>";

    while ($r = $res->fetch_assoc()) {
        $status = CzyDostepny($r);
        $kat = $r['kat_nazwa'] ?? "-";

        $img = $r['zdjecie']
            ? "<img src='{$r['zdjecie']}' style='width:80px;height:80px;object-fit:cover;'>"
            : "-";

        echo "<tr>
            <td>{$r['id']}</td>
            <td>{$r['tytul']}</td>
            <td>$img</td>
            <td>$kat</td>
            <td>{$r['cena_netto']} zł</td>
            <td>{$r['ilosc_magazynowa']}</td>
            <td>$status</td>
            <td>{$r['data_utworzenia']}</td>
            <td>{$r['data_modyfikacji']}</td>
            <td>{$r['data_wygasniecia']}</td>
            <td>
                <a href='admin.php?prod_edit={$r['id']}'>Edytuj</a> |
                <a href='admin.php?prod_del={$r['id']}'>Usuń</a>
            </td>
        </tr>";
    }

    echo "</table>";
}

/***********************************************************************
 *  Dodawanie produktu
 ***********************************************************************/

function DodajProdukt() {
    global $mysqli;

    if (isset($_POST['prod_dodaj'])) {
        $stmt = $mysqli->prepare("
            INSERT INTO produkty
            (tytul, opis, data_utworzenia, data_modyfikacji, data_wygasniecia,
             cena_netto, podatek_vat, ilosc_magazynowa, status_dostepnosci,
             kategoria, gabaryt_produktu, zdjecie)
            VALUES (?, ?, CURDATE(), CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $nullBlob = null;

         $stmt->bind_param(
            "sssdiiiiis",
            $_POST['tytul'],
            $_POST['opis'],
            $_POST['data_wyg'],
            $_POST['cena'],
            $_POST['vat'],
            $_POST['ilosc'],
            $_POST['status'],
            $_POST['kategoria'],
            $_POST['gabaryt'],
            $_POST['zdjecie']
        );

        $stmt->execute();
        echo "<div style='color:green'>Dodano produkt</div>";
    }

    echo "
    <form method='post'>
        Tytuł:<br><input name='tytul'><br>
        Opis:<br><textarea name='opis'></textarea><br>
        Data wygaśnięcia:<br><input type='date' name='data_wyg'><br>
        Cena netto:<br><input name='cena'><br>
        VAT:<br><input name='vat'><br>
        Ilość:<br><input name='ilosc'><br>
        Status (1/0):<br><input name='status'><br>
        Kategoria:<br>".SelectKategorie()."<br>
        Gabaryt:<br><input name='gabaryt'><br>
        Zdjęcie (URL):<br><input name='zdjecie'><br><br>
        <button name='prod_dodaj'>Dodaj</button>
    </form>";
}

/***********************************************************************
 *  Edytowanie produktu
 ***********************************************************************/

function EdytujProdukt($id) {
    global $mysqli;
    $id = intval($id);

    if (isset($_POST['prod_save'])) {
        $stmt = $mysqli->prepare("
            UPDATE produkty SET
            tytul=?, opis=?, data_modyfikacji=CURDATE(), data_wygasniecia=?,
            cena_netto=?, podatek_vat=?, ilosc_magazynowa=?, status_dostepnosci=?,
            kategoria=?, gabaryt_produktu=?, zdjecie=?
            WHERE id=?
        ");

        $stmt->bind_param(
             "sssiiiiissi",
             $_POST['tytul'], $_POST['opis'], $_POST['data_wyg'],
             $_POST['cena'], $_POST['vat'], $_POST['ilosc'],
             $_POST['status'], $_POST['kategoria'], $_POST['gabaryt'],
             $_POST['zdjecie'], $id
        );

        $stmt->execute();
        echo "<div style='color:green'>Zapisano</div>";
    }

    $r = $mysqli->query("SELECT * FROM produkty WHERE id=$id")->fetch_assoc();

    echo "
    <form method='post'>
        Tytuł:<input name='tytul' value='{$r['tytul']}'><br>
        Opis:<textarea name='opis'>{$r['opis']}</textarea><br>
        Data wyg:<input type='date' name='data_wyg' value='{$r['data_wygasniecia']}'><br>
        Cena:<input name='cena' value='{$r['cena_netto']}'><br>
        VAT:<input name='vat' value='{$r['podatek_vat']}'><br>
        Ilość:<input name='ilosc' value='{$r['ilosc_magazynowa']}'><br>
        Status:<input name='status' value='{$r['status_dostepnosci']}'><br>
        Kategoria:<br>".SelectKategorie($r['kategoria'])."<br>
        Gabaryt:<input name='gabaryt' value='{$r['gabaryt_produktu']}'><br>
        Zdjęcie (URL):<input name='zdjecie' value='{$r['zdjecie']}'><br>
        <button name='prod_save'>Zapisz</button>
    </form>";
}

/***********************************************************************
 *  Usuwanie produktu
 ***********************************************************************/

function UsunProdukt($id) {
    global $mysqli;
    $id = intval($id);
    $mysqli->query("DELETE FROM produkty WHERE id=$id LIMIT 1");
    echo "<div style='color:red'>Usunięto produkt</div>";
}


/***********************************************************************
 *  PODSTRONY – DODAWANIE
 ***********************************************************************/
function DodajNowaPodstrone()
{
    global $mysqli;

    if (isset($_POST['dodaj'])) {
        $title   = $_POST['title'];
        $content = $_POST['content'];
        $status  = isset($_POST['status']) ? 1 : 0;

        $stmt = $mysqli->prepare(
            "INSERT INTO page_list (page_title, page_content, status)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("ssi", $title, $content, $status);
        $stmt->execute();

        echo "<div style='color:green;'>Dodano podstronę</div>";
    }

    echo '
    <h3>Dodaj podstronę</h3>
    <form method="post">
        Tytuł:<br><input type="text" name="title"><br><br>
        Treść:<br><textarea name="content" rows="6" cols="60"></textarea><br><br>
        Aktywna <input type="checkbox" name="status"><br><br>
        <input type="submit" name="dodaj" value="Dodaj">
    </form>';
}

/***********************************************************************
 *  PODSTRONY – EDYCJA
 ***********************************************************************/
function EdytujPodstrone($id)
{
    global $mysqli;
    $id = intval($id);

    if (isset($_POST['save'])) {
        $title   = $_POST['title'];
        $content = $_POST['content'];
        $status  = isset($_POST['status']) ? 1 : 0;

        $stmt = $mysqli->prepare(
            "UPDATE page_list
             SET page_title=?, page_content=?, status=?
             WHERE id=? LIMIT 1"
        );
        $stmt->bind_param("ssii", $title, $content, $status, $id);
        $stmt->execute();

        echo "<div style='color:green;'>Zapisano zmiany</div>";
    }

    $stmt = $mysqli->prepare("SELECT * FROM page_list WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    echo "
    <h3>Edycja podstrony</h3>
    <form method='post'>
        Tytuł:<br><input type='text' name='title' value='{$row['page_title']}'><br><br>
        Treść:<br><textarea name='content' rows='6' cols='60'>{$row['page_content']}</textarea><br><br>
        Aktywna <input type='checkbox' name='status' ".($row['status'] ? "checked" : "")."><br><br>
        <input type='submit' name='save' value='Zapisz'>
    </form>";
}

/***********************************************************************
 *  PODSTRONY – USUWANIE
 ***********************************************************************/
function UsunPodstrone($id)
{
    global $mysqli;
    $id = intval($id);

    $stmt = $mysqli->prepare(
        "DELETE FROM page_list WHERE id=? LIMIT 1"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<div style='color:red;'>Usunięto podstronę</div>";
}

/***********************************************************************
 *  KATEGORIE – DODAWANIE
 ***********************************************************************/
function DodajKategorie()
{
    global $mysqli;

    if (isset($_POST['kat_dodaj'])) {
        $nazwa = trim($_POST['nazwa']);
        $matka = intval($_POST['matka']);

        $stmt = $mysqli->prepare(
            "INSERT INTO kategorie (nazwa, matka) VALUES (?, ?)"
        );
        $stmt->bind_param("si", $nazwa, $matka);
        $stmt->execute();

        echo "<div style='color:green;'>Dodano kategorię</div>";
    }

    echo "
    <h3>Dodaj kategorię</h3>
    <form method='post'>
        Nazwa:<br>
        <input type='text' name='nazwa' required><br><br>

        Kategoria nadrzędna:<br>
        <select name='matka'>
            <option value='0'>-- główna --</option>";

    $result = $mysqli->query("SELECT id, nazwa FROM kategorie WHERE matka = 0");
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['nazwa']}</option>";
    }

    echo "
        </select><br><br>
        <input type='submit' name='kat_dodaj' value='Dodaj kategorię'>
    </form>";
}

/***********************************************************************
 *  KATEGORIE – WYŚWIETLANIE (DRZEWO)
 ***********************************************************************/
function PokazKategorie()
{
    global $mysqli;

    echo "<h3>Drzewo kategorii</h3><ul>";

    $matki = $mysqli->query(
        "SELECT id, nazwa FROM kategorie WHERE matka = 0"
    );

    while ($m = $matki->fetch_assoc()) {

        echo "<li>
            <strong>{$m['nazwa']}</strong>
            <a href='admin.php?kat_usun={$m['id']}'>[usuń]</a>";

        $stmt = $mysqli->prepare(
            "SELECT id, nazwa FROM kategorie WHERE matka = ?"
        );
        $stmt->bind_param("i", $m['id']);
        $stmt->execute();
        $dzieci = $stmt->get_result();

        if ($dzieci->num_rows > 0) {
            echo "<ul>";
            while ($d = $dzieci->fetch_assoc()) {
                echo "<li>
                    {$d['nazwa']}
                    <a href='admin.php?kat_usun={$d['id']}'>[usuń]</a>
                </li>";
            }
            echo "</ul>";
        }

        echo "</li>";
    }

    echo "</ul>";
}

/***********************************************************************
 *  KATEGORIE – USUWANIE
 ***********************************************************************/
function UsunKategorie($id)
{
    global $mysqli;
    $id = intval($id);

    $stmt = $mysqli->prepare("DELETE FROM kategorie WHERE matka = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt = $mysqli->prepare("DELETE FROM kategorie WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<div style='color:red;'>Usunięto kategorię</div>";
}

/***********************************************************************
 *  MENU CMS
 ***********************************************************************/
echo "
<h2>Panel administracyjny</h2>
<ul>
    <li><a href='admin.php?podstrony=1'>Podstrony</a></li>
    <li><a href='admin.php?dodaj=1'>Dodaj podstronę</a></li>
    <li><a href='admin.php?kategorie=1'>Kategorie</a></li>
    <li><a href='admin.php?produkty=1'>Produkty</a></li>
    <li><a href='admin.php?prod_add=1'>Dodaj produkt</a></li>
</ul>
<hr>
";

/***********************************************************************
 *  OBSŁUGA AKCJI
 ***********************************************************************/
if (isset($_GET['produkty'])) PokazProdukty();
if (isset($_GET['prod_add'])) DodajProdukt();
if (isset($_GET['prod_edit'])) EdytujProdukt($_GET['prod_edit']);
if (isset($_GET['prod_del'])) {
    UsunProdukt($_GET['prod_del']);
    PokazProdukty();
}
if (isset($_GET['podstrony'])) ListaPodstron();
if (isset($_GET['dodaj'])) DodajNowaPodstrone();
if (isset($_GET['edit'])) EdytujPodstrone($_GET['edit']);
if (isset($_GET['delete'])) UsunPodstrone($_GET['delete']);

if (isset($_GET['kategorie'])) {
    DodajKategorie();
    PokazKategorie();
}

if (isset($_GET['kat_usun'])) {
    UsunKategorie($_GET['kat_usun']);
    DodajKategorie();
    PokazKategorie();
}
?>