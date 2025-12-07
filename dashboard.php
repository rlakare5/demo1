<?php 
include 'include/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$pageTitle = 'Dashboard - Vportal';
$user_id = $_SESSION['user_id'];

$user = [];
$orders = [];
$preorders = [];
$wishlist = [];

if ($con) {
    $result = mysqli_query($con, "SELECT * FROM users WHERE id = $user_id");
    if ($result) {
        $user = mysqli_fetch_assoc($result);
    }
    
    $orders_result = mysqli_query($con, "SELECT o.*, (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count FROM orders o WHERE o.user_id = $user_id ORDER BY o.created_at DESC LIMIT 5");
    if ($orders_result) {
        while ($row = mysqli_fetch_assoc($orders_result)) {
            $orders[] = $row;
        }
    }
    
    $preorders_result = mysqli_query($con, "SELECT pr.*, p.name as product_name FROM preorders pr LEFT JOIN products p ON pr.product_id = p.id WHERE pr.user_id = $user_id ORDER BY pr.created_at DESC LIMIT 5");
    if ($preorders_result) {
        while ($row = mysqli_fetch_assoc($preorders_result)) {
            $preorders[] = $row;
        }
    }
    
    $wishlist_result = mysqli_query($con, "SELECT w.*, p.name, p.price, p.images FROM wishlist w LEFT JOIN products p ON w.product_id = p.id WHERE w.user_id = $user_id ORDER BY w.created_at DESC");
    if ($wishlist_result) {
        while ($row = mysqli_fetch_assoc($wishlist_result)) {
            $wishlist[] = $row;
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
                <div class="card p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle" style="font-size: 5rem; color: var(--primary);"></i>
                        <h4 class="mt-3"><?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></h4>
                        <p class="text-muted small"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="my-orders.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="my-preorders.php"><i class="fas fa-clock me-2"></i>Pre-Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="my-wishlist.php"><i class="fas fa-heart me-2"></i>Wishlist</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php"><i class="fas fa-user-cog me-2"></i>Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-9">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card p-4 text-center">
                            <i class="fas fa-shopping-bag mb-2" style="font-size: 2rem; color: var(--primary);"></i>
                            <h3 class="mb-0"><?php echo count($orders); ?></h3>
                            <p class="text-muted mb-0">Orders</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 text-center">
                            <i class="fas fa-clock mb-2" style="font-size: 2rem; color: var(--primary);"></i>
                            <h3 class="mb-0"><?php echo count($preorders); ?></h3>
                            <p class="text-muted mb-0">Pre-Orders</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 text-center">
                            <i class="fas fa-heart mb-2" style="font-size: 2rem; color: var(--primary);"></i>
                            <h3 class="mb-0"><?php echo count($wishlist); ?></h3>
                            <p class="text-muted mb-0">Wishlist Items</p>
                        </div>
                    </div>
                </div>
                
                <div class="card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Recent Pre-Orders</h5>
                        <a href="my-preorders.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    
                    <?php if (empty($preorders)): ?>
                    <p class="text-muted text-center py-3">No pre-orders yet. <a href="bikes.php" style="color: var(--primary);">Browse EV Bikes</a></p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Product</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($preorders as $po): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($po['order_number']); ?></td>
                                    <td><?php echo htmlspecialchars($po['product_name']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $po['status'] == 'completed' ? 'success' : 
                                                ($po['status'] == 'cancelled' ? 'danger' : 
                                                ($po['status'] == 'confirmed' ? 'info' : 'warning')); 
                                        ?>">
                                            <?php echo ucfirst($po['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($po['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Wishlist</h5>
                        <a href="my-wishlist.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    
                    <?php if (empty($wishlist)): ?>
                    <p class="text-muted text-center py-3">Your wishlist is empty. <a href="bikes.php" style="color: var(--primary);">Add some bikes!</a></p>
                    <?php else: ?>
                    <div class="row g-3">
                        <?php foreach (array_slice($wishlist, 0, 3) as $item): ?>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <?php 
                                $images = json_decode($item['images'], true);
                                $image = !empty($images) ? $images[0] : 'https://via.placeholder.com/200x150?text=EV+Bike';
                                ?>
                                <img src="<?php echo htmlspecialchars($image); ?>" class="card-img-top" style="height: 120px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <span class="price small"><?php echo formatPrice($item['price']); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'include/footer.php'; ?>
