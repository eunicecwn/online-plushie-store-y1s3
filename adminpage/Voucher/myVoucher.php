<?php
// Include required files
require '../../_base.php';          // Database connection and core functions
require_once '../../lib/SimplePager.php';  // Pagination functionality

// Get request parameters
$searchTerm = req('search');     // Optional search term
$statusFilter = req('status');   // Status filter (active/expired/inactive lowercase)
$GLOBALS['search'] = $searchTerm; // Persist search term

// Define sortable columns
$fields = [
    'voucherCode' => 'Voucher Code',
    'discountAmount' => 'Discount Amount',
    'usageLimit' => 'Usage Limit',
    'timesUsed' => 'Claimed',
    'expiryDate' => 'Expired Date',
    'createdDate' => 'Created Date'
];

// Validate sorting parameters
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'createdDate';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'desc';

// Auto-update expired vouchers before listing
$currentDateTime = date('Y-m-d H:i:s');
$_db->exec("UPDATE voucher 
            SET status = 'Expired' 
            WHERE expiryDate IS NOT NULL 
              AND expiryDate < '$currentDateTime' 
              AND status != 'Expired'");

// Initialize query conditions
$whereConditions = [];
$params = [];

// Add search term condition if provided
if ($searchTerm) {
    $whereConditions[] = "(voucherCode LIKE ?)";
    $params[] = "%$searchTerm%";
}

// Map lowercase filter values to DB enum values
$statusMap = [
    'active' => 'Active',
    'expired' => 'Expired',
    'disabled' => 'Disabled'
];

if ($statusFilter && array_key_exists($statusFilter, $statusMap)) {
    $whereConditions[] = "status = ?";
    $params[] = $statusMap[$statusFilter];
}

$page = req('page', 1);

// Combine WHERE conditions
$whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Set up pager
$p = new SimplePager(
    "SELECT * FROM voucher $whereClause ORDER BY $sort $dir",
    $params,
    7,
    $page
);

// Fetch vouchers
$vouchers = $p->result;

// Set page title and include header
$_title = 'KAWAII.SellerCentre | My Voucher';
include '../../_headadmin.php';
?>

<link rel="stylesheet" href="/css/admin.css">

<h1 class="category-title">My Voucher</h1>

<!-- Record count information -->
<p class="record-count">
    <?= $p->count ?> of <?= $p->item_count ?> record(s) |
    Page <?= $p->page ?> of <?= $p->page_count ?>
</p>

<!-- Search and Filter Form -->
<form class="search-form">
    <input type="text" name="search" placeholder="Search by Voucher Code" value="<?= htmlspecialchars($searchTerm) ?>">
    <select name="status">
        <option value="">All Status</option>
        <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="expired" <?= $statusFilter === 'expired' ? 'selected' : '' ?>>Expired</option>
        <option value="disabled" <?= $statusFilter === 'disabled' ? 'selected' : '' ?>>Disabled</option>
    </select>
    <button type="submit">Apply</button>
    <?php if ($searchTerm || $statusFilter): ?>
        <a href="?" class="btn btn-reset">Reset</a>
    <?php endif; ?>
</form>

<div class="table-container" id="voucher-table">
    <table class="category-table">
        <thead>
            <tr>
                <?= table_headers($fields, $sort, $dir, "&search=$searchTerm&status=$statusFilter&page=$page") ?>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($vouchers)): ?>
                <tr>
                    <td colspan="<?= count($fields) + 2 ?>" class="no-results">
                        No vouchers found
                        <?php if ($searchTerm): ?>
                            for: <strong><?= htmlspecialchars($searchTerm) ?></strong>
                        <?php endif; ?>
                        <?php if ($statusFilter): ?>
                            with status: <strong><?= htmlspecialchars($statusFilter) ?></strong>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($vouchers as $voucher): ?>
                    <tr>
                        <td><?= htmlspecialchars($voucher->voucherCode) ?></td>
                        <td>RM <?= number_format($voucher->discountAmount, 2) ?></td>
                        <td><?= $voucher->usageLimit == 0 ? 'Unlimited' : $voucher->usageLimit ?></td>
                        <td><?= $voucher->timesUsed ?></td>
                        <td><?= $voucher->expiryDate ? date('d M Y, h:i A', strtotime($voucher->expiryDate)) : 'No expiry' ?></td>
                        <td><?= date('d M Y, h:i A', strtotime($voucher->createdDate)) ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower($voucher->status) ?>">
                                <?= htmlspecialchars($voucher->status) ?>
                            </span>
                        </td>
                        <td class="actions">
                            <?php if ($voucher->status === 'Active'): ?>
                                <a href="editVoucher.php?voucherID=<?= $voucher->voucherID ?>" class="btn btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                                <a href="disableVoucher.php?voucherID=<?= $voucher->voucherID ?>" class="btn btn-deactivate">
                                    <i class="fas fa-ban"></i> Disable
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
    <?= $p->html("&search=$searchTerm&status=$statusFilter") ?>
</div>
