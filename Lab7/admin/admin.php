<?php

session_start();
require_once("..\cfg.php");

if (isset($_POST['x1_submit'])){
	if($_POST['login_email']==$login && $_POST['login_pass']==$pass){
		$_SESSION['zalogowany']=true;
	}else{
		$error = 'Bledny login lub haslo';
		echo FormularzLogowania($error);
		exit();
	}
}

if(!isset($_SESSION['zalogowany'])){
	echo FormularzLogowania();
	exit();
}

function FormularzLogowania($komunikat = "")
{
    $wynik = '
    <h1 class="heading">Panel CMS</h1>

    <div id="login_box">
        <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
            <table>
                <tr><td>Email:</td><td><input type="text" name="login_email" /></td></tr>
                <tr><td>Haslo:</td><td><input type="password" name="login_pass" /></td></tr>
                <tr><td colspan="2"><input type="submit" name="x1_submit" value="Zaloguj" /></td></tr>
            </table>
        </form>
        <div style="color:red;">'.$komunikat.'</div>
    </div>';

    return $wynik;
}


function ListaPodstron() {

    require("../cfg.php");

    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

    if (!$conn) {
        echo "Błąd: nie udało się połączyć z bazą danych.";
        return;
    }

    $query = "SELECT id, page_title FROM page_list ORDER BY id ASC";
    $result = mysqli_query($conn, $query);
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Tytuł</th><th>Akcja</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {

        echo "<tr>";
        echo "<td>".$row['id']."</td>";
        echo "<td>".$row['page_title']."</td>";
        echo "<td>
                <a href='admin.php?edit=".$row['id']."'>Edytuj</a> |
                <a href='admin.php?delete=".$row['id']."'>Usuń</a>
              </td>";
        echo "</tr>";
    }

    echo "</table>";

    mysqli_close($conn);
}


function EdytujPodstrone($id){
    require("../cfg.php");
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    if(!$conn){
        echo "Błąd połączenia z bazą!";
        return;
    }
    if (isset($_POST['save'])){
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);
        $status = isset($_POST['status']) ? 1 : 0;

        $query = "
            UPDATE page_list
            SET page_title='$title', page_content='$content', status='$status'
            WHERE id=$id
            LIMIT 1
        ";

        mysqli_query($conn, $query);

        echo "<div style='color:green;'>Zapisano zmiany!</div>";
    }
     $result = mysqli_query($conn, "SELECT * FROM page_list WHERE id=$id LIMIT 1");
    $row = mysqli_fetch_assoc($result);

    ?>
    

    <h2>Edycja podstrony (ID: <?= $id ?>)</h2>

<form method="post">
    Tytuł:<br>
    <input type="text" name="title" style="width:300px;" value="<?= $row['page_title'] ?>"><br><br>

    Treść strony:<br>
    <textarea name="content" rows="10" cols="60"><?= $row['page_content'] ?></textarea><br><br>

    Aktywna? 
    <input type="checkbox" name="status" <?= $row['status'] ? 'checked' : '' ?>><br><br>

    <input type="submit" name="save" value="Zapisz zmiany">

</form>

    <?php

    mysqli_close($conn);
}

function DodajNowaPodstrone()
{
    require("../cfg.php");
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

    if (!$conn) {
        echo "Błąd połączenia z bazą!";
        return;
    }

    if (isset($_POST['dodaj'])) {

        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);
        $status = isset($_POST['status']) ? 1 : 0;

        $query = "
            INSERT INTO page_list (page_title, page_content, status) 
            VALUES ('$title', '$content', '$status')
        ";

        mysqli_query($conn, $query);

        echo "<div style='color:green;'>Dodano nową podstronę!</div>";
    }

?>
    <h2>Dodawanie nowej podstrony</h2>

    <form method="post">
        Tytuł:<br>
        <input type="text" name="title" style="width:300px;"><br><br>

        Treść:<br>
        <textarea name="content" rows="10" cols="60"></textarea><br><br>

        Aktywna?
        <input type="checkbox" name="status"><br><br>

        <input type="submit" name="dodaj" value="Dodaj stronę">
    </form>

<?php
    mysqli_close($conn);
}


function UsunPodstrone($id)
{
    require("../cfg.php");
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

    if (!$conn) {
        echo "Błąd połączenia z bazą!";
        return;
    }

    $query = "DELETE FROM page_list WHERE id=$id LIMIT 1";
    mysqli_query($conn, $query);

    echo "<div style='color:red;'>Usunięto podstronę o ID: $id</div>";

    mysqli_close($conn);
}

echo "<h2>Panel administracyjny</h2>";

echo "<ul>
        <li><a href='admin.php?podstrony=1'>Lista podstron</a></li>
        <li><a href='admin.php?dodaj=1'>Dodaj nową podstronę</a></li>
      </ul><hr>";

if (isset($_GET['podstrony'])) {
    ListaPodstron();
}

if (isset($_GET['edit'])) {
    EdytujPodstrone($_GET['edit']);
}

if (isset($_GET['dodaj'])) {
    DodajNowaPodstrone();
}

if (isset($_GET['delete'])) {
    UsunPodstrone($_GET['delete']);
}

?>