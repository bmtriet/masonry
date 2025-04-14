<?php
// *** LUÔN GỌI ĐẦU TIÊN ***
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/functions.php';

// Ensure uploads directory exists and is writable
if (!is_dir(UPLOADS_PRODUCTS_PATH)) {
    if (!mkdir(UPLOADS_PRODUCTS_PATH, 0775, true)) {
        $_SESSION['upload_error'] = "Error: Failed to create base products upload directory.";
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'admin_edit_product.php'); // Redirect back
        exit;
    }
}
if (!is_writable(UPLOADS_PRODUCTS_PATH)) {
     $_SESSION['upload_error'] = "Error: Base products upload directory is not writable.";
     header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'admin_edit_product.php'); // Redirect back
     exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id = $_POST['id'] ?? null; // Get ID if it's an update
    $is_new_product = empty($product_id);

    // If it's a new product, generate ID *now* to use for folder path
    if ($is_new_product) {
         $product_id = 'prod_' . uniqid();
    }

    // --- Image Handling ---
    $finalImageUrls = [];
    $uploadErrors = [];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB limit (adjust as needed)

    // 1. Determine existing images (if any)
    $existingImages = [];
    if (!$is_new_product && isset($_POST['existing_images_json'])) {
        $decoded = json_decode($_POST['existing_images_json'], true);
        if (is_array($decoded)) {
            $existingImages = $decoded;
        }
    }

    // 2. Handle deletions
    $imagesToDelete = $_POST['delete_images'] ?? [];
    $keptImages = [];
    $deletedFilesCount = 0;
    foreach ($existingImages as $url) {
        if (in_array($url, $imagesToDelete)) {
            // Delete the actual file
            $fullPath = UPLOADS_PATH . '../' . $url; // Path relative to project root
            if (file_exists($fullPath)) {
                if (unlink($fullPath)) {
                    $deletedFilesCount++;
                    error_log("Admin deleted image: " . $fullPath);
                } else {
                    error_log("Failed to delete image file: " . $fullPath);
                    $uploadErrors[] = "Could not delete file: " . basename($url);
                    $keptImages[] = $url; // Keep it in the list if deletion failed
                }
            } else {
                 error_log("Tried to delete non-existent file: " . $fullPath); // File might already be gone
            }
        } else {
            // Keep the image URL if not marked for deletion
            $keptImages[] = $url;
        }
    }
    $finalImageUrls = $keptImages; // Start with the images that were kept

    // 3. Handle Uploads
    $newlyUploadedPaths = [];
    if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {

        // Define and create product-specific upload directory
        $productUploadDir = UPLOADS_PRODUCTS_PATH . $product_id . '/'; // e.g., uploads/products/prod_xxxxxx/
        $relativeProductUploadDir = 'uploads/products/' . $product_id . '/'; // Path to store in JSON

        if (!is_dir($productUploadDir)) {
            if (!mkdir($productUploadDir, 0775, true)) { // Create recursive
                $uploadErrors[] = "Error creating product directory. Check permissions.";
                // Don't proceed with uploads if dir creation fails
                 goto process_product_data; // Jump to saving other data
            }
        }
        if (!is_writable($productUploadDir)) {
             $uploadErrors[] = "Product directory is not writable.";
             goto process_product_data; // Jump to saving other data
        }

        $fileCount = count($_FILES['product_images']['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            // Check for individual file upload errors
            if ($_FILES['product_images']['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = $_FILES['product_images']['name'][$i];
                $fileTmpName = $_FILES['product_images']['tmp_name'][$i];
                $fileSize = $_FILES['product_images']['size'][$i];
                $fileType = $_FILES['product_images']['type'][$i]; // More reliable than finfo sometimes needed

                // Validate file type
                if (!in_array($fileType, $allowedTypes)) {
                    $uploadErrors[] = "Invalid file type: " . htmlspecialchars($fileName);
                    continue; // Skip this file
                }

                // Validate file size
                if ($fileSize > $maxFileSize) {
                     $uploadErrors[] = "File too large: " . htmlspecialchars($fileName);
                     continue; // Skip this file
                }

                // Generate unique filename
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $safeBaseName = preg_replace("/[^a-zA-Z0-9_-]/", "_", pathinfo($fileName, PATHINFO_FILENAME)); // Make filename safer
                $newFileName = $safeBaseName . '_' . uniqid() . '.' . $fileExtension;
                $destination = $productUploadDir . $newFileName;
                $relativeDestination = $relativeProductUploadDir . $newFileName; // Path for JSON

                // Move the uploaded file
                if (move_uploaded_file($fileTmpName, $destination)) {
                    $newlyUploadedPaths[] = $relativeDestination;
                } else {
                    $uploadErrors[] = "Failed to move uploaded file: " . htmlspecialchars($fileName);
                     error_log("Failed to move uploaded file '$fileName' to '$destination'");
                }

            } elseif ($_FILES['product_images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                // Report errors other than "no file uploaded"
                 $uploadErrors[] = "Upload error for file " . ($i+1) . ": Error code " . $_FILES['product_images']['error'][$i];
                  error_log("Upload error for file " . ($i+1) . " (name: ".$_FILES['product_images']['name'][$i]."): Code " . $_FILES['product_images']['error'][$i]);
            }
        }
    }

    // 4. Combine kept existing URLs and newly uploaded URLs
    $finalImageUrls = array_merge($finalImageUrls, $newlyUploadedPaths);
    $finalImageUrls = array_unique($finalImageUrls); // Remove potential duplicates


    // --- Process Other Product Data ---
    process_product_data: // Label for goto jump on critical dir error

    $productData = [
        'name'        => trim($_POST['name'] ?? ''),
        'brand'       => trim($_POST['brand'] ?? ''),
        'price'       => filter_var($_POST['price'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
        'image_urls'  => $finalImageUrls, // Use the processed list
        'description' => trim($_POST['description'] ?? '')
    ];

    // Assign the generated ID if it's a new product
    if ($is_new_product) {
         $productData['id'] = $product_id;
    }


    $success = false;
    if (!$is_new_product) {
        // --- Update existing product ---
        if (empty($productData['name']) || empty($productData['brand']) || !is_numeric($productData['price'])) {
             $_SESSION['upload_error'] = "Missing required product fields for update."; // Use session for feedback
             header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'admin_edit_product.php?id='.$product_id);
             exit;
        }
        $success = updateProduct($product_id, $productData);
         if (!$success) {
             error_log("Failed to update product ID: " . $product_id);
              $_SESSION['upload_error'] = "Database error while updating product.";
         }

    } else {
        // --- Add new product ---
        if (empty($productData['name']) || empty($productData['brand']) || !is_numeric($productData['price'])) {
            $_SESSION['upload_error'] = "Missing required product fields for adding."; // Use session for feedback
            // Attempt cleanup of created folder/files if add fails early
            if (!empty($newlyUploadedPaths)) { /* ... add cleanup logic ... */ }
            if (is_dir(UPLOADS_PRODUCTS_PATH . $product_id)) { rmdir(UPLOADS_PRODUCTS_PATH . $product_id); } // Basic cleanup

            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'admin_edit_product.php');
            exit;
        }
        $success = addProduct($productData); // Add product with its new ID
         if (!$success) {
             error_log("Failed to add new product.");
             $_SESSION['upload_error'] = "Database error while adding product.";
             // Attempt cleanup
             if (!empty($newlyUploadedPaths)) { /* ... add cleanup logic ... */ }
             if (is_dir(UPLOADS_PRODUCTS_PATH . $product_id)) { rmdir(UPLOADS_PRODUCTS_PATH . $product_id); }
         }
    }

    // --- Redirect with Feedback ---
    $redirectUrl = 'admin.php'; // Default redirect

    if ($success) {
         $successMsg = $is_new_product ? 'Product added successfully.' : 'Product updated successfully.';
         if(count($newlyUploadedPaths) > 0) $successMsg .= ' Added ' . count($newlyUploadedPaths) . ' new image(s).';
         if($deletedFilesCount > 0) $successMsg .= ' Deleted ' . $deletedFilesCount . ' image(s).';
         if(!empty($uploadErrors)) $successMsg .= ' Some image uploads failed (see errors below).';
         // Store success message and errors (if any) in session
         $_SESSION['upload_success'] = $successMsg;
         if (!empty($uploadErrors)) {
              $_SESSION['upload_error'] = implode("<br>", array_map('htmlspecialchars', $uploadErrors));
              $redirectUrl = $_SERVER['HTTP_REFERER'] ?? 'admin_edit_product.php?id='.$product_id; // Redirect back to edit page if errors occurred
         }
    } else {
        // Error message already set in session above
         $redirectUrl = $_SERVER['HTTP_REFERER'] ?? ($is_new_product ? 'admin_edit_product.php' : 'admin_edit_product.php?id='.$product_id); // Redirect back to form on failure
    }

    header('Location: ' . $redirectUrl);
    exit;


} else {
    // If accessed directly without POST, redirect away
    header('Location: admin.php');
    exit;
}
?>