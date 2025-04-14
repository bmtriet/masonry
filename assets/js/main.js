document.addEventListener('DOMContentLoaded', function () {
    // Initialize Masonry (if present)
    const grid = document.querySelector('.masonry-grid');
    if (grid) {
        imagesLoaded(grid, function () {
            new Masonry(grid, { /* ... options ... */ });
        });
    }

    // Confirmation for delete buttons
    const deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(button => { /* ... existing listener ... */ });

    // --- NEW: Drag and Drop Image Upload Logic ---
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('product_images'); // The hidden input
    const previewContainer = document.getElementById('image-preview-container');
    const maxFileSize = 5 * 1024 * 1024; // 5MB limit (match PHP)
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    let uploadedFiles = new DataTransfer(); // Use DataTransfer to manage files for the input

    if (dropZone && fileInput && previewContainer) {
        // Click drop zone to trigger file input
        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        // Handle file input changes (from click or drop)
        fileInput.addEventListener('change', handleFiles);

        // Dragover listener
        dropZone.addEventListener('dragover', (event) => {
            event.preventDefault(); // Prevent default browser behavior
            dropZone.classList.add('dragover');
        });

        // Dragleave listener
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        // Drop listener
        dropZone.addEventListener('drop', (event) => {
            event.preventDefault(); // Prevent default browser behavior
            dropZone.classList.remove('dragover');

            const files = event.dataTransfer.files;
            // Add dropped files to our DataTransfer object
            addFilesToDataTransfer(files);
            // Update the hidden file input's files
            fileInput.files = uploadedFiles.files;
             // Trigger preview generation manually after drop
             handleFiles(); // Call the same handler used for 'change'
        });

        function handleFiles() {
            // Get files either from the input event or directly if called after drop
            const files = fileInput.files;
             // Clear previous previews *only for newly selected/dropped files*
             // We rely on PHP to show existing images. This preview is for the *upload* batch.
            previewContainer.innerHTML = ''; // Clear previous upload batch previews
             // Reset DataTransfer for this new selection/drop batch
            uploadedFiles = new DataTransfer();

            if (files.length > 0) {
                // Filter and preview valid files
                Array.from(files).forEach(file => {
                     if (validateFile(file)) {
                         addFilesToDataTransfer([file]); // Add valid file
                        previewFile(file);
                    }
                });
                // Update the hidden file input with *only* the valid files
                 fileInput.files = uploadedFiles.files;
            }
        }

        function addFilesToDataTransfer(files) {
             if (files && files.length) {
                 Array.from(files).forEach(file => {
                     // Check for duplicates based on name and size (simple check)
                     let isDuplicate = false;
                     for (let i = 0; i < uploadedFiles.items.length; i++) {
                         if (uploadedFiles.items[i].kind === 'file') {
                             const existingFile = uploadedFiles.items[i].getAsFile();
                             if (existingFile.name === file.name && existingFile.size === file.size) {
                                 isDuplicate = true;
                                 break;
                             }
                         }
                     }
                     if (!isDuplicate) {
                        uploadedFiles.items.add(file);
                     }
                 });
             }
         }


        function validateFile(file) {
            if (!allowedTypes.includes(file.type)) {
                alert(`Loại file không hợp lệ: ${file.name}. Chỉ chấp nhận JPG, PNG, GIF, WEBP.`);
                return false;
            }
            if (file.size > maxFileSize) {
                alert(`File quá lớn: ${file.name}. Tối đa 5MB.`);
                return false;
            }
            return true;
        }

        function previewFile(file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function() {
                const previewItem = document.createElement('div');
                previewItem.classList.add('preview-item');

                const img = document.createElement('img');
                img.src = reader.result;
                img.alt = `Xem trước: ${file.name}`;
                previewItem.appendChild(img);

                const fileNameSpan = document.createElement('span');
                fileNameSpan.classList.add('text-xs', 'mt-1', 'truncate', 'w-20', 'text-center');
                fileNameSpan.textContent = file.name;
                previewItem.appendChild(fileNameSpan);

                 // Add a remove button for the preview
                 const removeBtn = document.createElement('button');
                 removeBtn.type = 'button'; // Prevent form submission
                 removeBtn.innerHTML = '&times;'; // 'X' symbol
                 removeBtn.classList.add('remove-preview');
                 removeBtn.title = 'Xóa ảnh này khỏi danh sách tải lên';
                 removeBtn.onclick = function() {
                     removeFileFromDataTransfer(file); // Remove from DataTransfer
                     fileInput.files = uploadedFiles.files; // Update the input's file list
                     previewItem.remove(); // Remove the preview element
                 };
                 previewItem.appendChild(removeBtn);


                previewContainer.appendChild(previewItem);
            }
        }

         function removeFileFromDataTransfer(fileToRemove) {
             const newFiles = new DataTransfer();
             for (let i = 0; i < uploadedFiles.items.length; i++) {
                 if (uploadedFiles.items[i].kind === 'file') {
                     const file = uploadedFiles.items[i].getAsFile();
                     // Keep files that don't match the one to remove
                     if (!(file.name === fileToRemove.name && file.size === fileToRemove.size)) {
                         newFiles.items.add(file);
                     }
                 }
             }
             uploadedFiles = newFiles; // Replace the old DataTransfer object
         }
    }
    // --- End Drag and Drop Logic ---

}); // End DOMContentLoaded

// Mobile Menu Toggle (if needed globally)
function toggleMobileMenu() { /* ... existing code ... */ }

// Image Detail Change (if needed globally)
function changeMainImage(newImageUrl, clickedThumbnail) { /* ... existing code ... */ }