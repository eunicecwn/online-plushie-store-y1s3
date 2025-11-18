<?php
require '../../_base.php';
require_once '../../lib/SimplePager.php';

// Handle AJAX request for category dropdown
if (isset($_GET['type'])) {
    header('Content-Type: application/json');
    $type = $_GET['type'];

    if (!$type) {
        echo json_encode([]); // Return an empty array if no type is selected
        exit;
    }

    try {
        $stm = $_db->prepare("SELECT categoryID, categoryName FROM category WHERE categoryType = ?");
        $stm->execute([$type]);
        $categories = $stm->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categories); // Return categories as JSON
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error']); // Handle errors and return as JSON
    }
    exit;
}

// Get request parameters
$categoryTypeFilter = req('categoryType'); // Add this line
$categoryName = req('categoryName'); // new line
$searchTerm = req('search');     // Optional search term
$statusFilter = req('status');   // Status filter (Available/Delisted)
$GLOBALS['search'] = $searchTerm; // Persist search term

$categories = [];
if ($categoryTypeFilter) {
    $stm = $_db->prepare("SELECT categoryID, categoryName FROM category WHERE categoryType = ?");
    $stm->execute([$categoryTypeFilter]);
    $categories = $stm->fetchAll(PDO::FETCH_ASSOC);
}


// Define sortable columns
$fields = [
    'productName'   => 'Product Name',
    'stockQuantity' => 'Stock Quantity',
    'price' => 'Price'
];

// Validate sorting parameters
$sort = req('sort');  // Get sort field from request
key_exists($sort, $fields) || $sort = 'productName'; // Default to productName

$dir = req('dir');    // Get sort direction
in_array($dir, ['asc', 'desc']) || $dir = 'asc'; // Default to ascending

$page = (int) req('page', 1); // Force it to be an integer


$whereConditions = [];
$params = [];

// Add search term condition if provided
if ($searchTerm) {
    $whereConditions[] = "(p.productName LIKE ? OR p.description LIKE ? OR p.tags LIKE ? OR c.categoryName LIKE ?)";
    array_push($params, "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%");
}


if ($categoryName) {
    $whereConditions[] = "c.categoryID = ?";
    $params[] = $categoryName;
}



// Add status filter condition if provided and valid
if ($statusFilter && in_array($statusFilter, ['available', 'delisted'])) {
    $whereConditions[] = "p.status = ?";
    $params[] = $statusFilter;
}

// Combine all WHERE conditions with AND
$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';


$p = new SimplePager(
    "SELECT p.*, g.imageName as coverImage, GROUP_CONCAT(c.categoryName) as categories
     FROM product p
     LEFT JOIN gallery g ON p.productID = g.productID AND g.is_cover = 1
     LEFT JOIN product_category pc ON p.productID = pc.productID
     LEFT JOIN category c ON pc.categoryID = c.categoryID
     $whereClause
     GROUP BY p.productID
     ORDER BY $sort $dir",
    $params,
    12,  // items per page
    $page
);


// Get results for current page
$arr = $p->result;

// Set page title and include header
$_title = 'KAWAII.SellerCentre | My Product';
include '../../_headadmin.php';
?>

<!-- Link to admin CSS -->
<link rel="stylesheet" href="/css/admin.css">

<div class="product-scroll">
<!-- Page Heading -->
<h1 class="product-title">My Product</h1>

