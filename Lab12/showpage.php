<?php
/***********************************************************************
 *  PLIK: showpage.php
 *  OPIS: Pobiera podstronę z bazy danych lub z plików lokalnych.
 ***********************************************************************/

function PokazPodstrone($id)
{
    global $mysqli;
    $id_clear = intval($id);

    /* ===== STRONY Z PLIKÓW ===== */
    if ($id_clear == 7) {
        ob_start();
        include("sklep.php");
        return ob_get_clean();
    }

    if ($id_clear == 8) {
        ob_start();
        include("koszyk.php");
        return ob_get_clean();
    }

    /* ===== STRONY Z BAZY ===== */
    $stmt = $mysqli->prepare(
        "SELECT page_content FROM page_list WHERE id = ? LIMIT 1"
    );
    $stmt->bind_param("i", $id_clear);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        return "[nie_znaleziono_strony]";
    }

    $row = $result->fetch_assoc();
    return $row['page_content'];
}
?>
