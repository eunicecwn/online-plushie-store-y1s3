<?php
// Include required files
require '../../_base.php';          // Database connection and core functions
require_once '../../lib/SimplePager.php';  // Pagination functionality

// Get request parameters
$categoryType = req('type');     // Required category type from URL
$searchTerm = req('search');     // Optional search term
$statusFilter = req('status');   // Status filter (Active/Inactive)
$GLOBALS['search'] = $searchTerm; // Persist search term

// Define sortable columns (removed status from sortable fields)
$fields = [
    'categoryID'   => 'Category ID',    // Sort by ID
    'categoryName' => 'Category Name'   // Sort by Name
];

// Validate sorting parameters
$sort = req('sort');  // Get sort field from request
key_exists($sort, $fields) || $sort = 'categoryID'; // Default to categoryID

$dir = req('dir');    // Get sort direction
in_array($dir, ['asc', 'desc']) || $dir = 'asc'; // Default to ascending

$page = req('page', 1); // Get current page, default to 1

// Build WHERE conditions for filtering
$whereConditions = ["categoryType = ?"]; // Base condition for category type
$params = [$categoryType];               // Parameters array

// Add search term condition if provided
if ($searchTerm) {
    $whereConditions[] = "(categoryID LIKE ? OR categoryName LIKE ?)";
    array_push($params, "%$searchTerm%", "%$searchTerm%");
}

// NEW: Add status filter condition if provided and valid
if ($statusFilter && in_array($statusFilter, ['Active', 'Inactive'])) {
    $whereConditions[] = "status = ?";
    $params[] = $statusFilter;
}

// Combine all WHERE conditions with AND
$whereClause = implode(' AND ', $whereConditions);

// Create paginated query with filtering
$p = new SimplePager(
    "SELECT * FROM category WHERE $whereClause ORDER BY $sort $dir", // Query with filtering
    $params,    // Parameters for prepared statement
    7,   // Items per page
    $page       // Current page
);

// Get results for current page
$arr = $p->result;

// Set page title and include header
$_title = 'KAWAII.SellerCentre | My Category';
include '../../_headadmin.php';
?>

<!-- Link to admin CSS -->
<link rel="stylesheet" href="/css/admin.css">

<!-- Page Heading -->
<h1 class="category-title">Browse Categories</h1>
<h2 class="category-subtitle">Showing Category: <strong><?= encode($categoryType) ?></strong></h2>

<!-- NEW: Enhanced Search and Filter Form -->
<form class="search-form">
    <!-- Hidden field to maintain category type -->
    <input type="hidden" name="type" value="<?= encode($categoryType) ?>">

    <!-- Search input -->
    <input type="text" name="search" placeholder="Search by ID or Name" value="<?= encode($searchTerm) ?>">

    <!-- NEW: Status Filter Dropdown -->
    <select name="status">
        <option value="">All Status</option>
        <option value="Active" <?= $statusFilter === 'Active' ? 'selected' : '' ?>>Active</option>
        <option value="Inactive" <?= $statusFilter === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
    </select>

    <!-- Form buttons -->
    <button type="submit">Apply</button>

    <!-- NEW: Reset button appears only when filters are active -->
    <?php if ($searchTerm || $statusFilter): ?>
        <a href="?type=<?= encode($categoryType) ?>" class="btn btn-reset">Reset</a>
    <?php endif; ?>
</form>

<!-- Record count information -->
<p class="record-count">
    <?= $p->count ?> of <?= $p->item_count ?> record(s) |
    Page <?= $p->page ?> of <?= $p->page_count ?>
</p>

<!-- Main Table Container -->
<div class="table-container" id="category-table">
    <table class="category-table">
        <thead>
            <tr>
                <!-- Generate sortable headers (status is not sortable) -->
                <?= table_headers($fields, $sort, $dir, "type=$categoryType&search=$searchTerm&status=$statusFilter&page=$page") ?>
                <!-- Status column header (not sortable) -->
                <th>Status</th>
                <!-- Actions column header -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($arr)): ?>
                <!-- Enhanced no results message showing active filters -->
                <tr>
                    <td colspan="4" class="no-results">
                        No results found in <strong><?= encode($categoryType) ?></strong>
                        <?php if ($searchTerm): ?>
                            for: <strong><?= encode($searchTerm) ?></strong>
                        <?php endif; ?>
                        <?php if ($statusFilter): ?>
                            with status: <strong><?= encode($statusFilter) ?></strong>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php else: ?>
                <!-- Loop through results -->
                <?php foreach ($arr as $s): ?>
                    <tr>
                        <!-- Category ID -->
                        <td><?= encode($s->categoryID) ?></td>
                        <!-- Category Name (formatted) -->
                        <td><?= ucwords(strtolower(encode($s->categoryName))) ?></td>
                        <!-- Status with colored badge -->
                        <td>
                            <span class="status-badge status-<?= $s->status ?>">
                                <?= ucwords(strtolower(encode($s->status))) ?>
                            </span>
                        </td>
                        <!-- Action buttons -->
                        <td class="actions">
                            <!-- Edit button -->
                            <a href="/adminpage/Category/updateCategory.php?categoryID=<?= $s->categoryID ?>" class="btn btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <!-- Context-sensitive Activate/Deactivate button -->
                            <?php if (strtolower($s->status) == 'active'): ?>
                                <a href="deactivateCategory.php?categoryID=<?= $s->categoryID ?>" class="btn btn-deactivate">
                                    <i class="fas fa-ban"></i> Deactivate
                                </a>
                            <?php else: ?>
                                <a href="activateCategory.php?categoryID=<?= $s->categoryID ?>" class="btn btn-activate">
                                    <i class="fas fa-check"></i> Activate
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination Links (preserves all filter parameters) -->
<div class="pagination">
    <?= $p->html("type=$categoryType&search=$searchTerm&status=$statusFilter&sort=$sort&dir=$dir") ?>
</div>