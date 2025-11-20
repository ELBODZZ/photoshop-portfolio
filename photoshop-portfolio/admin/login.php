<?php
require_once 'includes/auth.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (login($username, $password)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root { --primary: #0F0F0F; --accent1: #5A00FF; --accent2: #00C2FF; }
    body { background: var(--primary); height: 100vh; }
    .form-container { max-width: 400px; }
    .btn-login { background: linear-gradient(90deg, var(--accent1), var(--accent2)); border: none; }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center text-light">
  <div class="form-container w-100">
    <div class="card bg-dark border border-secondary">
      <div class="card-body p-4">
        <h3 class="text-center mb-4">Admin Login</h3>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
          <div class="mb-3">
            <input type="text" name="username" class="form-control bg-secondary text-light" placeholder="Username" required>
          </div>
          <div class="mb-3">
            <input type="password" name="password" class="form-control bg-secondary text-light" placeholder="Password" required>
          </div>
          <button type="submit" class="btn btn-login w-100">Login</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>