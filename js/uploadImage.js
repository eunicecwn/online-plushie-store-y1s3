// ============================================================================
// Enhanced Image Upload with Drag-and-Drop and Cover Selection
// ============================================================================

document.addEventListener('DOMContentLoaded', function () {
    const config = {
        maxImages: 9,
        maxSize: 1 * 1024 * 1024, // 1MB
        allowedTypes: ['image/jpeg', 'image/png', 'image/webp']
    };

    const fileInput = document.getElementById('photos');
    const previewContainer = document.getElementById('imagePreview');
    const coverIndexInput = document.getElementById('cover_image_index') || createHiddenInput();
    let draggedItem = null;

    createAddButton();

    fileInput.addEventListener('change', handleFileSelect);
    previewContainer.addEventListener('dragover', handleContainerDragOver);
    previewContainer.addEventListener('drop', handleContainerDrop);

    function handleFileSelect(e) {
        const files = Array.from(e.target.files).filter(file => {
            if (!config.allowedTypes.includes(file.type)) {
                alert(`Invalid file type: ${file.name}`);
                return false;
            }
            if (file.size > config.maxSize) {
                alert(`File too large: ${file.name}`);
                return false;
            }
            return true;
        });

        if (previewContainer.querySelectorAll('.preview-item').length + files.length > config.maxImages) {
            alert(`Maximum ${config.maxImages} images allowed`);
            return;
        }

        files.forEach(file => {
            createImageCard(file);
        });

        updateFileInput();
        updateCoverStatus();
    }

    function createImageCard(file) {
        const card = document.createElement('div');
        card.className = 'preview-item';
        card.draggable = true;
        card.dataset.filename = file.name;

        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);

        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-btn';
        removeBtn.innerHTML = '×';
        removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            card.remove();
            updateFileInput();
            updateCoverStatus();
            updateAddButton();
        });

        const positionBadge = document.createElement('span');
        positionBadge.className = 'position-badge';

        applyDragEvents(card);

        card.appendChild(img);
        card.appendChild(removeBtn);
        card.appendChild(positionBadge);

        const addBtn = previewContainer.querySelector('.add-btn-container');
        previewContainer.insertBefore(card, addBtn);

        updatePositionBadges();
        updateAddButton();
    }

    function applyDragEvents(card) {
        card.addEventListener('dragstart', (e) => {
            draggedItem = card;
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            setTimeout(() => card.style.opacity = '0.5', 0);
        });

        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
            card.style.opacity = '1';
            previewContainer.querySelectorAll('.preview-item').forEach(item => {
                item.classList.remove('drop-target');
            });
        });

        card.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            if (draggedItem !== card) {
                card.classList.add('drop-target');
            }
        });

        card.addEventListener('dragleave', () => {
            card.classList.remove('drop-target');
        });

        card.addEventListener('drop', (e) => {
            e.preventDefault();
            card.classList.remove('drop-target');

            if (draggedItem !== card) {
                swapElements(draggedItem, card);
                updateFileInput();
                updateCoverStatus();
            }
        });
    }

    function swapElements(el1, el2) {
        const parent = el1.parentNode;
        const nextSibling = el2.nextSibling === el1 ? el2 : el2.nextSibling;

        parent.insertBefore(el1, el2);
        parent.insertBefore(el2, nextSibling);
    }

    function updateCoverStatus() {
        const items = previewContainer.querySelectorAll('.preview-item');
        items.forEach((item, index) => {
            const isCover = index === 0;
            item.classList.toggle('cover-image', isCover);
            item.querySelector('.position-badge').textContent = isCover ? 'Cover' : index + 1;
        });
        coverIndexInput.value = '0';
    }

    function updatePositionBadges() {
        const items = previewContainer.querySelectorAll('.preview-item');
        items.forEach((item, index) => {
            const badge = item.querySelector('.position-badge');
            badge.textContent = index === 0 ? 'Cover' : index + 1;
        });
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        previewContainer.querySelectorAll('.preview-item').forEach(item => {
            const file = Array.from(fileInput.files).find(f => f.name === item.dataset.filename);
            if (file) dataTransfer.items.add(file);
        });
        fileInput.files = dataTransfer.files;
    }

    function createAddButton() {
        const addBtn = document.createElement('div');
        addBtn.className = 'add-btn-container';
        addBtn.innerHTML = `
            <label for="photos" class="add-btn">
                <span>+</span>
                <span>Add Photos</span>
            </label>
        `;
        previewContainer.appendChild(addBtn);
    }

    function updateAddButton() {
        const addBtn = previewContainer.querySelector('.add-btn-container');
        const itemCount = previewContainer.querySelectorAll('.preview-item').length;
        addBtn.style.display = itemCount >= config.maxImages ? 'none' : 'flex';
        fileInput.disabled = itemCount >= config.maxImages;
    }

    function createHiddenInput() {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.id = 'cover_image_index';
        input.name = 'cover_image_index';
        input.value = '0';
        document.querySelector('form').appendChild(input);
        return input;
    }

    function handleContainerDragOver(e) {
        e.preventDefault();
        previewContainer.classList.add('drop-here');
        e.dataTransfer.dropEffect = 'move'; // Fixes the "block" cursor issue
    }

    function handleContainerDrop(e) {
        e.preventDefault();
        previewContainer.classList.remove('drop-here');
        if (e.dataTransfer.files.length > 0) {
            handleFileSelect({ target: { files: e.dataTransfer.files } });
        }
    }
});