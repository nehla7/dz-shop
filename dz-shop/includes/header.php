<?php
// includes/header.php
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DZ-Shop</title>

  <!-- Bootstrap (CDN) : pas de 404 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Ton CSS (facultatif). Si tu n'as pas ce fichier, crée-le vide pour éviter 404 -->
  <link rel="stylesheet" href="/dz-shop/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg bg-light border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/dz-shop/">DZ-Shop</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
      <li class="nav-item">
  <a class="nav-link" href="/dz-shop/">Boutique</a>
</li>

<li class="nav-item">
  <a class="nav-link" href="/dz-shop/panier.php">Panier</a>
</li>

<li class="nav-item">
  <a class="nav-link" href="/dz-shop/admin.php">Admin</a>
</li>

<li class="nav-item">
  <a class="nav-link" href="/dz-shop/dashboard.php">Dashboard</a>
</li>
<li class="nav-item"><a class="nav-link" href="/dz-shop/about.php">À propos</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="container py-4">