<!-- Search and Filter Form -->
<form class="search-form" id="product-searchform">
    <!-- Search input -->
    <input type="text" name="search" placeholder="Search by product name, description or tags" value="<?= encode($searchTerm) ?>">

    <select name="categoryType">
        <option value="">All Category Types</option>
        <?php foreach ($_categoryType as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= $categoryTypeFilter === $value ? 'selected' : '' ?>>
                <?= htmlspecialchars($label) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="categoryName" id="categoryName2">
        <option value="">All Categories</option>
        <?php
        // Assuming $categories is populated with category data from the database
        foreach ($categories as $category):
        ?>
            <option value="<?= encode($category['categoryID']) ?>" <?= $categoryName == $category['categoryID'] ? 'selected' : '' ?>>
                <?= encode($category['categoryName']) ?>
            </option>
        <?php endforeach; ?>
    </select>


    <!-- Status Filter Dropdown -->
    <select name="status">
        <option value="">All Status</option>
        <option value="available" <?= $statusFilter === 'available' ? 'selected' : '' ?>>Available</option>
        <option value="delisted" <?= $statusFilter === 'delisted' ? 'selected' : '' ?>>Delisted</option>
    </select>

    <!-- Hidden fields to maintain sort -->
    <input type="hidden" name="sort" value="<?= encode($sort) ?>">
    <input type="hidden" name="dir" value="<?= encode($dir) ?>">

    <!-- Form buttons -->
    <button type="submit">Apply</button>

    <!-- Reset button appears only when filters are active -->
    <?php if (!empty($searchTerm) || !empty($statusFilter) || !empty($categoryTypeFilter) || !empty($categoryName)): ?>
        <a href="?" class="btn btn-reset">Reset</a>
    <?php endif; ?>

</form>

<!-- Record count information -->
<p class="record-count" id = "product-count">
    <?= $p->count ?> of <?= $p->item_count ?> record(s) |
    Page <?= $p->page ?> of <?= $p->page_count ?>
</p>

<!-- Generate sortable headers -->
<div class="sort-bar">
    Sort by:
    <?= table_headers2($fields, $sort, $dir, "search=$searchTerm&status=$statusFilter&page=$page") ?>
</div>

</div>

<!-- Main Product Container -->
<div class="product-container">
    <div class="product-grid">
        <?php if (empty($arr)): ?>
            <div class="no-results">
                No products found
                <?php if ($searchTerm): ?>
                    matching: <strong><?= encode($searchTerm) ?></strong>
                <?php endif; ?>
                <?php if ($statusFilter): ?>
                    with status: <strong><?= ucfirst(encode($statusFilter)) ?></strong>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Loop through results -->
            <?php foreach ($arr as $product): ?>
                <div class="product-card">
                    <!-- Product Image -->
                    <div class="product-image">
                        <?php if ($product->coverImage): ?>
                            <img src="/productImage/<?= encode($product->coverImage) ?>" alt="<?= encode($product->productName) ?>">
                        <?php else: ?>
                            <span class="no-image">No image</span>
                        <?php endif; ?>
                    </div>

                    <!-- Product Name -->
                    <div class="product-name">
                        <strong><?= encode($product->productName) ?></strong>
                    </div>

                    <!-- Price -->
                    <div class="product-price">
                        <strong>RM <?= number_format($product->price, 2) ?></strong>
                    </div>

                    <!-- Stock Quantity -->
                    <div class="product-stock">
                        <strong>Stock <?= encode($product->stockQuantity) ?></strong>
                    </div>

                    <?php
                    $tagsArray = array_filter(array_map('trim', explode(',', $product->tags)));
                    ?>
                    <div class="product-tags">
                        Tags:
                        <?php if (count($tagsArray) > 0): ?>
                            <?php foreach ($tagsArray as $tag): ?>
                                <span class="tag-badge"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="tag-badge">No tags</span>
                        <?php endif; ?>
                    </div>

                    <!-- Status -->
                    <div class="product-status">
                        <span class="status-badge status-<?= $product->status ?>">
                            <?= ucfirst(encode($product->status)) ?>
                        </span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="product-actions">
                        <!-- Edit button -->
                        <a href="/adminpage/Product/updateProduct.php?productID=<?= $product->productID ?>" class="btn btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        <!-- Context-sensitive Activate/Deactivate button -->
                        <?php if ($product->status == 'available'): ?>
                            <a href="deactivateProduct.php?productID=<?= $product->productID ?>" class="btn btn-deactivate">
                                <i class="fas fa-ban"></i> Delist
                            </a>
                        <?php else: ?>
                            <a href="activateProduct.php?productID=<?= $product->productID ?>" class="btn btn-activate">
                                <i class="fas fa-check"></i> Activate
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination Links (preserves all filter parameters) -->
<div class="pagination-product">
    <?= $p->html("&search=$searchTerm&status=$statusFilter&sort=$sort&dir=$dir") ?>
</div>

</div>

<style>
.pagination-product {
    margin-bottom: 20px;  /* Adjust spacing between the products and pagination */
    text-align: center; /* Center the pagination links */
    justify-content: center;
    padding: 10px 0;
    max-width: 1200px;
    gap: 5px;
}

.pagination-product a,
.pagination-product span {
    display: inline-block;
    min-width: 32px;
    height: 32px;
    line-height: 32px;
    padding: 0 5px;
    border: 1px solid #e58f8f;
    border-radius: 15px;
    color: #e58f8f;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
    text-align: center;
    box-sizing: border-box;
}

/* Prevent underline effect under dropdown */
.pagination-product a:after {
    display: none;
}

.pagination-product a:hover {
    background-color: #e58f8f;
    color: #ffffff;
    transform: translateY(-2px);
}

.pagination-product .current {
    background-color: #e58f8f;
    color: #ffffff;
    font-weight: bold;
}

.pagination-product .disabled {
    opacity: 0.5;
    pointer-events: none;
}

/* Arrow specific styling */
.pagination-product .prev,
.pagination-product .next {
    font-weight: bold;
    min-width: 40px;
}

/* Ellipsis styling */
.pagination-product .ellipsis {
    border: none;
    pointer-events: none;
}
</style>


<script src="/js/categoryDropdown.js"></script>