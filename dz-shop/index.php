<?php
// index.php
require_once __DIR__ . "/includes/header.php";
?>

<div class="container py-4">
  <div class="row align-items-center g-3 mb-4">
    <div class="col-md-8">
      <h1 class="display-5 fw-bold mb-1">Boutique en ligne DZ-Shop</h1>
      <p class="text-muted mb-0">Site e-commerce professionnel (HTML, CSS, JS, PHP, MySQL)</p>
    </div>

    <div class="col-md-4">
      <input id="searchInput" class="form-control" type="text" placeholder="Rechercher un produit...">
    </div>
  </div>

  <hr class="my-4">

  <h2 class="h4 mb-3">Produits</h2>

  <!-- IMPORTANT: C’est ici que les produits doivent s’afficher -->
  <div id="products" class="row g-3">
    <div class="col-12 text-muted">Chargement des produits…</div>
  </div>
</div>

<?php
require_once __DIR__ . "/includes/footer.php";
?>
