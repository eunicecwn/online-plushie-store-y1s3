<?php
require '../../_base.php';
require_once '../../lib/SimplePager.php';

// Get request parameters
$searchTerm = req('search');
$statusFilter = req('status');
$GLOBALS['search'] = $searchTerm;

// Define sortable columns
$fields = [
    'staffID'   => 'Staff ID',
    'staffName' => 'Name',
    'staffEmail' => 'Email',
    'phoneNumber' => 'Phone'
];

// Validate sorting parameters
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'staffName';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$page = req('page', 1);

// Build WHERE conditions
$whereConditions = ["1=1"];
$params = [];

if ($searchTerm) {
    $whereConditions[] = "(staffID LIKE ? OR staffName LIKE ?)";
    array_push($params, "%$searchTerm%", "%$searchTerm%");
}

if ($statusFilter && in_array($statusFilter, ['active', 'inactive'])) {
    $whereConditions[] = "status = ?";
    $params[] = $statusFilter;
}

$whereClause = implode(' AND ', $whereConditions);

// Create paginated query
$p = new SimplePager(
    "SELECT * FROM staff WHERE $whereClause ORDER BY $sort $dir",
    $params,
    7,
    $page
);

$staff = $p->result;

$_title = 'KAWAII.SellerCentre | View All Staff';
include '../../_headadmin.php';
?>
<link rel="stylesheet" href="/css/admin.css">


    <h1 class="staff-title">View All Staff</h1>

    <form class="search-form" method="GET">
        <input type="text" name="search" placeholder="Search by ID or Name" value="<?= encode($searchTerm) ?>">

        <select name="status">
            <option value="">All Status</option>
            <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>

        <button type="submit" class="search-button">Apply</button>

        <?php if ($searchTerm || $statusFilter): ?>
            <a href="staffTable.php" class="btn btn-reset">Reset</a>
        <?php endif; ?>
    </form>

    <p class="record-count">
        <?= $p->count ?> of <?= $p->item_count ?> record(s) |
        Page <?= $p->page ?> of <?= $p->page_count ?>
    </p>

    <div class="table-container">
        <table class="category-table">
            <thead>
                <tr>
                    <th>No.</th>
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
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($staff)): ?>
                    <tr>
                        <td colspan="7" class="no-results">
                            No staff found
                            <?php if ($searchTerm): ?>
                                matching: <strong><?= encode($searchTerm) ?></strong>
                            <?php endif; ?>
                            <?php if ($statusFilter): ?>
                                with status: <strong><?= ucfirst(encode($statusFilter)) ?></strong>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($staff as $index => $staffMember): ?>
                        <?php
                        $statusClass = 'status-badge ';
                        switch ($staffMember->status) {
                            case 'active':
                                $statusClass .= 'status-active';
                                break;
                            case 'inactive':
                                $statusClass .= 'status-inactive';
                                break;
                        }
                        ?>
                        <tr>
                            <td><?= $index + 1 + (($page - 1) * 10) ?></td>
                            <td><?= encode($staffMember->staffID) ?></td>
                            <td><?= encode($staffMember->staffName) ?></td>
                            <td><?= encode($staffMember->staffEmail) ?></td>
                            <td><?= encode($staffMember->phoneNumber) ?></td>
                            <td>
                                <span class="<?= $statusClass ?>">
                                    <?= ucfirst($staffMember->status) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($staffMember->status === 'active') : ?>
                                    <a href="deactivateStaff.php?id=<?= $staffMember->staffID ?>" class="btn btn-deactivate">
                                        <i class="fas fa-ban"></i> Deactivate
                                    </a>
                                <?php else : ?>
                                    <a href="activateStaff.php?id=<?= $staffMember->staffID ?>" class="btn btn-activate">
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

    <div class="pagination">
        <?= $p->html(http_build_query([
            'search' => $searchTerm,
            'status' => $statusFilter,
            'sort' => $sort,
            'dir' => $dir
        ])) ?>
    </div>