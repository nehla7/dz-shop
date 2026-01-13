<?php include __DIR__ . "/includes/header.php"; ?>

<main class="container py-4">
  <h2 class="mb-2">Dashboard</h2>
  <p class="text-muted">Résumé du site DZ-Shop</p>

  <div id="stats" class="alert alert-secondary">Chargement…</div>

  <script>
    (async () => {
      try {
        const res = await fetch("/dz-shop/api/products.php", { cache: "no-store" });
        const products = await res.json();
        document.getElementById("stats").innerHTML =
          "Nombre de produits : <strong>" + products.length + "</strong>";
      } catch (e) {
        document.getElementById("stats").innerHTML =
          "<span class='text-danger'>Impossible de charger les stats.</span>";
      }
    })();
  </script>

  <a class="btn btn-dark btn-sm" href="/dz-shop/admin.php">Retour Admin</a>
</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
