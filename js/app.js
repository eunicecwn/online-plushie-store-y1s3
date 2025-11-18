// ============================================================================
// Product Form Initialization
// ============================================================================
// Main initialization when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    try {
        initCategoryDropdown();
        initImageUploadPreview();
    } catch (error) {
        console.error('Initialization error:', error);
    }
});

// ============================================================================
// Form Utility Functions
// ============================================================================

$(() => {

    // Autofocus
    $('form :input:not(button):first').focus();
    $('.err:first').prev().focus();
    $('.err:first').prev().find(':input:first').focus();
    
    // Confirmation message
    // e.target.dataset.confirm --> dataconfirm = "hello" --> if dont have value means use are you sure 
    // https://www.w3schools.com/jsref/met_win_confirm.asp
    // javascript dialog box 
    // alert = show message to user
    // confirm = a confirmation from user
    // prompt = prompt user an input

    $('[data-confirm]').on('click', e => {
        const text = e.target.dataset.confirm || 'Are you sure?';
        if (!confirm(text)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    });

    // Initiate GET request
    $('[data-get]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.get;
        location = url || location;
    });

    // Initiate POST request
    $('[data-post]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.post;
        const f = $('<form>').appendTo(document.body)[0];
        f.method = 'POST';
        f.action = url || location;
        f.submit();
    });

    // Reset form
    $('[type=reset]').on('click', e => {
        e.preventDefault();
        location = location;
    });

    // Auto uppercase
    $('[data-upper]').on('input', e => {
        const a = e.target.selectionStart;
        const b = e.target.selectionEnd;
        e.target.value = e.target.value.toUpperCase();
        e.target.setSelectionRange(a, b);
    });

});

// ============================================================================
// Category Dropdown
// ============================================================================

function initCategoryDropdown() {
    const categoryTypeSelect = document.getElementById("categoryType");
    const categoryNameSelect = document.getElementById("categoryID");

    if (!categoryTypeSelect || !categoryNameSelect) {
        console.warn('Category dropdown elements not found');
        return;
    }

    categoryTypeSelect.addEventListener("change", function() {
        const type = this.value;
        categoryNameSelect.innerHTML = '<option value="">Loading...</option>';
        categoryNameSelect.disabled = true;

        if (!type) {
            categoryNameSelect.innerHTML = '<option value="">Select Type First</option>';
            return;
        }

        fetch(`/adminpage/Product/addProduct.php?type=${encodeURIComponent(type)}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(categories => {
                let options = '<option value="">Select Name</option>';
                
                if (categories.length === 0) {
                    options = '<option value="" disabled>No categories available</option>';
                } else {
                    categories.forEach(cat => {
                        options += `<option value="${cat.categoryID}">${cat.categoryName}</option>`;
                    });
                }

                categoryNameSelect.innerHTML = options;
                categoryNameSelect.disabled = false;
            })
            .catch(error => {
                console.error("Error fetching categories:", error);
                categoryNameSelect.innerHTML = '<option value="" disabled>Error loading categories</option>';
            });
    });
}

function updateCategories() {
    const categoryType = document.getElementById('categoryType').value;
    const categorySelect = document.getElementById('categoryID');
    
    if (!categoryType) {
        categorySelect.innerHTML = '<option value="">Select Type First</option>';
        return;
    }

    categorySelect.innerHTML = '<option value="">Loading...</option>';
    categorySelect.disabled = true;

    fetch(`/adminpage/Product/addProduct.php?type=${encodeURIComponent(categoryType)}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(categories => {
            let options = '<option value="">Select Category</option>';
            categories.forEach(cat => {
                options += `<option value="${cat.categoryID}">${cat.categoryName}</option>`;
            });
            categorySelect.innerHTML = options;
            categorySelect.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            categorySelect.innerHTML = '<option value="">Error loading categories</option>';
            categorySelect.disabled = false;
        });
}


