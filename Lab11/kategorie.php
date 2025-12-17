<?php
require_once("cfg.php");
function DodajKategorie()
{
    global $mysqli;

    if (isset($_POST['dodaj'])) {

        $nazwa = htmlspecialchars(trim($_POST['nazwa']));
        $matka = intval($_POST['matka']);

        $stmt = $mysqli->prepare(
            "INSERT INTO kategorie (nazwa, matka) VALUES (?, ?)"
        );
        $stmt->bind_param("si", $nazwa, $matka);
        $stmt->execute();

        echo "<p style='color:green;'>Kategoria została dodana</p>";
    }

    echo '
    <h3>Dodaj kategorię</h3>
    <form method="post">
        Nazwa kategorii:<br>
        <input type="text" name="nazwa" required><br><br>

        Kategoria nadrzędna:<br>
        <select name="matka">
            <option value="0">-- Kategoria główna --</option>';

    $result = $mysqli->query(
        "SELECT id, nazwa FROM kategorie WHERE matka = 0"
    );

    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['nazwa']}</option>";
    }

    echo '
        </select><br><br>
        <input type="submit" name="dodaj" value="Dodaj kategorię">
    </form>';
}
function UsunKategorie()
{
    global $mysqli;

    if (isset($_GET['usun'])) {

        $id = intval($_GET['usun']);

        $stmt = $mysqli->prepare(
            "DELETE FROM kategorie WHERE matka = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt = $mysqli->prepare(
            "DELETE FROM kategorie WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo "<p style='color:red;'>Kategoria została usunięta</p>";
    }
}
function EdytujKategorie()
{
    global $mysqli;

    if (isset($_POST['edytuj'])) {

        $id    = intval($_POST['id']);
        $nazwa = htmlspecialchars(trim($_POST['nazwa']));

        $stmt = $mysqli->prepare(
            "UPDATE kategorie SET nazwa = ? WHERE id = ?"
        );
        $stmt->bind_param("si", $nazwa, $id);
        $stmt->execute();

        echo "<p style='color:green;'>Kategoria została zaktualizowana</p>";
    }
}
function PokazKategorie()
{
    global $mysqli;

    echo "<h3>Lista kategorii</h3>";

    $matki = $mysqli->query(
        "SELECT id, nazwa FROM kategorie WHERE matka = 0 ORDER BY nazwa"
    );

    echo "<ul>";

    while ($matka = $matki->fetch_assoc()) {

        echo "<li>
            <strong>{$matka['nazwa']}</strong>
            <a href='?usun={$matka['id']}'>[usuń]</a>
        ";

        $stmt = $mysqli->prepare(
            "SELECT id, nazwa FROM kategorie WHERE matka = ?"
        );
        $stmt->bind_param("i", $matka['id']);
        $stmt->execute();
        $dzieci = $stmt->get_result();

        if ($dzieci->num_rows > 0) {
            echo "<ul>";
            while ($dziecko = $dzieci->fetch_assoc()) {
                echo "<li>
                    {$dziecko['nazwa']}
                    <a href='?usun={$dziecko['id']}'>[usuń]</a>
                </li>";
            }
            echo "</ul>";
        }

        echo "</li>";
    }

    echo "</ul>";
}
