<?php
// admin/includes/fetch_portfolio.php
require_once 'db.php';
header('Content-Type: application/json');

try {
    // Fetch all categories
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch all images with category/subcategory info
    $images = $pdo->query("
        SELECT 
            i.id, 
            i.title, 
            i.filename, 
            i.category_id, 
            i.subcategory_id
        FROM images i 
        ORDER BY i.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'categories' => $categories,
        'images' => $images
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>