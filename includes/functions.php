<?php
// Basic helper functions

define('DATA_PATH', __DIR__ . '/../data/');
define('UPLOADS_PATH', __DIR__ . '/../uploads/'); // Base uploads path
define('UPLOADS_PRODUCTS_PATH', UPLOADS_PATH . 'products/');
define('UPLOADS_HOME_PATH', UPLOADS_PATH . 'home/');
define('PRODUCTS_JSON_PATH', DATA_PATH . 'products.json');
define('NAV_JSON_PATH', DATA_PATH . 'nav.json');

// --- JSON Data Handling ---
// ... (loadJson and saveJson remain the same) ...
function loadJson($filepath) {
    if (!file_exists($filepath)) {
        error_log("Error: JSON file not found at " . $filepath);
        return null;
    }
    $json_data = file_get_contents($filepath);
    if ($json_data === false) {
         error_log("Error: Could not read JSON file at " . $filepath);
         return null;
    }
    $data = json_decode($json_data, true);
     if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error decoding JSON from " . $filepath . ": " . json_last_error_msg());
        return null;
    }
    return $data;
}

function saveJson($filepath, $data) {
    // Ensure parent directory is writable
    $dir = dirname($filepath);
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0775, true)) { // Create recursive if not exists
             error_log("Error: Failed to create directory " . $dir);
             return false;
        }
    }
     if (!is_writable($dir)) {
        error_log("Error: Directory " . $dir . " is not writable.");
        return false;
    }

    // Ensure products are saved as an array, even if empty after filtering
    $jsonDataToSave = is_array($data) ? array_values($data) : $data;
    $json_string = json_encode($jsonDataToSave, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
     if ($json_string === false) {
        error_log("Error encoding data to JSON for " . $filepath . ": " . json_last_error_msg());
        return false;
    }
    if (file_put_contents($filepath, $json_string, LOCK_EX) === false) {
         error_log("Error: Could not write JSON file at " . $filepath);
        return false;
    }
    return true;
}


// --- Product Functions ---

function getProducts($brand = null) {
    $products = loadJson(PRODUCTS_JSON_PATH);
    if (!$products) {
        return [];
    }

    if ($brand) {
        $filteredProducts = array_filter($products, function($product) use ($brand) {
            return isset($product['brand']) && strcasecmp($product['brand'], $brand) == 0;
        });
        return array_values($filteredProducts);
    }
    return $products;
}

function getProductById($id) {
    $products = loadJson(PRODUCTS_JSON_PATH);
     if (!$products) return null;

    foreach ($products as $product) {
        if (isset($product['id']) && $product['id'] === $id) {
            if (!isset($product['image_urls']) || !is_array($product['image_urls'])) {
                $product['image_urls'] = [];
            }
            return $product;
        }
    }
    return null;
}

function getPrimaryImageUrl($product, $default = 'uploads/products/placeholder_shoe.jpg') {
    if (!empty($product['image_urls']) && is_array($product['image_urls'])) {
        // Check if the first image exists, otherwise try the next, etc.
        foreach($product['image_urls'] as $url) {
             if(file_exists(UPLOADS_PATH . '../' . $url)) { // Check relative to project root
                 return htmlspecialchars($url);
             }
        }
    }
    // If no valid images found in array, return default
    return htmlspecialchars($default);
}


function addProduct($productData) {
    $products = loadJson(PRODUCTS_JSON_PATH);
    if ($products === null) $products = [];

    // Basic validation (image_urls array is now prepared by admin_process_product.php)
    if (empty($productData['name']) || empty($productData['brand']) || empty($productData['price'])) {
        error_log("Add product error: Missing required fields.");
        return false;
    }
    // Ensure image_urls is an array, even if empty
    if (!isset($productData['image_urls']) || !is_array($productData['image_urls'])) {
        $productData['image_urls'] = [];
    }

    // ID should be generated *before* calling addProduct if needed for folder path
    // Or generate it here if not passed
    if (!isset($productData['id']) || empty($productData['id'])) {
        $productData['id'] = 'prod_' . uniqid();
    }

    $products[] = $productData;
    return saveJson(PRODUCTS_JSON_PATH, $products);
}

