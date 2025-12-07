<?php 
include 'include/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$pageTitle = 'My Pre-Orders - Vportal';
$user_id = $_SESSION['user_id'];
$preorders = [];

if ($con) {
    $result = mysqli_query($con, "
        SELECT pr.*, p.name as product_name, p.price, p.images, p.brand
        FROM preorders pr 
        LEFT JOIN products p ON pr.product_id = p.id
        WHERE pr.user_id = $user_id 
        ORDER BY pr.created_at DESC
    ");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $preorders[] = $row;
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
                        <h3 class="mb-0">My Pre-Orders</h3>
                        <span class="badge bg-warning"><?php echo count($preorders); ?> Pre-Orders</span>
                    </div>
                    
                    <?php if (empty($preorders)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-clock mb-3" style="font-size: 4rem; color: var(--primary); opacity: 0.5;"></i>
                        <h4>No pre-orders yet</h4>
                        <p class="text-muted">Pre-order your favorite EV bikes.</p>
                        <a href="bikes.php" class="btn btn-primary">Browse EV Bikes</a>
                    </div>
                    <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($preorders as $po): ?>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <?php 
                                        $images = json_decode($po['images'], true);
                                        $image = !empty($images) ? $images[0] : 'https://via.placeholder.com/100x80?text=EV';
                                        ?>
                                        <img src="<?php echo htmlspecialchars($image); ?>" class="rounded me-3" style="width: 100px; height: 80px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($po['product_name']); ?></h5>
                                            <p class="text-muted small mb-1"><?php echo htmlspecialchars($po['brand']); ?></p>
                                            <span class="price"><?php echo formatPrice($po['price']); ?></span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Order #: <?php echo htmlspecialchars($po['order_number']); ?></small><br>
                                            <small class="text-muted"><?php echo date('M d, Y', strtotime($po['created_at'])); ?></small>
                                        </div>
                                        <span class="badge bg-<?php 
                                            echo $po['status'] == 'completed' ? 'success' : 
                                                ($po['status'] == 'cancelled' ? 'danger' : 
                                                ($po['status'] == 'confirmed' ? 'info' : 'warning')); 
                                        ?> fs-6">
                                            <?php echo ucfirst($po['status']); ?>
                                        </span>
                                    </div>
                                    <?php if ($po['color']): ?>
                                    <small class="text-muted d-block mt-2">Color: <?php echo htmlspecialchars($po['color']); ?></small>
                                    <?php endif; ?>
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
