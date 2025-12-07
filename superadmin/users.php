<?php
$pageTitle = 'All Users';
include 'include/header.php';

$success = '';
$error = '';

if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $result = mysqli_query($con, "SELECT is_active FROM users WHERE id = $id");
    if ($result) {
        $user = mysqli_fetch_assoc($result);
        $new_status = $user['is_active'] ? 0 : 1;
        mysqli_query($con, "UPDATE users SET is_active = $new_status WHERE id = $id");
        $success = 'User status updated!';
    }
}

$users = [];
$result = mysqli_query($con, "SELECT u.*, a.showroom_name FROM users u LEFT JOIN admin a ON u.admin_id = a.id ORDER BY u.created_at DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users me-2" style="color: var(--primary);"></i>All Users</h2>
    <span class="badge bg-primary fs-6"><?php echo count($users); ?> Total Users</span>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Showroom</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No users found.</td></tr>
                <?php else: ?>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($u['full_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo htmlspecialchars($u['phone'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($u['showroom_name'] ?? 'N/A'); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $u['is_active'] ? 'success' : 'secondary'; ?>">
                            <?php echo $u['is_active'] ? 'Active' : 'Blocked'; ?>
                        </span>
                    </td>
                    <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                    <td>
                        <a href="users.php?toggle=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-warning" title="Toggle Status"><i class="fas fa-ban"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>
