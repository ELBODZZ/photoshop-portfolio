<?php
header("Content-Type: application/json");
require_once "../admin/includes/db.php";

try {
    // Fetch categories
    $catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch subcategories
    $subStmt = $pdo->query("SELECT id, name, category_id FROM subcategories ORDER BY name");
    $subcategories = $subStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch images
    $imgStmt = $pdo->query("
        SELECT 
            images.id,
            images.title,
            images.filename,
            images.category_id,
            images.subcategory_id,
            categories.name AS category_name,
            subcategories.name AS subcategory_name
        FROM images
        LEFT JOIN categories ON images.category_id = categories.id
        LEFT JOIN subcategories ON images.subcategory_id = subcategories.id
        ORDER BY images.id DESC
    ");
    $images = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "categories" => $categories,
        "subcategories" => $subcategories,
        "images" => $images
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
