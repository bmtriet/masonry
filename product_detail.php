<?php
// *** PHP includes at the top ***
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$product_id = $_GET['id'] ?? null;
$product = null;
$pageTitle = "Chi tiết Sản phẩm"; // Default Vietnamese title

if ($product_id) {
    $product = getProductById($product_id);
    if ($product) {
        $pageTitle = htmlspecialchars($product['name']); // Title is product name
    }
}

require_once __DIR__ . '/includes/header.php'; // Include header after getting data

if (!$product): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Lỗi!</strong>
        <span class="block sm:inline">Sản phẩm không tồn tại hoặc ID không hợp lệ.</span>
    </div>
<?php else:
    // Use the function to reliably get the first valid image URL
    $primaryImage = getPrimaryImageUrl($product, 'uploads/products/placeholder_shoe.jpg');
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Image Gallery -->
        <div>
            <div class="mb-4 border rounded overflow-hidden">
                <img id="main-product-image" src="<?= $primaryImage ?>" alt="<?= htmlspecialchars($product['name']) ?> - Ảnh chính" class="w-full h-auto object-contain max-h-96">
            </div>
            <?php // Check if there are images to display as thumbnails
                $imageUrls = $product['image_urls'] ?? [];
                if (!empty($imageUrls)):
            ?>
                <div class="flex space-x-2 overflow-x-auto pb-2">
                    <?php foreach ($imageUrls as $index => $imageUrl):
                        $thumbPath = htmlspecialchars($imageUrl);
                         // Optional: Check if thumbnail file exists before displaying
                         // if (!file_exists(__DIR__ . '/../' . $imageUrl)) continue;

                         // Determine if this thumbnail is the currently selected primary image
                         $isActive = ($imageUrl === $primaryImage);
                    ?>
                        <img src="<?= $thumbPath ?>" alt="<?= htmlspecialchars($product['name']) ?> - Thumbnail <?= $index + 1 ?>"
                             class="h-20 w-20 object-cover rounded border-2 cursor-pointer product-thumbnail <?= $isActive ? 'thumbnail-active' : 'thumbnail-inactive' ?>"
                             onclick="changeMainImage('<?= $thumbPath ?>', this)">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="text-lg text-gray-600 mb-4"><?= htmlspecialchars($product['brand']) ?></p>
            <!-- Format price to VNĐ -->
            <p class="text-3xl font-extrabold text-gray-900 mb-6">
                <?= htmlspecialchars(number_format((float)($product['price'] ?? 0), 0, ',', '.')) ?> ₫
            </p>

            <?php if (!empty($product['description'])): ?>
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">Mô tả</h2>
                    <p class="text-gray-600 whitespace-pre-line"><?= htmlspecialchars($product['description']) ?></p>
                </div>
            <?php endif; ?>

            <a href="http://m.me/tiemgiaytayninh2?ref=<?= urlEncodeName($product['name']) ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-block w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded text-center transition duration-300">
                Liên hệ qua Messenger
            </a>

             <a href="javascript:history.back()" class="inline-block w-full sm:w-auto mt-3 sm:mt-0 sm:ml-4 text-gray-600 hover:underline py-3 px-6 rounded text-center">
                Quay lại Sản phẩm
            </a>
        </div>
    </div>
</div>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>