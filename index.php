<?php
// *** PHP includes at the top ***
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php'; // Include header AFTER getting data

$homeImages = getHomepageImages();
$latestProductsByBrand = getLatestProductsByBrand(4);

?>

<h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Chào mừng đến với Cửa hàng Giày</h1>

<!-- Masonry Gallery -->
<?php if (!empty($homeImages)): ?>
    <div class="masonry-grid mb-12" data-masonry='{ "itemSelector": ".grid-item", "columnWidth": ".grid-item", "percentPosition": true, "gutter": 15 }'>
        <?php foreach ($homeImages as $imagePath): ?>
            <div class="grid-item bg-white rounded shadow overflow-hidden">
                <img src="<?= htmlspecialchars($imagePath) ?>" alt="Ảnh trang chủ" class="w-full h-auto block">
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<!-- Latest Products by Brand -->
<div class="space-y-12">
    <?php if (empty($latestProductsByBrand)): ?>
         <p class="text-center text-gray-500">Không có sản phẩm nào để hiển thị.</p>
    <?php else: ?>
        <?php foreach ($latestProductsByBrand as $brand => $products): ?>
            <section>
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h2 class="text-2xl font-semibold text-gray-700"><?= htmlspecialchars($brand) ?></h2>
                    <a href="products.php?brand=<?= urlencode($brand) ?>" class="text-sm text-blue-600 hover:underline font-medium">
                        Xem tất cả sản phẩm <?= htmlspecialchars($brand) ?> &rarr;
                    </a>
                </div>
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
                                <h3 class="text-md font-semibold text-gray-800 truncate mb-1" title="<?= htmlspecialchars($product['name']) ?>">
                                    <a href="product_detail.php?id=<?= htmlspecialchars($product['id']) ?>" class="hover:text-blue-600">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </a>
                                </h3>
                                <!-- Format price to VNĐ -->
                                <p class="text-lg font-bold text-gray-900 mb-3">
                                    <?= htmlspecialchars(number_format((float)($product['price'] ?? 0), 0, ',', '.')) ?> ₫
                                </p>
                                <div class="mt-auto">
                                    <a href="product_detail.php?id=<?= htmlspecialchars($product['id']) ?>"
                                       class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-3 rounded text-center text-sm transition duration-300">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
     <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>