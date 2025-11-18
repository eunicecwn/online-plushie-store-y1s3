<?php
require '../../_base.php';

// Check if admin is logged in
$adminID = $_SESSION['adminID'] ?? null;
$productID = req('productID');

// Retrieve existing product data for editing
if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM product WHERE productID = ? LIMIT 1');
    $stm->execute([$productID]);
    $product = $stm->fetch();

    if (!$product) {
        temp('error', 'Product not found');
        redirect('/');
    }

    // Get product's assigned categoryID
    $prodCatStmt = $_db->prepare('SELECT categoryID FROM product_category WHERE productID = ?');
    $prodCatStmt->execute([$productID]);
    $categoryID = $prodCatStmt->fetchColumn();

    // Get the category type for the product's category
    $catTypeStmt = $_db->prepare('SELECT categoryType FROM category WHERE categoryID = ?');
    $catTypeStmt->execute([$categoryID]);
    $productCategoryType = $catTypeStmt->fetchColumn();

    // Retrieve all distinct category types for dropdown
    $categoryTypesStmt = $_db->prepare('SELECT DISTINCT categoryType FROM category');
    $categoryTypesStmt->execute();
    $categoryTypes = $categoryTypesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Retrieve categories for the current type
    $categoriesStmt = $_db->prepare('SELECT categoryID, categoryName FROM category WHERE categoryType = ?');
    $categoriesStmt->execute([$productCategoryType]);
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Retrieve existing product images
    $productImagesStmt = $_db->prepare('
    SELECT imageID, imageName, is_cover 
    FROM gallery 
    WHERE productID = ? 
    ORDER BY is_cover DESC, imageID ASC
');
    $productImagesStmt->execute([$productID]);
    $productImages = $productImagesStmt->fetchAll(PDO::FETCH_ASSOC);
    if ($product) {
        extract((array)$product);
        // Ensure tags are properly formatted
        $tags = $tags ?? '';
    }
}

// Initialize error array
$_err = [];

// Process form submission
if (is_post()) {
    $productName = req('productName');
    $stockQuantity = req('stockQuantity');
    $description = req('description');
    $price = req('price');
    $categoryID = req('categoryID');
    $weight = req('weight');
    $length = req('length');
    $width = req('width');
    $height = req('height');
    $coverImageIndex = (int)req('cover_image_index');
    $tagsInput = req('tags');
    $tags = !empty($tagsInput) ? implode(',', array_map('trim', explode(',', $tagsInput))) : null;
    $photos = get_multiple_files('photos');

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

// Validate : photos - only require if no existing images
$existingImagesStmt = $_db->prepare('SELECT COUNT(*) FROM gallery WHERE productID = ?');
$existingImagesStmt->execute([$productID]);
$existingImageCount = $existingImagesStmt->fetchColumn();

if (!$photos && $existingImageCount == 0) {
    $_err['photos'] = 'Please upload at least one product image.';
} elseif ($photos && count($photos) > 9) {
    $_err['photos'] = 'You can upload up to 9 images maximum.';
} elseif ($photos) {
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
    try {
        $_db->beginTransaction();

        // Update product details
        $stm = $_db->prepare('UPDATE product SET productName=?, stockQuantity=?, description=?, price=?, weight=?, length=?, width=?, height=?, adminID=?, tags=? WHERE productID=?');
        $stm->execute([$productName, $stockQuantity, $description, $price, $weight, $length, $width, $height, $adminID, $tags, $productID]);

        // Update product category
        $ins = $_db->prepare('UPDATE product_category SET categoryID = ? WHERE productID = ?');
        $ins->execute([$categoryID, $productID]);

        // Handle images to delete if any
        if (!empty($_POST['imagesToDelete'])) {
            $imagesToDelete = json_decode($_POST['imagesToDelete']);
            if (is_array($imagesToDelete)) {
                foreach ($imagesToDelete as $imageID) {
                    // Delete image file from folder if needed (optional)
                    $stmt = $_db->prepare("SELECT imageName FROM gallery WHERE imageID = ? AND productID = ?");
                    $stmt->execute([$imageID, $productID]);
                    $img = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($img) {
                        $imagePath = '../../productImage/' . $img['imageName'];
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }

                    // Delete image record
                    $stmt = $_db->prepare("DELETE FROM gallery WHERE imageID = ? AND productID = ?");
                    $stmt->execute([$imageID, $productID]);
                }
            }
        }

        // ===== ADD IMAGE PROCESSING CODE HERE =====
// 1. First set ALL images as non-cover
$_db->prepare('UPDATE gallery SET is_cover = 0 WHERE productID = ?')->execute([$productID]);

// 2. Process new uploads if any
if (!empty($_FILES['photos']['name'][0])) {
    foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
        if ($_FILES['photos']['error'][$index] === UPLOAD_ERR_OK) {
            $photo = [
                'tmp_name' => $tmpName,
                'name' => $_FILES['photos']['name'][$index],
                'type' => $_FILES['photos']['type'][$index],
                'size' => $_FILES['photos']['size'][$index],
                'error' => $_FILES['photos']['error'][$index]
            ];
            
            // Validate the file
            if (!str_starts_with($photo['type'], 'image/')) {
                error_log('Invalid file type: ' . $photo['type']);
                continue;
            }
            
            if ($photo['size'] > 1 * 1024 * 1024) {
                error_log('File too large: ' . $photo['name']);
                continue;
            }
            
            try {
                $photoName = save_photo((object)$photo, '../../productImage');
                
                // Only first image becomes cover
                $isCover = ($index === 0) ? 1 : 0;
                
                $stm = $_db->prepare('INSERT INTO gallery (productID, imageName, is_cover) VALUES (?, ?, ?)');
                $stm->execute([$productID, $photoName, $isCover]);
                
                error_log('Successfully saved image: ' . $photoName);
            } catch (Exception $e) {
                error_log('Error saving image: ' . $e->getMessage());
            }
        }
    }
}

// 3. Handle cover image from reordering (only if no new uploads)
if (empty($_FILES['photos']['name'][0])) {
    $coverId = $_POST['cover_image_index'];
    
    if (!empty($coverId)) {
        if (is_numeric($coverId)) {
            $_db->prepare('UPDATE gallery SET is_cover = 1 WHERE imageID = ? AND productID = ?')
               ->execute([$coverId, $productID]);
        } else {
            $_db->prepare('UPDATE gallery SET is_cover = 1 WHERE imageName = ? AND productID = ?')
               ->execute([basename($coverId), $productID]);
        }
    }
}
        // ===== END IMAGE PROCESSING CODE =====

        $_db->commit();
        temp('info', 'Product updated successfully');
        redirect('/adminpage/Product/myproduct.php');
    } catch (Exception $e) {
        $_db->rollBack();
        $_err[] = 'Error: ' . $e->getMessage();
    }
}

