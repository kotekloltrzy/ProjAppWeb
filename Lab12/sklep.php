<?php
// (Reszta logiki PHP pozostaje bez zmian, ale dołączam ją dla kompletności pliku)
$kat = isset($_GET['kat']) ? intval($_GET['kat']) : 0;

/* ===== POBIERANIE KATEGORII ===== */
// Upewniamy się, że $mysqli jest dostępne (powinno być z index.php)
if (isset($mysqli)) {
    $kategorie = $mysqli->query("SELECT id, nazwa FROM kategorie WHERE matka = 0 ORDER BY nazwa");

    /* ===== POBIERANIE PRODUKTÓW ===== */
    $where = "
    WHERE status_dostepnosci = 1
    AND ilosc_magazynowa > 0
    AND data_wygasniecia > CURDATE()
    ";

    if ($kat > 0) {
        $where .= " AND kategoria = $kat";
    }

    $sql = "
    SELECT *
    FROM produkty
    $where
    ORDER BY RAND()
    LIMIT 12
    ";

    $produkty = $mysqli->query($sql);
}
?>

<style>
/* --- KLUCZOWE ZMIANY W CSS --- */

/* Kontener główny sklepu - ustawia elementy obok siebie */
.shop-wrapper {
    display: flex;          /* To ustawia dzieci (.left i .right) w rzędzie */
    align-items: flex-start; /* Wyrównuje kolumny do góry */
    gap: 20px;              /* Odstęp między lewą a prawą kolumną */
    padding: 20px;
    font-family: Arial, sans-serif;
}

/* Lewa kolumna (kategorie) */
.left {
    width: 220px;
    flex-shrink: 0;         /* Zapobiega zmniejszaniu się kolumny, gdy brakuje miejsca */
    background: #eee;
    padding: 15px;
    border-radius: 5px;
}

.left a {
    display: block;
    padding: 8px;
    text-decoration: none;
    color: black;
    border-bottom: 1px solid #ddd;
}
.left a:last-child { border-bottom: none; }
.left a:hover { background: #ccc; }

/* Prawa kolumna (produkty) */
.right {
    flex: 1;                /* Zajmuje całą pozostałą dostępną przestrzeń */
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); /* Siatka produktów */
    gap: 20px;
}

/* Styl pojedynczego produktu */
.prod {
    border: 1px solid #aaa;
    padding: 10px;
    text-align: center;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.prod img {
    width: 180px;
    height: 180px;
    object-fit: cover;
}
.prod h4 { margin: 10px 0 5px; min-height: 40px; display: flex; align-items: center; justify-content: center; }
.prod p { margin: 5px 0; }

button {
    padding: 8px 12px;
    margin-top: 5px;
    cursor: pointer;
    background: #007bff; color: white; border: none; border-radius: 3px;
}
button:hover { background: #0056b3; }

#go-to-cart {
    display: block;
    margin-top: 20px;
    padding: 10px 15px;
    background: #28a745;
    color: white;
    text-decoration: none;
    text-align: center;
    border-radius: 5px;
    border: none;
}
#go-to-cart:hover { background: #218838; }
</style>

<div class="shop-wrapper">

    <div class="left">
        <h3>Kategorie</h3>
        <a href="index.php?id=7">Wszystkie</a>

        <?php if(isset($kategorie) && $kategorie): ?>
            <?php while($k = $kategorie->fetch_assoc()): ?>
                <a href="index.php?id=7&kat=<?= $k['id'] ?>">
                    <?= $k['nazwa'] ?>
                </a>
            <?php endwhile; ?>
        <?php endif; ?>

        <a id="go-to-cart" href="index.php?id=8">Przejdź do koszyka</a>
    </div>

    <div class="right">
        <?php
        if (!isset($produkty) || $produkty->num_rows == 0) {
            echo "<h3>Brak produktów w tej kategorii</h3>";
        } else {
            while($p = $produkty->fetch_assoc()):
                $cena_brutto = $p['cena_netto'] + ($p['cena_netto'] * $p['podatek_vat'] / 100);
        ?>
            <div class="prod">
                <?php if(!empty($p['zdjecie'])): ?>
                <img src="<?= $p['zdjecie'] ?>" alt="<?= $p['tytul'] ?>">
                <?php else: ?>
                <div style="width:180px; height:180px; background:#ccc; display:flex; align-items:center; justify-content:center;">Brak zdjęcia</div>
                <?php endif; ?>

                <h4><?= $p['tytul'] ?></h4>
                <p>Cena: <b><?= number_format($cena_brutto,2) ?> zł</b></p>
                <p style="font-size: 0.9em; color: #666;">Dostępne: <?= $p['ilosc_magazynowa'] ?> szt.</p>
                <button class="add-to-cart" data-id="<?= $p['id'] ?>" data-ilosc="1">Dodaj do koszyka</button>
            </div>
        <?php endwhile; } ?>
    </div>

</div> <script>
// Skrypt AJAX (bez zmian, upewniamy się tylko że używa id_prod)
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', () => {
        const produktId = btn.dataset.id;
        const ilosc = btn.dataset.ilosc;

        // Używamy id_prod, aby nie kolidowało z id strony w index.php
        fetch(`index.php?id=8&add=1&id_prod=${produktId}&ilosc=${ilosc}`)
            .then(response => response.text())
            .then(data => {
                // Opcjonalnie: można tu sprawdzić odpowiedź serwera
                alert('Produkt dodany do koszyka!');
            })
            .catch(err => {
                alert('Błąd przy dodawaniu produktu!');
                console.error(err);
            });
    });
});
</script>