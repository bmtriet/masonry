<?php
// *** PHP includes at the top ***
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php'; // Include header AFTER getting data
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Liên hệ Chúng tôi</h1>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-semibold mb-4">Thông tin Liên hệ</h2>

    <p class="mb-3 text-gray-700">Vui lòng liên hệ với chúng tôi qua các phương thức sau:</p>

    <ul class="list-disc list-inside mb-6 text-gray-700 space-y-2">
        <li><strong>Địa chỉ:</strong> [Địa chỉ cửa hàng của bạn], Tây Ninh, Việt Nam (Thay thế)</li>
        <li><strong>Điện thoại:</strong> [Số điện thoại của bạn] (Thay thế)</li>
        <li><strong>Facebook Messenger:</strong> <a href="http://m.me/tiemgiaytayninh2" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">Nhấn vào đây để chat</a></li>
    </ul>

    <!-- Optional Contact Form -->
    <h2 class="text-xl font-semibold mb-4 border-t pt-4 mt-6">Gửi tin nhắn cho chúng tôi (Form mẫu)</h2>
    <form action="#" method="POST" class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Tên của bạn</label>
            <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
         <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email của bạn</label>
            <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
         <div>
            <label for="message" class="block text-sm font-medium text-gray-700">Nội dung tin nhắn</label>
            <textarea name="message" id="message" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
        </div>
        <div>
            <button type="submit" disabled class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                Gửi Tin nhắn (Chưa hoạt động)
            </button>
             <p class="text-xs text-gray-500 mt-1">Lưu ý: Chức năng gửi form hiện đang bị vô hiệu hóa.</p>
        </div>
    </form>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>