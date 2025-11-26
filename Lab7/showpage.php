<?php
function PokazPodstrone($id)
{
    global $mysqli;

    $id_clear = intval($id);

    $stmt = $mysqli->prepare("SELECT page_content FROM page_list WHERE id = ? LIMIT 1");
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