<?php
session_start();
include '../include/config.php';
include 'include/header.php';

$pageTitle = 'Orders';
$admin_id = $_SESSION['admin_id'];
$success = '';

if (isset($_POST['update_order'])) {
    $order_id = intval($_POST['order_id']);
    $order_status = sanitize($_POST['order_status']);
    $payment_status = sanitize($_POST['payment_status']);
    
    mysqli_query($con, "UPDATE orders SET order_status = '$order_status', payment_status = '$payment_status' WHERE id = $order_id AND admin_id = $admin_id");
    $success = 'Order updated successfully!';
}

$orders = [];
$result = mysqli_query($con, "
    SELECT o.*, u.full_name as customer_name, u.email, u.phone,
           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.admin_id = $admin_id 
    ORDER BY o.created_at DESC
");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-shopping-bag me-2" style="color: var(--primary);"></i>Orders</h2>
    <span class="badge bg-success fs-6"><?php echo count($orders); ?> Total</span>
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
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No orders yet.</td></tr>
                <?php else: ?>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($o['order_number']); ?></strong></td>
                    <td>
                        <?php echo htmlspecialchars($o['customer_name']); ?>
                        <br><small class="text-muted"><?php echo htmlspecialchars($o['email']); ?></small>
                    </td>
                    <td><span class="badge bg-info"><?php echo $o['items_count']; ?> items</span></td>
                    <td class="text-success fw-bold"><?php echo formatPrice($o['total_amount']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $o['payment_status'] == 'paid' ? 'success' : ($o['payment_status'] == 'refunded' ? 'danger' : 'warning'); ?>">
                            <?php echo ucfirst($o['payment_status']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?php 
                            echo $o['order_status'] == 'delivered' ? 'success' : 
                                ($o['order_status'] == 'cancelled' ? 'danger' : 
                                ($o['order_status'] == 'shipped' ? 'info' : 'warning')); 
                        ?>">
                            <?php echo ucfirst($o['order_status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $o['id']; ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>

                <div class="modal fade" id="orderModal<?php echo $o['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content" style="background: var(--accent); color: #fff;">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">Order #<?php echo $o['order_number']; ?></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                    <input type="hidden" name="update_order" value="1">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Order Status</label>
                                        <select name="order_status" class="form-select">
                                            <option value="pending" <?php echo $o['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $o['order_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="processing" <?php echo $o['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="shipped" <?php echo $o['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo $o['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $o['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Payment Status</label>
                                        <select name="payment_status" class="form-select">
                                            <option value="pending" <?php echo $o['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="partial" <?php echo $o['payment_status'] == 'partial' ? 'selected' : ''; ?>>Partial</option>
                                            <option value="paid" <?php echo $o['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                            <option value="refunded" <?php echo $o['payment_status'] == 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                                        </select>
                                    </div>
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
