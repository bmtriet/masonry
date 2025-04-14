<?php
// *** LUÔN GỌI ĐẦU TIÊN ***
require_once __DIR__ . '/includes/auth.php';
requireLogin(); // Check login status AFTER session is started
require_once __DIR__ . '/includes/functions.php';

// *** KHÔNG CÓ OUTPUT HTML Ở ĐÂY, NÊN KHÔNG CẦN INCLUDE HEADER/FOOTER ***

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
     header('Location: admin.php');
     exit;
}

// Add JS confirmation in main.js for safety
$success = deleteProduct($product_id);

if (!$success) {
    error_log("Failed to delete product ID: " . $product_id);
}

header('Location: admin.php');
exit;
?>