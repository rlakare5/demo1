<?php
session_start();
include '../include/config.php';
include 'include/header.php';

$pageTitle = 'Customers';
$admin_id = $_SESSION['admin_id'];
$success = '';
$error = '';

if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $user_id = intval($_GET['toggle']);
    mysqli_query($con, "UPDATE users SET is_blocked = NOT is_blocked WHERE id = $user_id AND admin_id = $admin_id");
    $success = 'Customer status updated!';
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    if (mysqli_query($con, "DELETE FROM users WHERE id = $user_id AND admin_id = $admin_id")) {
        $success = 'Customer deleted!';
    }
}

$customers = [];
$result = mysqli_query($con, "
    SELECT u.*, 
           (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
           (SELECT COUNT(*) FROM preorders WHERE user_id = u.id) as preorder_count
    FROM users u 
    WHERE u.admin_id = $admin_id 
    ORDER BY u.created_at DESC
");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $customers[] = $row;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users me-2" style="color: var(--primary);"></i>Customers</h2>
    <span class="badge bg-info fs-6"><?php echo count($customers); ?> Total</span>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Orders</th>
                    <th>Pre-Orders</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No customers yet.</td></tr>
                <?php else: ?>
                <?php foreach ($customers as $c): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($c['full_name']); ?></strong>
                        <br><small class="text-muted">@<?php echo htmlspecialchars($c['username']); ?></small>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($c['email']); ?>
                        <br><small class="text-muted"><?php echo htmlspecialchars($c['phone'] ?? 'No phone'); ?></small>
                    </td>
                    <td><span class="badge bg-success"><?php echo $c['order_count']; ?></span></td>
                    <td><span class="badge bg-warning"><?php echo $c['preorder_count']; ?></span></td>
                    <td>
                        <?php if ($c['is_blocked']): ?>
                        <span class="badge bg-danger">Blocked</span>
                        <?php elseif ($c['is_active']): ?>
                        <span class="badge bg-success">Active</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($c['created_at'])); ?></td>
                    <td>
                        <a href="customers.php?toggle=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-<?php echo $c['is_blocked'] ? 'success' : 'warning'; ?>">
                            <i class="fas fa-<?php echo $c['is_blocked'] ? 'unlock' : 'ban'; ?>"></i>
                        </a>
                        <button onclick="confirmDelete('customers.php?delete=<?php echo $c['id']; ?>', '<?php echo htmlspecialchars($c['full_name']); ?>')" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>
