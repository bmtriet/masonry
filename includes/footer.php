</main> <!-- End main container -->

<footer class="bg-gray-700 text-gray-300 text-center p-4 mt-12">
    &copy; <?= date('Y') ?> Masonry Shoes. Bản quyền đã được đăng ký.
    <?php if (!isLoggedIn()): ?>
        <a href="login.php" class="text-xs text-gray-400 hover:text-white ml-4">[Đăng nhập Admin]</a>
    <?php endif; ?>
</footer>

<!-- jQuery (must come before DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<!-- DataTables JavaScript -->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- Optional: DataTables Tailwind Theme JS (if using Tailwind theme CSS) -->
<!-- <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script> -->


<!-- Main JS File -->
<script src="assets/js/main.js"></script>

<!-- DataTables Initialization (and other page-specific JS) -->
<script>
    $(document).ready(function() {
        // Check if the products table exists before initializing
        if ($('#productsTable').length) {
            $('#productsTable').DataTable({
                // Default sort by the second column (Name/ID) descending
                // Adjust column index if your table structure changes (index starts at 0)
                "order": [[1, "desc"]],
                "language": { // Vietnamese language settings
                    "decimal":        "",
                    "emptyTable":     "Không có dữ liệu trong bảng",
                    "info":           "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                    "infoEmpty":      "Hiển thị 0 đến 0 của 0 mục",
                    "infoFiltered":   "(được lọc từ _MAX_ tổng số mục)",
                    "infoPostFix":    "",
                    "thousands":      ".", // Vietnamese thousands separator
                    "lengthMenu":     "Hiển thị _MENU_ mục",
                    "loadingRecords": "Đang tải...",
                    "processing":     "Đang xử lý...",
                    "search":         "Tìm kiếm:",
                    "zeroRecords":    "Không tìm thấy mục nào phù hợp",
                    "paginate": {
                        "first":      "Đầu",
                        "last":       "Cuối",
                        "next":       "Tiếp",
                        "previous":   "Trước"
                    },
                    "aria": {
                        "sortAscending":  ": kích hoạt để sắp xếp cột tăng dần",
                        "sortDescending": ": kích hoạt để sắp xếp cột giảm dần"
                    }
                }
                // Optional: Disable sorting on specific columns if needed
                // "columnDefs": [
                //   { "orderable": false, "targets": [0, 4] } // Disable sorting for Image (0) and Actions (4)
                // ]
            });
        }
    });

    // Image detail change function (from previous step)
    function changeMainImage(newImageUrl, clickedThumbnail) {
        const mainImage = document.getElementById('main-product-image');
        if (mainImage) { mainImage.src = newImageUrl; }
        const allThumbnails = document.querySelectorAll('.product-thumbnail');
        allThumbnails.forEach(thumb => {
            thumb.classList.remove('thumbnail-active');
            thumb.classList.add('thumbnail-inactive');
        });
        if (clickedThumbnail) {
            clickedThumbnail.classList.remove('thumbnail-inactive');
            clickedThumbnail.classList.add('thumbnail-active');
        }
    }
</script>

</body>
</html>