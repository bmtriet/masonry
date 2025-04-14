<?php
// *** PHP includes at the top ***
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php'; // Include header AFTER getting data

$brand = isset($_GET['brand']) ? trim($_GET['brand']) : null;
$products = [];
$pageTitle = "Tất cả Sản phẩm"; // Default Vietnamese title

if ($brand) {
    $products = getProducts($brand);
    $pageTitle = "Giày " . htmlspecialchars($brand); // Vietnamese title
} else {
    $products = getProducts(); // Load all if no brand
}

?>

<h1 class="text-3xl font-bold text-gray-800 mb-6"><?= $pageTitle ?></h1>

<?php if (!empty($products)): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($products as $product):
             $primaryImage = getPrimaryImageUrl($product);
        ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col group">
                <a href="product_detail.php?id=<?= htmlspecialchars($product['id']) ?>" class="block aspect-w-1 aspect-h-1 w-full overflow-hidden">
                     <img src="<?= $primaryImage ?>"
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         class="w-full h-full object-center object-cover group-hover:opacity-75 transition-opacity duration-300">
                </a>
                <div class="p-4 flex flex-col flex-grow">
                    <h2 class="text-lg font-semibold text-gray-800 truncate mb-1" title="<?= htmlspecialchars($product['name']) ?>">
                        <a href="product_detail.php?id=<?= htmlspecialchars($product['id']) ?>" class="hover:text-blue-600">
                            <?= htmlspecialchars($product['name']) ?>
                        </a>
                    </h2>
                    <p class="text-sm text-gray-500 mb-2"><?= htmlspecialchars($product['brand']) ?></p>
                    <!-- Format price to VNĐ -->
                    <p class="text-xl font-bold text-gray-900 mb-3">
                        <?= htmlspecialchars(number_format((float)($product['price'] ?? 0), 0, ',', '.')) ?> ₫
                    </p>
                    <div class="mt-auto flex space-x-2">
                        <a href="product_detail.php?id=<?= htmlspecialchars($product['id']) ?>"
                           class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-3 rounded text-center text-sm transition duration-300">
                            Xem chi tiết
                        </a>
                        <a href="http://m.me/tiemgiaytayninh2?ref=<?= urlEncodeName($product['name']) ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-3 rounded text-center text-sm transition duration-300">
                            Liên hệ
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="text-center text-gray-500">
        <?= $brand ? "Không tìm thấy sản phẩm nào cho thương hiệu \"" . htmlspecialchars($brand) . "\"." : "Không tìm thấy sản phẩm nào." ?>
    </p>
<?php endif; ?>


<?php require_once __DIR__ . '/includes/footer.php'; ?>