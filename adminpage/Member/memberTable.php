<?php
require '../../_base.php';          // Database connection and core functions
require_once '../../lib/SimplePager.php';  // Pagination functionality

// Get request parameters
$searchTerm = req('search');     // Optional search term
$statusFilter = req('status');   // Status filter (Active/Blocked/Pending)
$GLOBALS['search'] = $searchTerm; // Persist search term

// Define sortable columns
$fields = [
    'memberID'   => 'Member ID',    // Sort by ID
    'memberName' => 'Name',         // Sort by Name
    'memberEmail' => 'Email',       // Sort by Email
    'phoneNumber' => 'Phone'        // Sort by Phone
];

// Validate sorting parameters
$sort = req('sort');  // Get sort field from request
key_exists($sort, $fields) || $sort = 'memberName'; // Default to memberName

$dir = req('dir');    // Get sort direction
in_array($dir, ['asc', 'desc']) || $dir = 'asc'; // Default to ascending

$page = req('page', 1); // Get current page, default to 1

// Build WHERE conditions for filtering
$whereConditions = ["1=1"]; // Base condition
$params = [];               // Parameters array

// Add search term condition if provided
if ($searchTerm) {
    $whereConditions[] = "(memberID LIKE ? OR memberName LIKE ?)";
    array_push($params, "%$searchTerm%", "%$searchTerm%");
}

// Add status filter condition if provided and valid
if ($statusFilter && in_array($statusFilter, ['active', 'blocked', 'pending', 'inactive'])) {
    $whereConditions[] = "status = ?";
    $params[] = $statusFilter;
}

// Combine all WHERE conditions with AND
$whereClause = implode(' AND ', $whereConditions);

// Create paginated query with filtering
$p = new SimplePager(
    "SELECT * FROM member WHERE $whereClause ORDER BY $sort $dir", // Query with filtering
    $params,    // Parameters for prepared statement
    5,         // Items per page
    $page       // Current page
);

// Get results for current page
$members = $p->result;

// Set page title and include header
$_title = 'KAWAII.SellerCentre | View All Members';
include '../../_headadmin.php';
?>
    <!-- Link to member CSS -->
    <link rel="stylesheet" href="/css/member.css">
    <!-- Page Heading -->
        <h1 class="staff-title">View All Members</h1>
    
        <!-- Enhanced Search and Filter Form -->
        <form class="search-form" method="GET">
            <!-- Search input -->
            <input type="text" name="search" placeholder="Search by ID or Name" value="<?= encode($searchTerm) ?>">

            <!-- Status Filter Dropdown -->
            <select name="status">
                <option value="">All Status</option>
                <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="blocked" <?= $statusFilter === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
            </select>

            <!-- Form buttons -->
            <button type="submit" class="search-button">Apply</button>

            <!-- Reset button appears only when filters are active -->
            <?php if ($searchTerm || $statusFilter): ?>
                <a href="memberTable.php" class="btn btn-reset">Reset</a>
            <?php endif; ?>
        </form>

        <!-- Record count information -->
        <p class="record-count">
            <?= $p->count ?> of <?= $p->item_count ?> record(s) |
            Page <?= $p->page ?> of <?= $p->page_count ?>
        </p>

        <!-- Members Table -->
        <div class="table-container"id="category-table" style="position:fixed;height:500px;">
            <table class="category-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <!-- Generate sortable headers -->
                        <?php foreach ($fields as $field => $label): ?>
                            <th>
                                <a href="?<?= http_build_query([
                                    'sort' => $field,
                                    'dir' => ($sort === $field && $dir === 'asc') ? 'desc' : 'asc',
                                    'search' => $searchTerm,
                                    'status' => $statusFilter,
                                    'page' => $page
                                ]) ?>">
                                    <?= $label ?>
                                    <?php if ($sort === $field): ?>
                                        <?= $dir === 'asc' ? '▴' : '▾' ?>
                                    <?php endif; ?>
                                </a>
                            </th>
                        <?php endforeach; ?>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Profile Photo</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="9" class="no-results">
                                No members found
                                <?php if ($searchTerm): ?>
                                    matching: <strong><?= encode($searchTerm) ?></strong>
                                <?php endif; ?>
                                <?php if ($statusFilter): ?>
                                    with status: <strong><?= ucfirst(encode($statusFilter)) ?></strong>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $index => $member): ?>
                            <?php
                            $statusClass = 'status-badge ';
                            switch ($member->status) {
                                case 'active':
                                    $statusClass .= 'status-active';
                                    break;
                                case 'blocked':
                                case 'inactive':
                                    $statusClass .= 'status-inactive';
                                    break;
                                case 'pending':
                                    $statusClass .= 'status-pending';
                                    break;
                            }
                            ?>
                            <tr>
                                <td><?= $index + 1 + (($page - 1) * 5) ?></td>
                                <td><?= encode($member->memberID) ?></td>
                                <td><?= encode($member->memberName) ?></td>
                                <td><?= encode($member->memberEmail) ?></td>
                                <td><?= encode($member->phoneNumber) ?></td>
                                <td><?= encode($member->memberAddress) ?></td>
                                <td>
                                    <span class="<?= $statusClass ?>">
                                        <?= ucfirst($member->status) ?>
                                    </span>
                                </td>
                                <td>
                                    <img class="profile-img" src="../../profile/<?= encode($member->profilePhoto) ?>" alt="Profile Photo">
                                </td>
                                <td>
                                    <?php if ($member->status === 'active') : ?>
                                        <a href="blockmem.php?id=<?= $member->memberID ?>" class="btn btn-deactivate">
                                            <i class="fas fa-ban"></i> Block
                                        </a>
                                    <?php elseif ($member->status === 'inactive' || $member->status === 'blocked') : ?>
                                        <a href="unblockmem.php?id=<?= $member->memberID ?>" class="btn btn-activate">
                                            <i class="fas fa-ban"></i> Unblock
                                        </a>
                                    <?php elseif ($member->status === 'pending') : ?>
                                        <a href="approvemem.php?id=<?= $member->memberID ?>" class="btn btn-activate">
                                            <i class="fas fa-check-circle"></i> Approve
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div class="pagination">
            <?= $p->html(http_build_query([
                'search' => $searchTerm,
                'status' => $statusFilter,
                'sort' => $sort,
                'dir' => $dir
            ])) ?>
        </div>
    </div>

</body>
</html>