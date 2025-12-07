<?php
session_start();
include '../include/config.php';
include 'include/header.php';

$pageTitle = 'Pre-Orders';
$admin_id = $_SESSION['admin_id'];
$success = '';

if (isset($_POST['update_status']) && isset($_POST['preorder_id'])) {
    $preorder_id = intval($_POST['preorder_id']);
    $status = sanitize($_POST['status']);
    $estimated_delivery = sanitize($_POST['estimated_delivery'] ?? '');
    
    $query = "UPDATE preorders SET status = '$status'";
    if (!empty($estimated_delivery)) {
        $query .= ", estimated_delivery = '$estimated_delivery'";
    }
    $query .= " WHERE id = $preorder_id AND admin_id = $admin_id";
    
    if (mysqli_query($con, $query)) {
        $success = 'Pre-order updated successfully!';
    }
}

$preorders = [];
$result = mysqli_query($con, "
    SELECT pr.*, p.name as product_name, p.price, p.images, u.full_name as customer_name, u.email, u.phone
    FROM preorders pr 
    LEFT JOIN products p ON pr.product_id = p.id
    LEFT JOIN users u ON pr.user_id = u.id
    WHERE pr.admin_id = $admin_id 
    ORDER BY pr.created_at DESC
");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $preorders[] = $row;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-clock me-2" style="color: var(--primary);"></i>Pre-Orders</h2>
    <span class="badge bg-warning fs-6"><?php echo count($preorders); ?> Total</span>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($preorders)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No pre-orders yet.</td></tr>
                <?php else: ?>
                <?php foreach ($preorders as $po): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($po['order_number']); ?></strong></td>
                    <td>
                        <?php echo htmlspecialchars($po['customer_name']); ?>
                        <br><small class="text-muted"><?php echo htmlspecialchars($po['email']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($po['product_name']); ?></td>
                    <td class="text-success"><?php echo formatPrice($po['price']); ?></td>
                    <td>
                        <span class="badge bg-<?php 
                            echo $po['status'] == 'completed' ? 'success' : 
                                ($po['status'] == 'cancelled' ? 'danger' : 
                                ($po['status'] == 'confirmed' ? 'info' : 
                                ($po['status'] == 'processing' ? 'primary' : 'warning'))); 
                        ?>">
                            <?php echo ucfirst($po['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($po['created_at'])); ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal<?php echo $po['id']; ?>">
                            <i class="fas fa-edit"></i> Update
                        </button>
                    </td>
                </tr>

                <div class="modal fade" id="modal<?php echo $po['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content" style="background: var(--accent); color: #fff;">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">Update Pre-Order #<?php echo $po['order_number']; ?></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="preorder_id" value="<?php echo $po['id']; ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Customer</label>
                                        <p class="mb-1"><strong><?php echo htmlspecialchars($po['customer_name']); ?></strong></p>
                                        <small class="text-muted"><?php echo htmlspecialchars($po['email']); ?> | <?php echo htmlspecialchars($po['phone']); ?></small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Product</label>
                                        <p class="mb-0"><?php echo htmlspecialchars($po['product_name']); ?> - <?php echo formatPrice($po['price']); ?></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="pending" <?php echo $po['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $po['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="processing" <?php echo $po['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="ready" <?php echo $po['status'] == 'ready' ? 'selected' : ''; ?>>Ready</option>
                                            <option value="completed" <?php echo $po['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $po['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Estimated Delivery</label>
                                        <input type="date" name="estimated_delivery" class="form-control" value="<?php echo $po['estimated_delivery'] ?? ''; ?>">
                                    </div>
                                    
                                    <?php if ($po['notes']): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Customer Notes</label>
                                        <p class="text-muted small"><?php echo htmlspecialchars($po['notes']); ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>
