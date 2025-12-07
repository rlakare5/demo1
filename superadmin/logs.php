<?php
$pageTitle = 'System Logs';
include 'include/header.php';

$logs = [];
$result = mysqli_query($con, "SELECT l.*, 
                              CASE 
                                  WHEN l.user_type = 'admin' THEN (SELECT showroom_name FROM admin WHERE id = l.user_id)
                                  WHEN l.user_type = 'user' THEN (SELECT full_name FROM users WHERE id = l.user_id)
                                  WHEN l.user_type = 'super_admin' THEN (SELECT full_name FROM super_admin WHERE id = l.user_id)
                                  ELSE 'System'
                              END as user_name
                              FROM activity_logs l 
                              ORDER BY l.created_at DESC 
                              LIMIT 100");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $logs[] = $row;
    }
}

$log_icons = [
    'login' => 'fas fa-sign-in-alt text-success',
    'logout' => 'fas fa-sign-out-alt text-warning',
    'create' => 'fas fa-plus text-info',
    'update' => 'fas fa-edit text-primary',
    'delete' => 'fas fa-trash text-danger',
    'order' => 'fas fa-shopping-cart text-success',
    'payment' => 'fas fa-credit-card text-success',
    'default' => 'fas fa-circle text-muted'
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-history me-2" style="color: var(--primary);"></i>System Logs</h2>
    <span class="text-muted">Last 100 activities</span>
</div>

<div class="card p-4">
    <?php if (empty($logs)): ?>
    <div class="text-center text-muted py-5">
        <i class="fas fa-clipboard-list fa-3x mb-3" style="opacity: 0.5;"></i>
        <p>No activity logs found.</p>
        <p class="small">Activity logs will appear here as users interact with the system.</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th width="50"></th>
                    <th>Activity</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Details</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td>
                        <?php 
                        $icon_class = $log_icons[$log['action']] ?? $log_icons['default'];
                        ?>
                        <i class="<?php echo $icon_class; ?>"></i>
                    </td>
                    <td><strong><?php echo ucfirst(str_replace('_', ' ', $log['action'])); ?></strong></td>
                    <td><?php echo htmlspecialchars($log['user_name'] ?? 'Unknown'); ?></td>
                    <td><span class="badge bg-secondary"><?php echo ucfirst($log['user_type'] ?? 'system'); ?></span></td>
                    <td class="text-muted small"><?php echo htmlspecialchars($log['description'] ?? '-'); ?></td>
                    <td class="text-muted small"><?php echo date('d M Y H:i', strtotime($log['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include 'include/footer.php'; ?>