function updateProduct($id, $updatedData) {
    $products = loadJson(PRODUCTS_JSON_PATH);
     if (!$products) return false;

    $found = false;
    foreach ($products as $key => $product) {
        if (isset($product['id']) && $product['id'] === $id) {
             // Ensure image_urls is an array, even if empty
            if (!isset($updatedData['image_urls']) || !is_array($updatedData['image_urls'])) {
                $updatedData['image_urls'] = $product['image_urls'] ?? []; // Keep existing if not provided/invalid
            }

            $products[$key] = array_merge($product, $updatedData);
            $products[$key]['id'] = $id; // Ensure ID isn't changed
            $found = true;
            break;
        }
    }

    if (!$found) {
         error_log("Update product error: Product ID {$id} not found.");
        return false;
    }
    return saveJson(PRODUCTS_JSON_PATH, $products);
}


function deleteProduct($id) {
    $products = loadJson(PRODUCTS_JSON_PATH);
    if (!$products) return false;

    $productToDelete = null;
    $initialCount = count($products);

    // Find the product first to get its image paths for deletion
    foreach ($products as $key => $product) {
         if (isset($product['id']) && $product['id'] === $id) {
             $productToDelete = $product;
             unset($products[$key]); // Remove from array
             break;
         }
    }

    if ($productToDelete === null || count($products) === $initialCount) {
        error_log("Delete product error: Product ID {$id} not found.");
        return false; // Product not found
    }

    // Attempt to delete associated images and folder
    if (!empty($productToDelete['image_urls']) && is_array($productToDelete['image_urls'])) {
        $folderPath = '';
        foreach($productToDelete['image_urls'] as $imageUrl) {
            $fullPath = UPLOADS_PATH . '../' . $imageUrl; // Path relative to project root
             if (file_exists($fullPath)) {
                if (unlink($fullPath)) {
                     error_log("Deleted image file: " . $fullPath);
                 } else {
                     error_log("Failed to delete image file: " . $fullPath);
                 }
            }
             // Try to determine folder path from the first image URL
             if(empty($folderPath) && strpos($imageUrl, '/') !== false) {
                 $folderPath = dirname(UPLOADS_PRODUCTS_PATH . $productToDelete['id']); // Guess folder path based on ID convention
             }
        }

         // Attempt to delete the product's folder if it's empty
         // Use the ID to ensure we target the correct folder
         $productFolderPath = UPLOADS_PRODUCTS_PATH . $productToDelete['id'];
         if (is_dir($productFolderPath)) {
            // Check if directory is empty (scandir returns '.', '..')
             if (count(scandir($productFolderPath)) == 2) {
                 if (rmdir($productFolderPath)) {
                      error_log("Deleted empty product folder: " . $productFolderPath);
                 } else {
                      error_log("Failed to delete product folder (maybe not empty or permissions?): " . $productFolderPath);
                 }
             } else {
                  error_log("Product folder not empty, not deleting: " . $productFolderPath);
             }
         }
    }


    // Save the updated product list (with the product removed)
    // Re-index array
    return saveJson(PRODUCTS_JSON_PATH, array_values($products));
}


// --- Homepage Functions ---
// ... (getHomepageImages, getLatestProductsByBrand remain the same) ...
function getHomepageImages() {
    $images = [];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (is_dir(UPLOADS_HOME_PATH)) {
        if ($handle = opendir(UPLOADS_HOME_PATH)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                    if (in_array($extension, $allowed_extensions)) {
                        $images[] = 'uploads/home/' . $entry;
                    }
                }
            }
            closedir($handle);
        } else {
             error_log("Error: Could not open directory " . UPLOADS_HOME_PATH);
        }
    } else {
         error_log("Error: Directory not found " . UPLOADS_HOME_PATH);
    }
    return $images;
}

function getLatestProductsByBrand($limitPerBrand = 4) {
    $products = loadJson(PRODUCTS_JSON_PATH);
    if (!$products) {
        return [];
    }

    $groupedProducts = [];
    $reversedProducts = array_reverse($products);

    foreach ($reversedProducts as $product) {
        if (isset($product['brand'])) {
            $brand = $product['brand'];
            if (!isset($groupedProducts[$brand])) {
                $groupedProducts[$brand] = [];
            }
            if (count($groupedProducts[$brand]) < $limitPerBrand) {
                if (!isset($product['image_urls']) || !is_array($product['image_urls'])) {
                    $product['image_urls'] = [];
                }
                $groupedProducts[$brand][] = $product;
            }
        }
    }
    return $groupedProducts;
}

// --- URL Encoding Helper ---
function urlEncodeName($name) {
    return rawurlencode($name);
}

?>