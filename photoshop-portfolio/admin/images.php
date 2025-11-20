<?php
require_once 'includes/auth.php';
requireLogin();

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $title = trim($_POST['title']);
    $catId = (int)$_POST['category_id'];
    $subcatId = !empty($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : null;

    if (!empty($title) && $catId > 0 && $_FILES['image']['error'] === 0) {
        $uploadDir = '../assets/uploads/';
        $file = $_FILES['image'];
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowedTypes) && $file['size'] < 10 * 1024 * 1024) { // 10MB max
            $filename = uniqid() . '.' . $ext;
            $target = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                $stmt = $pdo->prepare("INSERT INTO images (title, filename, category_id, subcategory_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $filename, $catId, $subcatId]);
                $success = "Image uploaded successfully!";
            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Invalid file type or size (max 10MB).";
        }
    } else {
        $error = "Please fill all fields and select an image.";
    }
}

// Handle Delete
if (!empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Get filename to delete from disk
    $stmt = $pdo->prepare("SELECT filename FROM images WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetch();
    if ($image) {
        $filePath = '../assets/uploads/' . $image['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $stmt = $pdo->prepare("DELETE FROM images WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: images.php?deleted=1');
        exit();
    }
}

// Fetch data
$images = $pdo->query("SELECT i.*, c.name as category_name, s.name as subcategory_name 
                      FROM images i 
                      LEFT JOIN categories c ON i.category_id = c.id 
                      LEFT JOIN subcategories s ON i.subcategory_id = s.id 
                      ORDER BY i.created_at DESC")->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$subcategories = $pdo->query("SELECT * FROM subcategories ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Images</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root { --primary: #0F0F0F; --secondary: #1A1A1A; }
    body { background: var(--primary); color: #fff; }
    .img-thumb { max-height: 100px; object-fit: cover; }
  </style>
</head>
<body class="bg-dark">
<div class="container py-5">
  <h2>Manage Portfolio Images</h2>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if (!empty($_GET['deleted'])): ?>
    <div class="alert alert-info">Image deleted successfully.</div>
  <?php endif; ?>

  <!-- Upload Form -->
  <div class="card bg-secondary p-4 mb-5">
    <h5>Upload New Image</h5>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control bg-dark text-light" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select bg-dark text-light" required>
          <option value="">Select Main Category</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Subcategory (Optional)</label>
        <select name="subcategory_id" class="form-select bg-dark text-light">
          <option value="">None</option>
          <?php foreach ($subcategories as $sub): ?>
            <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Image (JPG, PNG, WebP - Max 10MB)</label>
        <input type="file" name="image" class="form-control bg-dark text-light" accept="image/*" required>
      </div>
      <button type="submit" class="btn btn-primary">Upload Image</button>
    </form>
  </div>

  <!-- Image List -->
  <h5 class="mt-5">Uploaded Images (<?= count($images) ?>)</h5>
  <?php if (empty($images)): ?>
    <p class="text-muted">No images uploaded yet.</p>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($images as $img): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card bg-secondary">
            <img src="../assets/uploads/<?= htmlspecialchars($img['filename']) ?>" class="card-img-top img-thumb" alt="<?= htmlspecialchars($img['title']) ?>">
            <div class="card-body p-2">
              <h6 class="card-title"><?= htmlspecialchars($img['title']) ?></h6>
              <p class="mb-1"><small>
                <?= htmlspecialchars($img['category_name']) ?>
                <?php if (!empty($img['subcategory_name'])): ?>
                  → <?= htmlspecialchars($img['subcategory_name']) ?>
                <?php endif; ?>
              </small></p>
              <a href="?delete=<?= $img['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this image?')">Delete</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>