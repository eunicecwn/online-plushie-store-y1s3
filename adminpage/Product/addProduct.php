<?php
require '../../_base.php';

// Check if admin is logged in
$adminID = $_SESSION['adminID'] ?? null;

// Handle AJAX request for category dropdown
if (isset($_GET['type'])) {
    header('Content-Type: application/json');
    $type = $_GET['type'];

    if (!$type) {
        echo json_encode([]);
        exit;
    }

    try {
        $stm = $_db->prepare("SELECT categoryID, categoryName FROM category WHERE categoryType = ?");
        $stm->execute([$type]);
        $categories = $stm->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categories);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error']);
    }
    exit;
}

// Initialize error array
$_err = [];

// Process form submission
if (is_post()) {

    // Get product details from form
    $productID = generateProductID();
    $productName = req('productName');
    $stockQuantity = req('stockQuantity');
    $description = req('description');
    $price = req('price');
    $categoryID = req('categoryID');
    $photos = get_multiple_files('photos');
    $weight = req('weight');
    $length = req('length');
    $width = req('width');
    $height = req('height');
    $coverImageIndex = (int)req('cover_image_index');
    $tagsInput = req('tags'); // Get tags input
    $tags = !empty($tagsInput) ? implode(',', array_map('trim', explode(',', $tagsInput))) : null;

    // Validate : product name
    if ($productName == '') {
        $_err['productName'] = 'Product name is required.';
    } else if (strlen($productName) > 100) {
        $_err['productName'] = 'Product name should not exceed 100 characters.';
    }

    // Validate : category selection
    if ($categoryID == '') {
        $_err['categoryID'] = 'Please select a product category.';
    }

    // Validate : category type
    if (req('categoryType') == '') {
        $_err['categoryType'] = 'Please specify the category type.';
    }

    // Validate tags
if (!empty($tagsInput)) {
    $tagsArray = explode(',', $tagsInput);
    if (count($tagsArray) > 10) {
        $_err['tags'] = 'Maximum 10 tags allowed.';
    }
    foreach ($tagsArray as $tag) {
        if (strlen(trim($tag)) > 20) {
            $_err['tags'] = 'Each tag should be less than 20 characters.';
            break;
        }
    }
}

    // Validate : stock quantity
    if ($stockQuantity == '') {
        $_err['stockQuantity'] = 'Stock quantity is required.';
    } else if (!is_numeric($stockQuantity)) {
        $_err['stockQuantity'] = 'Please enter a valid number for stock quantity.';
    } else if ($stockQuantity < 1) {
        $_err['stockQuantity'] = 'Stock quantity must be at least 1.';
    } else if ($stockQuantity > 10000) {
        $_err['stockQuantity'] = 'Stock quantity cannot exceed 10,000.';
    }

    // Validate : price
    if ($price == '') {
        $_err['price'] = 'Price is required.';
    } else if (!is_money($price)) {
        $_err['price'] = 'Please enter a valid price amount.';
    } else if (floatval($price) < 0.01) {
        $_err['price'] = 'Minimum price allowed is RM0.01.';
    } else if (floatval($price) > 10000000000.00) {
        $_err['price'] = 'Price exceeds the maximum allowed amount.';
    }

    // Validate : photos
    if (!$photos) {
        $_err['photos'] = 'Please upload at least one product image.';
    } elseif (count($photos) > 9) {
        $_err['photos'] = 'You can upload up to 9 images maximum.';
    } else {
        foreach ($photos as $photo) {
            if (!str_starts_with($photo->type, 'image/')) {
                $_err['photos'] = 'Only image files are accepted (JPEG, PNG, etc.).';
                break;
            } else if ($photo->size > 1 * 1024 * 1024) {
                $_err['photos'] = 'Each image should be less than 1MB in size.';
                break;
            }
        }
    }

    // Validate : weight and dimensions
    $dimensions = ['weight', 'length', 'width', 'height'];
    foreach ($dimensions as $dim) {
        if ($$dim == '') {
            $_err[$dim] = "Please provide the $dim.";
        } else if (!is_numeric($$dim)) {
            $_err[$dim] = "Please enter a valid number for $dim.";
        } else if ($$dim < 0.01) {
            $_err[$dim] = "Minimum $dim is 0.01.";
        } else if ($$dim > 100) {
            $_err[$dim] = "Maximum $dim allowed is 100.";
        }
    }

    // If no errors, insert into database
    if (!$_err) {
        try {
            $_db->beginTransaction();


            // Insert product
            $stm = $_db->prepare('
            INSERT INTO product (productID, productName, stockQuantity, description, price, 
            weight, length, width, height, adminID, tags)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stm->execute([
                $productID,
                $productName,
                $stockQuantity,
                $description,
                $price,
                $weight,
                $length,
                $width,
                $height,
                $adminID,
                $tags
            ]);
            

            // Insert product category
            $stm = $_db->prepare('INSERT INTO product_category (productID, categoryID) VALUES (?, ?)');
            $stm->execute([$productID, $categoryID]);

            // Insert images with cover image flag
            foreach ($photos as $index => $photo) {
                $photoName = save_photo($photo, '../../productImage');
                $isCover = ($index === $coverImageIndex) ? 1 : 0;

                $stm = $_db->prepare('
                    INSERT INTO gallery (productID, imageName, is_cover) 
                    VALUES (?, ?, ?)
                ');
                $stm->execute([$productID, $photoName, $isCover]);
            }

            $_db->commit();
            temp('info', 'Product added successfully');
            redirect('/adminpage/Product/addProduct.php');
        } catch (Exception $e) {
            $_db->rollBack();
            $_err[] = 'Error: ' . $e->getMessage();
        }
    }
}

// Set page title and include header
$_title = 'KAWAII.SellerCentre | Add Product';
include '../../_headadmin.php';
?>

<link rel="stylesheet" href="/css/admin.css">

<h1 class="AddProductTitle">Add a New Product</h1>

<form method="post" class="form-containerproduct" enctype="multipart/form-data" novalidate>
    <div class="section-container">
        <h2 class="product-subtitle">Basic Information</h2>

        <div class="form-group">
            <div class="image-upload-section">
                <label>Product Images (Max 9 images, first is cover)</label>
                <input type="file" name="photos[]" id="photos" multiple accept="image/*" hidden>
                <input type="hidden" name="cover_image_index" id="cover_image_index" value="0">
                <div id="imagePreview" class="image-preview"></div>
                <?= err('photos') ?>
            </div>
        </div>

        <div class="form-group">
            <label for="productName">Product Name</label>
            <?= html_text('productName', 'maxlength="100" placeholder="Product Name + Category Type + Key Features (Materials, Color , Size, Model)" class="form-input"') ?>
            <?= err('productName') ?>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <?php html_textarea('description', 'class="form-input-description" maxlength="3000" placeholder="Enter description"'); ?>
            <?= err('description') ?>
        </div>

        <div class="form-group">
    <label for="productTags">Tags</label>
    <input type="text" name="tags" id="productTags" 
           value="<?= !empty($_POST['tags']) ? htmlspecialchars($_POST['tags']) : '' ?>" 
           placeholder="e.g., kawaii, anime, gift" class="form-input">
    <small>Separate tags with commas (max 10 tags, 20 chars each)</small>
    <?= err('tags') ?>
</div>

        <div class="form-row">
            <div class="form-group" style="flex: 1; margin-right: 15px;">
                <label for="categoryType">Category Type</label>
                <select name="categoryType" id="categoryType" class="product-select-form">
                    <option value="">Select Type</option>
                    <?php foreach ($_categoryType as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= err('categoryType') ?>
            </div>

            <div class="form-group" style="flex: 1;">
                <label for="categoryID">Category Name</label>
                <select name="categoryID" id="categoryID" class="product-select-form">
                    <option value="">Select Type First</option>
                </select>
                <?= err('categoryID') ?>
            </div>
        </div>
    </div>

    <div class="section-container">
        <h2 class="product-subtitle">Sales Information</h2>

        <div class="form-group">
            <label for="stockQuantity">Stock Quantity</label>
            Pcs <?= html_number('stockQuantity', 1, 10000, 1, 'class="form-input-number" placeholder="Enter quantity"') ?>
            <?= err('stockQuantity') ?>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            RM <?= html_number('price', 0.01, 10000000000.00, 0.01, 'class="form-input-number" placeholder="Enter price"') ?>
            <?= err('price') ?>
        </div>
    </div>

    <div class="section-container">
        <h2 class="product-subtitle">Shipping Information</h2>

        <div class="form-group">
            <label for="weight">Weight</label>
            <?= html_number('weight', 0.01, 10000.00, 0.01, 'class="form-input-number shipping-dimension" placeholder="Weight" id="weight"') ?> kg
            <?= err('weight') ?>
        </div>

        <div class="form-group">
            <label>Dimensions</label>
            <?= html_number('length', 0.01, 10000.00, 0.01, 'class="form-input-number shipping-dimension" placeholder="Length" id="length"') ?> cm x
            <?= html_number('width', 0.01, 10000.00, 0.01, 'class="form-input-number shipping-dimension" placeholder="Width" id="width"') ?> cm x
            <?= html_number('height', 0.01, 10000.00, 0.01, 'class="form-input-number shipping-dimension" placeholder="Height" id="height"') ?> cm
            <?= err('length') ?>
            <?= err('width') ?>
            <?= err('height') ?>
        </div>
    </div>

    <section class="product-button">
        <button type="submit" class="form-button">Submit</button>
        <button type="reset" class="form-button">Reset</button>
        <a href="/adminpage/Product/myProduct.php" data-confirm="Discard changes?" class="form-button">Cancel</a>
    </section>
</form>

<script src="/js/uploadImage.js"></script>