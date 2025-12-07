<?php 
include 'include/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$pageTitle = 'My Orders - Vportal';
$user_id = $_SESSION['user_id'];
$orders = [];

if ($con) {
    $result = mysqli_query($con, "
        SELECT o.*, 
               (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count,
               (SELECT GROUP_CONCAT(p.name SEPARATOR ', ') FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = o.id) as product_names
        FROM orders o 
        WHERE o.user_id = $user_id 
        ORDER BY o.created_at DESC
    ");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
    }
}

include 'include/header.php';
?>

<div style="padding-top: 100px;"></div>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'include/user-sidebar.php'; ?>
            </div>
            
            <div class="col-lg-9">
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">My Orders</h3>
                        <span class="badge bg-success"><?php echo count($orders); ?> Orders</span>
                    </div>
                    
                    <?php if (empty($orders)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag mb-3" style="font-size: 4rem; color: var(--primary); opacity: 0.5;"></i>
                        <h4>No orders yet</h4>
                        <p class="text-muted">Start shopping to see your orders here.</p>
                        <a href="bikes.php" class="btn btn-primary">Browse EV Bikes</a>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Products</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                    <td><?php echo htmlspecialchars(substr($order['product_names'] ?? 'N/A', 0, 50)); ?></td>
                                    <td class="text-success"><?php echo formatPrice($order['total_amount']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $order['order_status'] == 'delivered' ? 'success' : 
                                                ($order['order_status'] == 'cancelled' ? 'danger' : 
                                                ($order['order_status'] == 'shipped' ? 'info' : 'warning')); 
                                        ?>">
                                            <?php echo ucfirst($order['order_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'include/footer.php'; ?>
