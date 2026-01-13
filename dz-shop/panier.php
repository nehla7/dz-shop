<?php
session_start();
require_once __DIR__ . "/api/db.php";
include __DIR__ . "/includes/header.php";

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }

// Init panier en session
if (!isset($_SESSION["cart"])) {
  $_SESSION["cart"] = []; // [productId => qty]
}

// Actions: add, remove, clear
$addId = isset($_GET["add"]) ? (int)$_GET["add"] : 0;
$removeId = isset($_GET["remove"]) ? (int)$_GET["remove"] : 0;
$clear = isset($_GET["clear"]) ? (int)$_GET["clear"] : 0;

if ($addId > 0) {
  $_SESSION["cart"][$addId] = ($_SESSION["cart"][$addId] ?? 0) + 1;
  header("Location: /dz-shop/panier.php");
  exit;
}
if ($removeId > 0) {
  unset($_SESSION["cart"][$removeId]);
  header("Location: /dz-shop/panier.php");
  exit;
}
if ($clear === 1) {
  $_SESSION["cart"] = [];
  header("Location: /dz-shop/panier.php");
  exit;
}

// Récupérer les produits du panier
$ids = array_keys($_SESSION["cart"]);
$products = [];
$total = 0;

if (count($ids) > 0) {
  $placeholders = implode(",", array_fill(0, count($ids), "?"));
  $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
  $stmt->execute($ids);
  $rows = $stmt->fetchAll();

  // index par id
  foreach ($rows as $p) $products[(int)$p["id"]] = $p;

  // total
  foreach ($_SESSION["cart"] as $pid => $qty) {
    if (isset($products[$pid])) {
      $total += ((float)$products[$pid]["price"]) * (int)$qty;
    }
  }
}
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Panier</h2>
    <a class="btn btn-outline-secondary" href="/dz-shop/index.php">← Retour boutique</a>
  </div>

  <?php if (count($_SESSION["cart"]) === 0): ?>
    <div class="card p-4 text-center">
      <h5 class="mb-2">Votre panier est vide</h5>
      <p class="text-muted mb-3">Ajoutez des produits pour les retrouver ici.</p>
      <a class="btn btn-dark" href="/dz-shop/index.php">Voir la boutique</a>
    </div>
  <?php else: ?>

    <div class="row g-3">
      <div class="col-12 col-lg-8">
        <?php foreach ($_SESSION["cart"] as $pid => $qty): ?>
          <?php if (!isset($products[$pid])) continue; $p = $products[$pid]; ?>
          <div class="card p-3 mb-3">
            <div class="d-flex gap-3 align-items-center">
              <?php
                $img = trim((string)$p["image_url"]);
                if ($img === "") $img = "https://picsum.photos/seed/p".$pid."/600/400";
              ?>
              <img src="<?= e($img) ?>" alt="<?= e($p["name"]) ?>"
                   style="width:110px;height:80px;object-fit:cover;border-radius:12px;">
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between">
                  <strong><?= e($p["name"]) ?></strong>
                  <strong><?= number_format((float)$p["price"], 2) ?> DA</strong>
                </div>
                <div class="text-muted small"><?= e($p["description"]) ?></div>
                <div class="mt-2 d-flex justify-content-between align-items-center">
                  <span class="badge text-bg-light">Quantité : <?= (int)$qty ?></span>
                  <a class="btn btn-sm btn-outline-danger" href="/dz-shop/panier.php?remove=<?= (int)$pid ?>">Supprimer</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="col-12 col-lg-4">
        <div class="card p-3">
          <h5 class="mb-3">Résumé</h5>
          <div class="d-flex justify-content-between">
            <span>Total</span>
            <strong><?= number_format($total, 2) ?> DA</strong>
          </div>

          <a class="btn btn-dark w-100 mt-3" href="#" onclick="alert('Paiement non implémenté (projet).'); return false;">
            Passer commande
          </a>

          <a class="btn btn-outline-danger w-100 mt-2" href="/dz-shop/panier.php?clear=1"
             onclick="return confirm('Vider le panier ?');">
            Vider le panier
          </a>
        </div>
      </div>
    </div>

  <?php endif; ?>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
