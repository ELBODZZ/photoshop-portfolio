<?php
require_once 'includes/auth.php';
requireLogin();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Add
if (($_POST['action'] ?? '') === 'add' && !empty($_POST['name']) && !empty($_POST['category_id'])) {
    $stmt = $pdo->prepare("INSERT INTO subcategories (name, category_id) VALUES (?, ?)");
    $stmt->execute([$_POST['name'], $_POST['category_id']]);
    header('Location: subcategories.php?success=added');
    exit();
}

// Edit
if (($_POST['action'] ?? '') === 'edit' && !empty($_POST['id']) && !empty($_POST['name']) && !empty($_POST['category_id'])) {
    $stmt = $pdo->prepare("UPDATE subcategories SET name = ?, category_id = ? WHERE id = ?");
    $stmt->execute([$_POST['name'], $_POST['category_id'], $_POST['id']]);
    header('Location: subcategories.php?success=edited');
    exit();
}

// Delete
if (!empty($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM subcategories WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: subcategories.php?success=deleted');
    exit();
}

$subcategories = $pdo->query("
    SELECT s.*, c.name as category_name 
    FROM subcategories s 
    JOIN categories c ON s.category_id = c.id 
    ORDER BY s.name
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Subcategories</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{background:#0F0F0F; color:#fff;}</style>
</head>
<body class="bg-dark">
<div class="container py-5">
  <h2>Manage Subcategories</h2>

  <?php if (!empty($_GET['success'])): ?>
    <div class="alert alert-success">Action completed.</div>
  <?php endif; ?>

  <!-- Add Form -->
  <div class="card bg-secondary p-4 mb-5">
    <h5>Add New Subcategory</h5>
    <form method="POST">
      <input type="hidden" name="action" value="add">
      <div class="mb-3">
        <input type="text" name="name" class="form-control bg-dark text-light" placeholder="Subcategory name" required>
      </div>
      <div class="mb-3">
        <select name="category_id" class="form-select bg-dark text-light" required>
          <option value="">Select Main Category</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Add Subcategory</button>
    </form>
  </div>

  <!-- List -->
  <table class="table table-dark table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Category</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($subcategories as $sub): ?>
        <tr>
          <td><?= $sub['id'] ?></td>
          <td><?= htmlspecialchars($sub['name']) ?></td>
          <td><?= htmlspecialchars($sub['category_name']) ?></td>
          <td>
            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $sub['id'] ?>">Edit</button>
            <a href="?delete=<?= $sub['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</a>
          </td>
        </tr>

        <div class="modal fade" id="editModal<?= $sub['id'] ?>">
          <div class="modal-dialog">
            <div class="modal-content bg-secondary">
              <div class="modal-header">
                <h5>Edit Subcategory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <form method="POST">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="id" value="<?= $sub['id'] ?>">
                  <input type="text" name="name" class="form-control mb-3" value="<?= htmlspecialchars($sub['name']) ?>" required>
                  <select name="category_id" class="form-select mb-3" required>
                    <?php foreach ($categories as $cat): ?>
                      <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $sub['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
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