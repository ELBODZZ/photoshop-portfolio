<?php
require_once 'includes/auth.php';
requireLogin();

$stmt = $pdo->query("SELECT COUNT(*) FROM images");
$totalImages = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM categories");
$totalCats = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{background:#0F0F0F; color:#fff;}</style>
</head>
<body class="bg-dark">
  <div class="container py-5">
    <h2>Admin Dashboard</h2>
    <div class="row mt-4">
      <div class="col-md-3">
        <div class="card bg-secondary text-white">
          <div class="card-body">
            <h5>Total Works</h5>
            <p class="display-6"><?= $totalImages ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-secondary text-white">
          <div class="card-body">
            <h5>Categories</h5>
            <p class="display-6"><?= $totalCats ?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-4">
      <a href="categories.php" class="btn btn-primary">Manage Categories</a>
      <a href="subcategories.php" class="btn btn-primary">Manage Subcategories</a>
      <a href="images.php" class="btn btn-primary">Manage Images</a>
      <a href="logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>