// ADD: Early check for JSON decoding errors
if (isset($_POST['imagesToDelete']) && !empty($_POST['imagesToDelete'])) {
    $imagesToDelete = json_decode($_POST['imagesToDelete']);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $_err[] = 'Invalid image deletion data.';
    }
}

// Set page title and include header
$_title = 'KAWAII.SellerCentre | Edit Product';
include '../../_headadmin.php';
?>

<link rel="stylesheet" href="/css/admin.css">
<script src="/js/EditImage.js"></script>

<h1 class="AddProductTitle">Product Details</h1>

<form method="post" class="form-containerproduct" enctype="multipart/form-data" novalidate>
    <div class="section-container">
        <h2 class="product-subtitle">Basic Information</h2>
        <!-- Add these hidden inputs to your form -->
        <input type="hidden" name="productID" value="<?= $productID ?>">
        <input type="hidden" name="imagesToDelete" id="imagesToDelete" value="">
        <input type="hidden" name="cover_image_index" id="cover_image_index" value="0">

        <div class="image-upload-section">
    <label>Product Images (Max 9 images, first is cover)</label>
    <input type="file" name="photos[]" id="photos" multiple accept="image/jpeg, image/png, image/webp" hidden>
    <input type="hidden" name="cover_image_index" id="cover_image_index" value="0">
    <input type="hidden" name="imagesToDelete" id="imagesToDelete" value="">
    
    <!-- Image preview container -->
    <div id="imagePreview" class="image-preview">
        <?php if (!empty($productImages)): ?>
            <?php foreach ($productImages as $index => $image): ?>
                <div class="preview-item <?= $image['is_cover'] ? 'cover-image' : '' ?>"
                    data-image-id="<?= $image['imageID'] ?>"
                    data-is-new="false"
                    draggable="true">
                    <img src="/productImage/<?= htmlspecialchars($image['imageName']) ?>" alt="Product Image">
                    <button type="button" class="remove-btn">×</button>
                    <span class="position-badge">
                        <?= $image['is_cover'] ? 'Cover' : ($index + 1) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Add button (only shows if less than max images) -->
        <div class="add-btn-container">
            <label for="photos" class="add-btn">
                <span>+</span>
                <span>Add Photos</span>
            </label>
        </div>
    </div>
    
    <?= err('photos') ?>
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
            <?php
            $tagsValue = '';

            // Check POST first
            if (!empty($_POST['tags'])) {
                $tagsValue = htmlspecialchars($_POST['tags']);
            }
            // Then check product object/array
            elseif (is_object($product) && isset($product->tags)) {
                $tagsValue = htmlspecialchars($product->tags);
            } elseif (is_array($product) && isset($product['tags'])) {
                $tagsValue = htmlspecialchars($product['tags']);
            }
            ?>
            <input type="text" name="tags" id="productTags"
                value="<?= $tagsValue ?>"
                placeholder="e.g., kawaii, anime, gift" class="form-input">
            <small>Separate tags with commas (max 10 tags, 20 chars each)</small>
            <?= err('tags') ?>
        </div>
        <div class="form-row">
            <div class="form-group" style="flex: 1; margin-right: 15px;">
                <label for="categoryType">Category Type</label>
                <select name="categoryType" id="categoryType" class="product-select-form" onchange="updateCategories()">
                    <option value="">Select Type</option>
                    <?php if (!empty($categoryTypes)): ?>
                        <?php foreach ($categoryTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type['categoryType']) ?>"
                                <?= ($type['categoryType'] == ($productCategoryType ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['categoryType']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?= err('categoryType') ?>
            </div>

            <div class="form-group" style="flex: 1;">
                <label for="categoryID">Category Name</label>
                <select name="categoryID" id="categoryID" class="product-select-form">
                    <option value="">Select Category</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['categoryID'] ?>"
                                <?= ($cat['categoryID'] == ($categoryID ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['categoryName']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?= err('categoryID') ?>
            </div>
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
        <button type="submit" class="form-button">Update</button>
        <button type="reset" class="form-button">Reset</button>
        <a href="/adminpage/Product/myProduct.php" data-confirm="Discard changes?" class="form-button">Cancel</a>
    </section>
</form>