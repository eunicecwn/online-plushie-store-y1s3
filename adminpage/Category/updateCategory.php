<?php
require '../../_base.php';

auth(ROLE_ADMIN);

if (is_get()) {
    $categoryID = req('categoryID');

    $stm = $_db->prepare('SELECT * FROM category WHERE categoryID = ? LIMIT 1');
    $stm->execute([$categoryID]);
    $category = $stm->fetch();

    if (!$category) {
        temp('error', 'Category not found');
        redirect('/');
    }

    // Fetch products in this category
    $productsStm = $_db->prepare("
    SELECT p.productID, p.productName,
        (SELECT imageName FROM gallery WHERE productID = p.productID AND is_cover = 1 LIMIT 1) AS productImage
    FROM product p
    JOIN product_category pc ON p.productID = pc.productID
    WHERE pc.categoryID = ?
    ORDER BY p.productName
");
    $productsStm->execute([$categoryID]);
    $currentProducts = $productsStm->fetchAll();

    // Fetch products NOT in this category
    $availableStm = $_db->prepare("
    SELECT p.productID, p.productName,
        (SELECT imageName FROM gallery WHERE productID = p.productID AND is_cover = 1 LIMIT 1) AS productImage
    FROM product p
    WHERE p.productID NOT IN (
        SELECT productID FROM product_category WHERE categoryID = ?
    )
    ORDER BY p.productName
");
    $availableStm->execute([$categoryID]);
    $availableProducts = $availableStm->fetchAll();

    extract((array)$category);
}

if (is_post()) {
    $categoryID = req('categoryID');
    $categoryName = req('categoryName');
    $categoryType = req('categoryType');

    // Handle product assignments
    // In your POST handling section:
    $productsToAdd = req('add_products', []);
    $productsToRemove = req('remove_products', []);

    // Ensure they're arrays even if empty
    $productsToAdd = is_array($productsToAdd) ? $productsToAdd : [];
    $productsToRemove = is_array($productsToRemove) ? $productsToRemove : [];

    // Validation (same as before)
    if ($categoryName == '') {
        $_err['categoryName'] = 'Please enter a category name.';
    } else if (strlen($categoryName) > 50) {
        $_err['categoryName'] = 'Name too long (max 50 chars)';
    }

    if (empty($_err)) {
        $_db->beginTransaction();

        try {
            // Update category
            $stmt = $_db->prepare("UPDATE category SET categoryName = ?, categoryType = ? WHERE categoryID = ?");
            $stmt->execute([$categoryName, $categoryType, $categoryID]);

            // Fetch current products from DB (again, fresh data)
            $stm = $_db->prepare('SELECT productID FROM product_category WHERE categoryID = ?');
            $stm->execute([$categoryID]);
            $currentProductsDB = $stm->fetchAll(PDO::FETCH_COLUMN);

            // Now you have $currentProductsDB = array of productIDs already in category

            // Remove duplicates: if product already in category, no need to re-insert
            $realAddProducts = array_diff($productsToAdd, $currentProductsDB);

            // Remove invalid deletes: only remove if product was originally in the category
            $realRemoveProducts = array_intersect($productsToRemove, $currentProductsDB);

            // Now safe to process
            foreach ($realAddProducts as $productID) {
                $addStm = $_db->prepare("INSERT INTO product_category (productID, categoryID) VALUES (?, ?)");
                $addStm->execute([$productID, $categoryID]);
            }

            foreach ($realRemoveProducts as $productID) {
                $removeStm = $_db->prepare("DELETE FROM product_category WHERE productID = ? AND categoryID = ?");
                $removeStm->execute([$productID, $categoryID]);
            }

            $_db->commit();
            temp('info', 'Category and products updated');
            redirect('/adminpage/Category/myCategory.php');
        } catch (PDOException $e) {
            $_db->rollBack();
            temp('error', 'Update failed: ' . $e->getMessage());
            redirect();
        }
    }
}

$_title = 'KAWAII.SellerCentre | Update Category';
include '../../_headadmin.php';
?>

<style>
    .product-list {
        max-height: 400px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .product-management {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-top: 30px;
        align-items: flex-start;
        /* Make sure it doesn't stretch */
    }

    .product-column {
        background: #fff7fa;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 2px 6px rgba(177, 68, 86, 0.4);
        width: 48%;
        display: inline-block;
        vertical-align: top;
    }

    .product-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        background: #fff0f5;
        border-radius: 4px;
        margin-bottom: 5px;
    }

    .product-item img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
    }

    .product-item button {
        margin-left: 120px;
        background: #f59ca4;
        color: white;
        border: none;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
    }

    .search-box {
        width: 95%;
        padding: 10px;
        border: 1px solid #e58f8f;
        border-radius: 15px;
        font-size: 14px;
        color: #000000;
        background-color: #ffffff;
        transition: border-color 0.5s ease;
        font-family: "Playpen Sans", serif;
        margin-bottom: 10px;
    }

    .form-container {
        margin-top: 120px;
        background-color: #ffffff;
        padding: 40px;
        border-radius: 30px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        max-width: 1000px;
        width: 100%;
        text-align: center;
    }

    .form-input {
        width: 98%;
        padding: 10px;
        border: 1px solid #e58f8f;
        border-radius: 15px;
        font-size: 14px;
        color: #000000;
        background-color: #ffffff;
        transition: border-color 0.5s ease;
        font-family: "Playpen Sans", serif;
    }

    .add-btn-category,
    .remove-btn-category {
        width: 30px;
        /* Ensures a minimum width */
        padding: 8px 20px;
        /* Consistent padding */
        font-size: 14px;
        /* Consistent font size */
        text-align: center;
        border-radius: 4px;
        background-color: #f59ca4;
        color: white;
        display: inline-block;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        font-family: "Playpen Sans", serif;
    }

    .add-btn-category:hover,
    .remove-btn-category:hover {
        background: #d87373;
    }

    .product-item:hover {
        background-color: #ffe6ec;
        transform: scale(1.01);
        transition: all 0.2s ease-in-out;
    }

    .form-actions {
        display: fixed;
        justify-content: space-between;
        padding: 20px 0;
        margin-top: 50px;
        position: sticky;
        bottom: 20px;
        background: white;
        border-top: 1px solid #eee;
        z-index: 100;
    }

    .product-name-category,
    .no-image {
        font-size: 16px;
        text-align: left;
        font-weight: bold;
        margin-bottom: 5px;
        width: 200px;
        /* Set a fixed width */
        white-space: nowrap;
        /* Prevents text from wrapping onto the next line */
        overflow: hidden;
        /* Hides any overflowed text */
        text-overflow: ellipsis;
        /* Adds "..." for overflowing text */
        color: #000000;

    }
</style>


<div class="form-container">
    <h1>Update Category</h1>
    <form action="" method="POST">
        <div class="form-group">
            <label for="categoryID">Category ID</label>
            <?= html_text('categoryID', 'class="form-input" required readonly') ?>
        </div>

        <div class="form-group">
            <label for="categoryName">Category Name</label>
            <?= html_text('categoryName', 'class="form-input" required') ?>
            <?= err('categoryName') ?>
        </div>

        <div class="form-group">
            <label for="categoryType">Category Type</label>
            <?= html_select('categoryType', $_categoryType, null, 'class="form-select" required') ?>
            <?= err('categoryType') ?>
        </div>

        <div class="product-management">
            <div class="product-column">
                <h3>Products in Category</h3>
                <input type="text" class="search-box" placeholder="Search...">
                <div class="product-list" id="current-products">
                    <?php foreach ($currentProducts as $product): ?>
                        <div class="product-item">
                            <?php if ($product->productImage): ?>
                                <img src="/productImage/<?= encode($product->productImage) ?>" alt="<?= encode($product->productName) ?>">
                            <?php else: ?>
                                <span class="no-image">No image</span>
                            <?php endif; ?>
                            <span class="product-name-category"><?= htmlspecialchars($product->productName) ?></span>
                            <input type="hidden" name="remove_products[]" value="<?= $product->productID ?>">
                            <button type="button" class="remove-btn-category" data-id="<?= $product->productID ?>">x</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p>Click x to remove from category</p>
            </div>

            <div class="product-column">
                <h3>Available Products</h3>
                <input type="text" class="search-box" placeholder="Search...">
                <div class="product-list" id="available-products">
                    <?php foreach ($availableProducts as $product): ?>
                        <div class="product-item">
                            <?php if ($product->productImage): ?>
                                <img src="/productImage/<?= encode($product->productImage) ?>" alt="<?= encode($product->productName) ?>">
                            <?php else: ?>
                                <span class="no-image">No image</span>
                            <?php endif; ?>
                            <span class="product-name-category"><?= htmlspecialchars($product->productName) ?></span>
                            <input type="hidden" name="add_products[]" value="<?= $product->productID ?>">
                            <button type="button" class="add-btn-category" data-id="<?= $product->productID ?>">+</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p>Click + to add to category</p>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="form-button">Save Changes</button>
            <button type="reset" class="form-button">Reset</button>
            <a href="/adminpage/Category/myCategory.php" class="form-button">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Store original product assignments
        const originalCurrentProducts = new Set(
            Array.from(document.querySelectorAll('#current-products input[type="hidden"]'))
            .map(input => input.value)
        );

        const originalAvailableProducts = new Set(
            Array.from(document.querySelectorAll('#available-products input[type="hidden"]'))
            .map(input => input.value)
        );

        // Function to move product between lists
        function moveProduct(button, fromListId, toListId, actionType) {
            const productItem = button.closest('.product-item');
            const productId = productItem.querySelector('input[type="hidden"]').value;

            // Move the item visually
            document.getElementById(toListId).appendChild(productItem);

            // Update the button and its handler
            if (actionType === 'add') {
                button.textContent = 'x';
                button.className = 'remove-btn-category';
                button.onclick = function() {
                    moveProduct(this, 'current-products', 'available-products', 'remove');
                };
            } else {
                button.textContent = '+';
                button.className = 'add-btn-category';
                button.onclick = function() {
                    moveProduct(this, 'available-products', 'current-products', 'add');
                };
            }
        }

        // Initialize button handlers
        document.querySelectorAll('.remove-btn-category').forEach(btn => {
            btn.onclick = function() {
                moveProduct(this, 'current-products', 'available-products', 'remove');
            };
        });

        document.querySelectorAll('.add-btn-category').forEach(btn => {
            btn.onclick = function() {
                moveProduct(this, 'available-products', 'current-products', 'add');
            };
        });

        // Before form submission, calculate actual changes
        document.querySelector('form').addEventListener('submit', function(e) {
            // Get current assignments
            const currentProducts = new Set(
                Array.from(document.querySelectorAll('#current-products input[type="hidden"]'))
                .map(input => input.value)
            );

            // Calculate products to add (in current but not in original)
            const productsToAdd = [...currentProducts].filter(
                id => !originalCurrentProducts.has(id)
            );

            // Calculate products to remove (in original but not in current)
            const productsToRemove = [...originalCurrentProducts].filter(
                id => !currentProducts.has(id)
            );

            // Create hidden fields for the actual changes
            const form = this;

            // Clear any existing change fields
            document.querySelectorAll('input[name^="add_products"], input[name^="remove_products"]')
                .forEach(el => el.remove());

            // Add new fields for changes
            productsToAdd.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'add_products[]';
                input.value = id;
                form.appendChild(input);
            });

            productsToRemove.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_products[]';
                input.value = id;
                form.appendChild(input);
            });
        });

        // Search functionality (unchanged)
        document.querySelectorAll('.search-box').forEach(input => {
            input.addEventListener('input', function() {
                const keyword = this.value.toLowerCase();
                const items = this.closest('.product-column').querySelectorAll('.product-item');
                items.forEach(item => {
                    const name = item.querySelector('span').textContent.toLowerCase();
                    item.style.display = name.includes(keyword) ? '' : 'none';
                });
            });
        });
    });
</script>