<?php
require_once 'includes/auth.php';
requireLogin();

// Add
if (($_POST['action'] ?? '') === 'add' && !empty($_POST['name'])) {
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([trim($_POST['name'])]);
    header('Location: categories.php?success=added');
    exit();
}

// Edit
if (($_POST['action'] ?? '') === 'edit' && !empty($_POST['id']) && !empty($_POST['name'])) {
    $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
    $stmt->execute([trim($_POST['name']), $_POST['id']]);
    header('Location: categories.php?success=edited');
    exit();
}

// Delete
if (!empty($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: categories.php?success=deleted');
    exit();
}


$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Categories</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{background:#0F0F0F; color:#fff;}</style>
</head>
<body class="bg-dark">
<div class="container py-5">
  <h2>Manage Main Categories</h2>

  <?php if (!empty($_GET['success'])): ?>
    <div class="alert alert-success">Action completed successfully.</div>
  <?php endif; ?>

  <!-- Add Form -->
  <div class="card bg-secondary p-4 mb-5">
    <h5>Add New Category</h5>
    <form method="POST">
      <input type="hidden" name="action" value="add">
      <div class="mb-3">
        <input type="text" name="name" class="form-control bg-dark text-light" placeholder="Category name" required>
      </div>
      <button type="submit" class="btn btn-primary">Add Category</button>
    </form>
  </div>

  <!-- List -->
  <table class="table table-dark table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categories as $cat): ?>
        <tr>
          <td><?= $cat['id'] ?></td>
          <td><?= htmlspecialchars($cat['name']) ?></td>
          <td>
            <!-- Edit Button (triggers JS modal or inline form) -->
            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $cat['id'] ?>">Edit</button>
            <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this category? All related subcategories and images will be removed!')">Delete</a>
          </td>
        </tr>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal<?= $cat['id'] ?>">
          <div class="modal-dialog">
            <div class="modal-content bg-secondary">
              <div class="modal-header">
                <h5>Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <form method="POST">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                  <input type="text" name="name" class="form-control mb-3" value="<?= htmlspecialchars($cat['name']) ?>" required>
                  <button type="submit" class="btn btn-primary">Save</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>