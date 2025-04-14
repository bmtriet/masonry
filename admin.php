<?php
// *** PHP includes at the top (Correct Order) ***
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php'; // Header now includes DT CSS

$products = getProducts();
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Bảng điều khiển Admin</h1>
<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h2 class="text-xl font-semibold mb-4">Quản lý Sản phẩm</h2>
    <a href="admin_edit_product.php" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4 transition duration-300">
        + Thêm Sản phẩm Mới
    </a>

    <?php if (isset($_SESSION['upload_success'])): ?>
        <p class="text-green-600 bg-green-100 p-3 rounded mb-4"><?= htmlspecialchars($_SESSION['upload_success']) ?></p>
        <?php unset($_SESSION['upload_success']); ?>
    <?php endif; ?>
     <?php if (isset($_SESSION['upload_error'])): ?>
        <p class="text-red-600 bg-red-100 p-3 rounded mb-4"><?= $_SESSION['upload_error'] // Don't escape HTML if errors contain <br> ?></p>
        <?php unset($_SESSION['upload_error']); ?>
    <?php endif; ?>


    <?php if (!empty($products)): ?>
    <div class="overflow-x-auto">
        <!-- Add id="productsTable" -->
        <table id="productsTable" class="min-w-full divide-y divide-gray-200 border">
             <thead class="bg-gray-50">
                <tr>
                    <!-- Column 0 -->
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ảnh</th>
                     <!-- Column 1 (Sortable by default) -->
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên / ID</th>
                     <!-- Column 2 -->
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thương hiệu</th>
                     <!-- Column 3 -->
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                     <!-- Column 4 (Not sortable by default if using columnDefs later) -->
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($products as $product):
                     $primaryImage = getPrimaryImageUrl($product);
                     // Prepend '../' if getPrimaryImageUrl returns paths relative to project root
                     // $displayImage = '../' . $primaryImage;
                     $displayImage = $primaryImage; // Assuming path is correct from web root
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img src="<?= $displayImage ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-12 w-12 object-cover rounded">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($product['id']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($product['brand']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars(number_format((float)($product['price'] ?? 0), 0, ',', '.')) ?> ₫</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="admin_edit_product.php?id=<?= htmlspecialchars($product['id']) ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</a>
                        <a href="admin_delete_product.php?id=<?= htmlspecialchars($product['id']) ?>" class="text-red-600 hover:text-red-900 delete-button">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p class="text-gray-500">Không tìm thấy sản phẩm nào.</p>
    <?php endif; ?>
</div>
<!-- ... other admin tasks ... -->
<?php require_once __DIR__ . '/includes/footer.php'; // Footer now includes DT JS init ?>