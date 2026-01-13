<?php
require_once __DIR__ . "/api/db.php";
include __DIR__ . "/includes/header.php";

// Helpers
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }

// --- ACTIONS (ADD / UPDATE / DELETE) ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $action = $_POST["action"] ?? "";

  if ($action === "add") {
    $category_id  = (int)($_POST["category_id"] ?? 1);
    $name         = trim($_POST["name"] ?? "");
    $description  = trim($_POST["description"] ?? "");
    $price        = (float)($_POST["price"] ?? 0);
    $stock        = (int)($_POST["stock"] ?? 0);
    $image_url    = trim($_POST["image_url"] ?? "");

    $stmt = $pdo->prepare("
      INSERT INTO products (category_id, name, description, price, stock, image_url, created_at)
      VALUES (:category_id, :name, :description, :price, :stock, :image_url, NOW())
    ");
    $stmt->execute([
      ":category_id" => $category_id,
      ":name" => $name,
      ":description" => $description,
      ":price" => $price,
      ":stock" => $stock,
      ":image_url" => $image_url
    ]);

    header("Location: /dz-shop/admin.php?ok=added");
    exit;
  }

  if ($action === "update") {
    $id           = (int)($_POST["id"] ?? 0);
    $category_id  = (int)($_POST["category_id"] ?? 1);
    $name         = trim($_POST["name"] ?? "");
    $description  = trim($_POST["description"] ?? "");
    $price        = (float)($_POST["price"] ?? 0);
    $stock        = (int)($_POST["stock"] ?? 0);
    $image_url    = trim($_POST["image_url"] ?? "");

    $stmt = $pdo->prepare("
      UPDATE products
      SET category_id=:category_id, name=:name, description=:description, price=:price, stock=:stock, image_url=:image_url
      WHERE id=:id
    ");
    $stmt->execute([
      ":id" => $id,
      ":category_id" => $category_id,
      ":name" => $name,
      ":description" => $description,
      ":price" => $price,
      ":stock" => $stock,
      ":image_url" => $image_url
    ]);

    header("Location: /dz-shop/admin.php?ok=updated");
    exit;
  }

  if ($action === "delete") {
    $id = (int)($_POST["id"] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=:id");
    $stmt->execute([":id" => $id]);

    header("Location: /dz-shop/admin.php?ok=deleted");
    exit;
  }
}

// --- FETCH PRODUCTS ---
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

// --- EDIT MODE (GET ?edit=ID) ---
$editId = isset($_GET["edit"]) ? (int)$_GET["edit"] : 0;
$editProduct = null;
if ($editId > 0) {
  $stmt = $pdo->prepare("SELECT * FROM products WHERE id=:id");
  $stmt->execute([":id" => $editId]);
  $editProduct = $stmt->fetch();
}
?>

<div class="container py-4">
  <h2 class="mb-3">Admin</h2>

  <?php if (isset($_GET["ok"])): ?>
    <div class="alert alert-success">Action OK : <?= e($_GET["ok"]) ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <!-- FORM ADD / EDIT -->
    <div class="col-12 col-lg-5">
      <div class="card p-3">
        <h5 class="mb-3"><?= $editProduct ? "Modifier le produit #".e($editProduct["id"]) : "Ajouter un produit" ?></h5>

        <form method="post">
          <input type="hidden" name="action" value="<?= $editProduct ? "update" : "add" ?>">
          <?php if ($editProduct): ?>
            <input type="hidden" name="id" value="<?= e($editProduct["id"]) ?>">
          <?php endif; ?>

          <div class="mb-2">
            <label class="form-label">Nom</label>
            <input class="form-control" name="name" required value="<?= e($editProduct["name"] ?? "") ?>">
          </div>

          <div class="mb-2">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3"><?= e($editProduct["description"] ?? "") ?></textarea>
          </div>

          <div class="row">
            <div class="col-6 mb-2">
              <label class="form-label">Prix (DA)</label>
              <input class="form-control" type="number" step="0.01" name="price" required value="<?= e($editProduct["price"] ?? "0") ?>">
            </div>
            <div class="col-6 mb-2">
              <label class="form-label">Stock</label>
              <input class="form-control" type="number" name="stock" required value="<?= e($editProduct["stock"] ?? "0") ?>">
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label">Category ID</label>
            <input class="form-control" type="number" name="category_id" value="<?= e($editProduct["category_id"] ?? "1") ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Image URL (ex: assets/images/rouge.jpg)</label>
            <input class="form-control" name="image_url" value="<?= e($editProduct["image_url"] ?? "") ?>">
          </div>

          <button class="btn btn-dark w-100" type="submit">
            <?= $editProduct ? "Enregistrer modification" : "Ajouter" ?>
          </button>

          <?php if ($editProduct): ?>
            <a class="btn btn-outline-secondary w-100 mt-2" href="/dz-shop/admin.php">Annuler</a>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <!-- LIST PRODUCTS -->
    <div class="col-12 col-lg-7">
      <div class="card p-3">
        <h5 class="mb-3">Produits</h5>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Image</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $p): ?>
                <tr>
                  <td><?= e($p["id"]) ?></td>
                  <td><?= e($p["name"]) ?></td>
                  <td><?= e($p["price"]) ?> DA</td>
                  <td><?= e($p["stock"]) ?></td>
                  <td style="max-width:180px;">
                    <small class="text-muted"><?= e($p["image_url"]) ?></small>
                  </td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-primary" href="/dz-shop/admin.php?edit=<?= e($p["id"]) ?>">Modifier</a>

                    <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer ce produit ?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= e($p["id"]) ?>">
                      <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$products): ?>
                <tr><td colspan="6" class="text-muted">Aucun produit.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <a class="btn btn-outline-dark" href="/dz-shop/dashboard.php">Aller au dashboard</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
