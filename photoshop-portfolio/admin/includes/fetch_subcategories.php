<?php
// admin/includes/fetch_subcategories.php
require_once 'db.php';
header('Content-Type: application/json');

try {
    $categoryId = $_GET['category_id'] ?? 0;
    if (!$categoryId) {
        echo json_encode([]);
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM subcategories WHERE category_id = ? ORDER BY name");
    $stmt->execute([$categoryId]);
    $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($subcategories);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>