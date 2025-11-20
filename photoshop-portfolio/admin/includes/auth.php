<?php
session_start();
require_once 'db.php';

// هل ال Admin مسجل دخول؟
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// لو مش داخل يمنعه ويرجّعه لصفحة اللوجين
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// دالة تسجيل الدخول بدون Hash
function login($username, $password) {
    global $pdo;

    // جلب بيانات المستخدم من قاعدة البيانات
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // مقارنة مباشرة (بدون password_hash)
    if ($user && $password === $user['password']) {
        $_SESSION['admin_id'] = $user['id']; // حفظ الجلسة
        return true;
    }

    return false;
}
?>
