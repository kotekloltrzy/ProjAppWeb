<?php
$kat = isset($_GET['kat']) ? intval($_GET['kat']) : 0;

if (isset($mysqli)) {
    /* ===== POBIERANIE KATEGORII I PODKATEGORII ===== */
    $res = $mysqli->query("SELECT id, nazwa, matka FROM kategorie ORDER BY matka ASC, nazwa ASC");
    $kategorie_tree = [];
    
    while ($row = $res->fetch_assoc()) {
        $kategorie_tree[$row['matka']][] = $row;
    }

    /* ===== POBIERANIE PRODUKTÓW ===== */
    $where = " WHERE status_dostepnosci = 1 AND ilosc_magazynowa > 0 AND data_wygasniecia > CURDATE() ";

    if ($kat > 0) {
        $ids_to_check = [$kat];
       
        if (isset($kategorie_tree[$kat])) {
            foreach ($kategorie_tree[$kat] as $sub_kat) {
                $ids_to_check[] = (int)$sub_kat['id'];
                
                if (isset($kategorie_tree[$sub_kat['id']])) {
                    foreach ($kategorie_tree[$sub_kat['id']] as $grand_child) {
                        $ids_to_check[] = (int)$grand_child['id'];
                    }
                }
            }
        }

        $ids_string = implode(',', $ids_to_check);
        $where .= " AND kategoria IN ($ids_string)";
    }

    $sql = "SELECT * FROM produkty $where ORDER BY RAND() LIMIT 12";
    $produkty = $mysqli->query($sql);
}
?>

<link rel="stylesheet" href="css/style.css" type="text/css" />

<div class="shop-wrapper">
    <div class="left">
        </div>
    <div class="right">
        </div>
</div>


<div class="shop-wrapper">

    <div class="left">
    <h3>Kategorie</h3>
    <a href="index.php?id=7" class="<?= $kat == 0 ? 'active-kat' : '' ?>">Wszystkie</a>

    <?php 
    if(isset($kategorie_tree[0])):
        foreach($kategorie_tree[0] as $main_kat): 
    ?>
        <a href="index.php?id=7&kat=<?= $main_kat['id'] ?>" class="<?= $kat == $main_kat['id'] ? 'active-kat' : '' ?>">
            <?= $main_kat['nazwa'] ?>
        </a>

        <?php if(isset($kategorie_tree[$main_kat['id']])): ?>
            <div class="submenu">
                <?php foreach($kategorie_tree[$main_kat['id']] as $sub_kat): ?>
                    <a href="index.php?id=7&kat=<?= $sub_kat['id'] ?>" class="<?= $kat == $sub_kat['id'] ? 'active-kat' : '' ?>">
                        └ <?= $sub_kat['nazwa'] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php 
        endforeach; 
    endif; 
    ?>

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

    <h4><?= htmlspecialchars($p['tytul']) ?></h4>
    
    <div class="prod-description">
        <?= nl2br(htmlspecialchars(substr($p['opis'], 0, 100))) ?><?= strlen($p['opis']) > 100 ? '...' : '' ?>
    </div>

    <p>Cena: <b><?= number_format($cena_brutto, 2, ',', ' ') ?> zł</b></p>
    <p style="font-size: 0.9em; color: #666;">Dostępne: <?= $p['ilosc_magazynowa'] ?> szt.</p>
    <button class="add-to-cart" data-id="<?= $p['id'] ?>" data-ilosc="1">Dodaj do koszyka</button>
</div>
        <?php endwhile; } ?>
    </div>

</div> <script>
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', () => {
        const produktId = btn.dataset.id;
        const ilosc = btn.dataset.ilosc;
        fetch(`index.php?id=8&add=1&id_prod=${produktId}&ilosc=${ilosc}`)
            .then(response => response.text())
            .then(data => {
                alert('Produkt dodany do koszyka!');
            })
            .catch(err => {
                alert('Błąd przy dodawaniu produktu!');
                console.error(err);
            });
    });
});
</script>