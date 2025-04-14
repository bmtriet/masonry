<?php
// Assumes functions.php and auth.php are loaded before this in main files
$navItems = loadJson(NAV_JSON_PATH);
$current_page = basename($_SERVER['PHP_SELF']);
$current_brand = isset($_GET['brand']) ? $_GET['brand'] : null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masonry Shoes - Cửa hàng Giày</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Optional: DataTables Tailwind Theme CSS (if you prefer Tailwind styling) -->
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css"> -->

    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script src="https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js"></script>

    <style type="text/tailwindcss">
        .nav-link { @apply px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white; }
        .nav-link-active { @apply bg-gray-900 text-white; }
        .dropdown .dropdown-menu { @apply mt-0; /* Closer dropdown */ }
        .dropdown:hover .dropdown-menu { display: block; }
        .dropdown-menu { display: none; }
        .grid-item { width: 23%; margin-bottom: 15px; }
        @media (max-width: 1024px) { .grid-item { width: 31%; } }
        @media (max-width: 768px) { .grid-item { width: 48%; } }
        @media (max-width: 480px) { .grid-item { width: 98%; } }
        .thumbnail-active { @apply border-blue-500; }
        .thumbnail-inactive { @apply border-transparent hover:border-blue-300; }

        /* Drag and Drop Styles */
        #drop-zone {
            @apply border-2 border-dashed border-gray-400 rounded-lg p-6 text-center cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors;
        }
        #drop-zone.dragover {
            @apply border-indigo-500 bg-indigo-50;
        }
        #image-preview-container .preview-item {
            @apply inline-flex flex-col items-center p-1 border rounded relative mr-2 mb-2;
        }
         #image-preview-container .preview-item img {
             @apply h-20 w-20 object-cover;
         }
        #image-preview-container .preview-item .remove-preview {
            @apply absolute -top-2 -right-2 bg-red-500 text-white rounded-full h-5 w-5 flex items-center justify-center text-xs cursor-pointer hover:bg-red-700;
        }

        /* DataTables Search Box - Basic Tailwind Style */
        div.dataTables_wrapper div.dataTables_filter input {
             @apply ml-2 p-1 border border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50;
        }
         div.dataTables_wrapper div.dataTables_length select {
            @apply ml-2 mr-2 p-1 border border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50;
         }

    </style>
     <script>
        function toggleMobileMenu() { /* ... existing code ... */ }
    </script>
</head>
<body class="bg-gray-100 font-sans">

<nav class="bg-gray-800">
    <!-- ... existing nav structure ... -->
     <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div class="relative flex h-16 items-center justify-between">
          <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
            <!-- Mobile menu button-->
            <button type="button" onclick="toggleMobileMenu()" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
              <span class="sr-only">Mở menu chính</span>
              <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
              <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
          <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
            <div class="flex flex-shrink-0 items-center">
              <a href="index.php" class="text-white text-lg font-bold">ShoeStore</a>
            </div>
            <div class="hidden sm:ml-6 sm:block">
              <div class="flex space-x-4">
                <?php foreach ($navItems as $item): ?>
                    <?php
                        $itemName = $item['name'];
                        if ($itemName == 'Home') $itemName = 'Trang chủ';
                        if ($itemName == 'Brand') $itemName = 'Thương hiệu';
                        if ($itemName == 'Contact') $itemName = 'Liên hệ';
                    ?>
                    <?php if (isset($item['submenu'])): ?>
                        <div class="relative dropdown">
                            <a href="#" class="nav-link inline-flex items-center"> <!-- Active state logic simplified -->
                                <?= htmlspecialchars($itemName) ?>
                                <svg class="ml-1 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                            </a>
                            <div class="dropdown-menu absolute z-10 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical">
                                <?php foreach ($item['submenu'] as $subItem): ?>
                                    <a href="<?= htmlspecialchars($subItem['url']) ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">
                                        <?= htmlspecialchars($subItem['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                         <?php $isItemActive = ($current_page == basename($item['url'])); /* Simplified active check */ ?>
                        <a href="<?= htmlspecialchars($item['url']) ?>" class="nav-link <?= $isItemActive ? 'nav-link-active' : ''; ?>">
                            <?= htmlspecialchars($itemName) ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
            <?php if (isLoggedIn()): ?>
                <a href="admin.php" class="nav-link <?= ($current_page == 'admin.php' || strpos($current_page, 'admin_') === 0) ? 'nav-link-active' : ''; ?>">Quản trị</a>
                <a href="logout.php" class="nav-link">Đăng xuất</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <!-- Mobile menu -->
      <div class="sm:hidden hidden" id="mobile-menu"> <!-- ... existing mobile menu structure ... --> </div>
</nav>

<main class="container mx-auto max-w-7xl px-4 py-8">