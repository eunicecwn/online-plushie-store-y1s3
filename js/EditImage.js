
// ============================================================================
// Enhanced Image Upload with Database Integration - Improved Version
// ============================================================================

document.addEventListener('DOMContentLoaded', function () {
    // Configuration
    const config = {
        maxImages: 9,
        maxSize: 1 * 1024 * 1024, // 1MB
        allowedTypes: ['image/jpeg', 'image/png', 'image/webp'],
        deleteEndpoint: '/adminpage/Product/deleteImage.php',  // Absolute path from root
        productID: document.querySelector('input[name="productID"]')?.value
    };

    // DOM Elements
    const fileInput = document.getElementById('photos');
    const previewContainer = document.getElementById('imagePreview');
    const coverIndexInput = document.getElementById('cover_image_index');
    const imagesToDeleteInput = document.getElementById('imagesToDelete');
    const form = document.querySelector('form');

    // State
    let draggedItem = null;
    let imagesToDelete = [];

    // Initialize
    init();

    // Event Listeners
    fileInput.addEventListener('change', handleFileSelect);
    previewContainer.addEventListener('dragover', handleDragOver);
    previewContainer.addEventListener('drop', handleDrop);
    form.addEventListener('submit', handleFormSubmit);

    // Main Functions
    function init() {
        initExistingImages();
        updateAddButtonVisibility();
    }

    function initExistingImages() {
        const existingImages = previewContainer.querySelectorAll('.preview-item[data-image-id]');

        existingImages.forEach((item, index) => {
            // Ensure proper data attributes
            item.dataset.isNew = 'false';
            item.draggable = true;  // Explicitly make draggable

            // Apply drag events
            applyDragEvents(item);

            // Set first image as cover by default
            if (index === 0) {
                item.classList.add('cover-image');
            }

            // Add loading state for images
            const img = item.querySelector('img');
            if (img) {
                img.onload = () => {
                    img.style.opacity = 1;
                    // Ensure draggable is set after image loads
                    item.draggable = true;
                };
                img.style.opacity = 0;
                img.style.transition = 'opacity 0.3s ease';
                // Force trigger load in case image is cached
                if (img.complete) img.onload();
            }

            // Add delete button event
            const removeBtn = item.querySelector('.remove-btn');
            if (removeBtn) {
                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeImageCard(item);
                });
            }
        });
    }

    function handleFileSelect(e) {
        const files = Array.from(e.target.files);
        const validFiles = files.filter(validateFile);

        if (!validateImageCount(validFiles.length)) return;

        validFiles.forEach(createImageCard);
        updateUI();
    }

    function validateFile(file) {
        if (!config.allowedTypes.includes(file.type)) {
            showError(`Invalid file type: ${file.name}`);
            return false;
        }

        if (file.size > config.maxSize) {
            showError(`File too large: ${file.name} (Max ${formatBytes(config.maxSize)})`);
            return false;
        }

        return true;
    }

    function validateImageCount(newImagesCount) {
        const currentCount = previewContainer.querySelectorAll('.preview-item').length;
        const total = currentCount + newImagesCount;

        if (total > config.maxImages) {
            showError(`Maximum ${config.maxImages} images allowed. You have ${currentCount} and trying to add ${newImagesCount}.`);
            return false;
        }

        return true;
    }

    function createImageCard(file, imageData = null) {
        const card = document.createElement('div');
        card.className = 'preview-item';
        card.draggable = true;

        // Set data attributes
        if (file) {
            card.dataset.filename = file.name;
            card.dataset.isNew = 'true';
        } else if (imageData) {
            card.dataset.imageId = imageData.id;
            card.dataset.isNew = 'false';
        }

        // Create image element
        const img = document.createElement('img');
        if (file) {
            img.src = URL.createObjectURL(file);
            img.onload = () => img.style.opacity = 1;
            img.style.opacity = 0;
            img.style.transition = 'opacity 0.3s ease';
        }

        // Create delete button
        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-btn';
        removeBtn.innerHTML = '×';
        removeBtn.title = 'Delete this image';
        removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            removeImageCard(card);
        });

        // Create position badge
        const positionBadge = document.createElement('span');
        positionBadge.className = 'position-badge';

        // Apply drag events
        applyDragEvents(card);

        // Build card
        card.append(img, removeBtn, positionBadge);

        // Insert before the add button if it exists
        const addBtn = previewContainer.querySelector('.add-btn-container');
        if (addBtn) {
            previewContainer.insertBefore(card, addBtn);
        } else {
            previewContainer.appendChild(card);
        }

        updateUI();
    }

    async function deleteImageFromServer(imageId) {
        if (!config.productID) {
            throw new Error('Product ID is missing');
        }

        const url = `${config.deleteEndpoint}?id=${imageId}&productID=${config.productID}`;
        console.log('Making request to:', url);

        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Delete failed');
        }

        return result;
    }

    async function removeImageCard(card) {
        console.log('Current config:', {
            productID: config.productID,
            imageId: card.dataset.imageId,
            isNew: card.dataset.isNew
        });

        if (!confirm('Delete this image?')) return;

        card.classList.add('deleting');
        const isNew = card.dataset.isNew === 'true';
        const imageId = card.dataset.imageId;

        if (!isNew && imageId) {
            const response = await deleteImageFromServer(imageId);
            console.log('Delete success:', response);
            trackImageForDeletion(imageId);
        }

        card.remove();
        updateUI();
    }

    function trackImageForDeletion(imageId) {
        imagesToDelete.push(imageId);
    }

    function applyDragEvents(element) {
        element.addEventListener('dragstart', handleDragStart);
        element.addEventListener('dragend', handleDragEnd);
        element.addEventListener('dragover', handleDragOverElement);
        element.addEventListener('dragleave', handleDragLeave);
        element.addEventListener('drop', handleDropOnElement);
    }

    // Event Handlers
    function handleDragStart(e) {
        draggedItem = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        setTimeout(() => this.style.opacity = '0.5', 0);
    }

    function handleDragEnd() {
        this.classList.remove('dragging');
        this.style.opacity = '1';
        previewContainer.querySelectorAll('.preview-item').forEach(item => {
            item.classList.remove('drop-target');
        });
    }

    function handleDragOver(e) {
        e.preventDefault();
        previewContainer.classList.add('drop-here');
        e.dataTransfer.dropEffect = 'move';
    }

    function handleDrop(e) {
        e.preventDefault();
        previewContainer.classList.remove('drop-here');

        if (e.dataTransfer.files.length > 0) {
            handleFileSelect({ target: { files: e.dataTransfer.files } });
        }
    }

    function handleDragOverElement(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        if (draggedItem !== this) {
            this.classList.add('drop-target');
        }
    }

    function handleDragLeave() {
        this.classList.remove('drop-target');
    }

    function handleDropOnElement(e) {
        e.preventDefault();
        this.classList.remove('drop-target');

        if (draggedItem !== this) {
            swapElements(draggedItem, this);
            updateUI();
        }
    }

    function handleFormSubmit() {
        imagesToDeleteInput.value = JSON.stringify(imagesToDelete);
    }

    // Helper Functions
    function swapElements(el1, el2) {
        const parent = el1.parentNode;
        const nextSibling = el2.nextSibling === el1 ? el2 : el2.nextSibling;
        parent.insertBefore(el1, el2);
        parent.insertBefore(el2, nextSibling);
    }

    function updateUI() {
        updateFileInput();
        updateCoverStatus();
        updatePositionBadges();
        updateAddButtonVisibility();
        updateImageOrder(); // NEW FUNCTION CALL
    }

    function updateImageOrder() {
        const order = [];
        previewContainer.querySelectorAll('.preview-item').forEach((item, index) => {
            if (item.dataset.imageId) {
                order.push(item.dataset.imageId);
            } else if (item.dataset.filename) {
                order.push(item.dataset.filename);
            }
        });
        document.getElementById('imageOrder').value = JSON.stringify(order);
    }


    function updateFileInput() {
        const dataTransfer = new DataTransfer();

        previewContainer.querySelectorAll('.preview-item').forEach(item => {
            if (item.dataset.isNew === 'true') {
                const file = Array.from(fileInput.files).find(f => f.name === item.dataset.filename);
                if (file) dataTransfer.items.add(file);
            }
        });

        fileInput.files = dataTransfer.files;
    }

    function updateCoverStatus() {
        const items = previewContainer.querySelectorAll('.preview-item');

        items.forEach((item, index) => {
            const isCover = index === 0;
            item.classList.toggle('cover-image', isCover);

            const badge = item.querySelector('.position-badge');
            if (badge) {
                badge.textContent = isCover ? 'Cover' : index + 1;
            }

            if (isCover) {
                if (item.dataset.isNew === 'true') {
                    coverIndexInput.value = item.dataset.filename;
                } else {
                    coverIndexInput.value = item.dataset.imageId;
                }
            }
        });
    }

    function updatePositionBadges() {
        const items = previewContainer.querySelectorAll('.preview-item');

        items.forEach((item, index) => {
            const badge = item.querySelector('.position-badge');
            if (badge) {
                badge.textContent = index === 0 ? 'Cover' : index + 1;
            }
        });
    }

    function updateAddButtonVisibility() {
        const addBtnContainer = previewContainer.querySelector('.add-btn-container');
        if (!addBtnContainer) return;

        const itemCount = previewContainer.querySelectorAll('.preview-item').length;
        addBtnContainer.style.display = itemCount >= config.maxImages ? 'none' : 'flex';
        fileInput.disabled = itemCount >= config.maxImages;
    }

    function showError(message) {
        console.error(message);
        alert(message);
    }

    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
});
