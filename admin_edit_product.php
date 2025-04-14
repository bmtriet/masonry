<?php
// *** PHP includes at the top (Correct Order) ***
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/functions.php';

$product_id = $_GET['id'] ?? null;
$product = null;
$pageTitle = "Thêm Sản phẩm Mới";
$formAction = "admin_process_product.php";
$existing_image_urls = []; // Initialize

if ($product_id) {
    $product = getProductById($product_id);
    if ($product) {
        $pageTitle = "Sửa Sản phẩm: " . htmlspecialchars($product['name']);
        $existing_image_urls = $product['image_urls'] ?? [];
    } else {
        // Nên thêm thông báo lỗi thân thiện hơn hoặc chuyển hướng
        die("Lỗi: Sản phẩm không tồn tại!");
    }
}

// Pre-fill form data or set defaults
$formData = [
    'id' => $product['id'] ?? '',
    'name' => $product['name'] ?? '',
    'brand' => $product['brand'] ?? '', // Sẽ được dùng để chọn 'selected'
    'price' => $product['price'] ?? '',
    'description' => $product['description'] ?? ''
];

// Include header AFTER potentially setting $pageTitle or getting product data
require_once __DIR__ . '/includes/header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6"><?= $pageTitle ?></h1>

<div class="bg-white p-6 rounded-lg shadow-md">
    <?php // Display potential upload feedback messages
        if (isset($_SESSION['upload_error'])) {
            echo '<div class="mb-4 p-3 bg-red-100 text-red-700 rounded border border-red-300">' . $_SESSION['upload_error'] . '</div>';
            unset($_SESSION['upload_error']); // Clear message after displaying
        }
        // You might want a success message here too if needed
        // if (isset($_SESSION['upload_success'])) { ... }
    ?>
    <form action="<?= $formAction ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php if ($product_id): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($formData['id']) ?>">
        <?php endif; ?>

        <!-- Product Name -->
        <div>
             <label for="name" class="block text-sm font-medium text-gray-700">Tên Sản phẩm</label>
             <input type="text" name="name" id="name" value="<?= htmlspecialchars($formData['name']) ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

         <!-- Brand Dropdown (FIXED) -->
         <div>
             <label for="brand" class="block text-sm font-medium text-gray-700">Thương hiệu</label>
             <select name="brand" id="brand" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                 <option value="">-- Chọn Thương hiệu --</option> <!-- Add a default empty option -->
                 <?php
                    // Define the list of brands (can be dynamic later if needed)
                    $brands = ["Nike", "Adidas", "Asics", "New Balance", "Under Armour", "MLB", "Hoka", "Champion", "Other"]; // Added Hoka, Champion based on context
                    // Loop through brands to create options
                    foreach ($brands as $b) {
                        // Check if this brand matches the current product's brand (case-insensitive)
                        $selected = (isset($formData['brand']) && strcasecmp($formData['brand'], $b) == 0) ? 'selected' : '';
                        // Output the option tag
                        echo "<option value=\"".htmlspecialchars($b)."\" $selected>".htmlspecialchars($b)."</option>";
                    }
                 ?>
             </select>
        </div>

        <!-- Price -->
         <div>
             <label for="price" class="block text-sm font-medium text-gray-700">Giá (VNĐ)</label>
             <input type="number" name="price" id="price" value="<?= htmlspecialchars($formData['price']) ?>" required step="1" min="0" placeholder="Ví dụ: 1200000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
         </div>


         <!-- Current Images Section (for Editing) -->
         <?php if ($product_id && !empty($existing_image_urls)): ?>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Ảnh hiện tại</label>
                <div class="flex flex-wrap gap-4">
                    <?php foreach ($existing_image_urls as $index => $url):
                        $fullUrl = '../' . $url; // Adjust path relative to current script
                        if (!file_exists($fullUrl)) $fullUrl = $url; // Try path from web root
                         if (!file_exists($fullUrl)) {
                             error_log("Admin Edit: Image file not found at path: " . $fullUrl . " (Original URL: $url)");
                             continue; // Skip if file doesn't exist
                         }
                    ?>
                        <div class="relative border p-1 rounded">
                            <img src="<?= htmlspecialchars($fullUrl) ?>" alt="Ảnh hiện tại <?= $index + 1 ?>" class="h-20 w-20 object-cover">
                            <div class="mt-1 text-center">
                                <input type="checkbox" name="delete_images[]" value="<?= htmlspecialchars($url) ?>" id="delete_img_<?= $index ?>" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <label for="delete_img_<?= $index ?>" class="ml-1 text-xs text-red-700">Xóa</label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                 <input type="hidden" name="existing_images_json" value="<?= htmlspecialchars(json_encode($existing_image_urls)) ?>">
            </div>
         <?php endif; ?>


         <!-- Drag & Drop Image Upload Section -->
         <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                 <?= ($product_id && !empty($existing_image_urls)) ? 'Thêm ảnh mới' : 'Tải ảnh lên' ?> (Kéo thả hoặc Nhấn vào)
            </label>
            <!-- The Drop Zone -->
            <div id="drop-zone">
                <p class="text-gray-500">Kéo & thả file ảnh vào đây, hoặc nhấn để chọn file.</p>
                 <p class="text-xs text-gray-400 mt-1">Cho phép: JPG, PNG, GIF, WEBP (Tối đa 5MB mỗi ảnh)</p>
            </div>
            <!-- Visually Hidden File Input -->
            <input type="file" name="product_images[]" id="product_images" multiple accept="image/jpeg, image/png, image/gif, image/webp" class="hidden">

             <!-- Image Preview Area -->
            <div id="image-preview-container" class="mt-4 flex flex-wrap gap-2">
                 <!-- Previews will be added here by JavaScript -->
            </div>
        </div>


        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả (Tùy chọn)</label>
            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?= htmlspecialchars($formData['description']) ?></textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center space-x-4 pt-4 border-t">
             <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <?= $product_id ? 'Cập nhật Sản phẩm' : 'Thêm Sản phẩm' ?>
            </button>
             <a href="admin.php" class="text-sm text-gray-600 hover:underline">Hủy bỏ</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; // Footer includes JS ?>