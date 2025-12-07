<?php 
include 'include/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$pageTitle = 'My Wishlist - Vportal';
$user_id = $_SESSION['user_id'];

if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    mysqli_query($con, "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
    redirect('my-wishlist.php');
}

$wishlist = [];

if ($con) {
    $result = mysqli_query($con, "
        SELECT w.*, p.name, p.price, p.images, p.brand, p.range_km, p.top_speed, p.is_active
        FROM wishlist w 
        LEFT JOIN products p ON w.product_id = p.id
        WHERE w.user_id = $user_id 
        ORDER BY w.created_at DESC
    ");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
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
                <?php include 'include/user-sidebar.php'; ?>
            </div>
            
            <div class="col-lg-9">
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">My Wishlist</h3>
                        <span class="badge bg-danger"><?php echo count($wishlist); ?> Items</span>
                    </div>
                    
                    <?php if (empty($wishlist)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-heart mb-3" style="font-size: 4rem; color: var(--primary); opacity: 0.5;"></i>
                        <h4>Your wishlist is empty</h4>
                        <p class="text-muted">Save your favorite bikes for later.</p>
                        <a href="bikes.php" class="btn btn-primary">Browse EV Bikes</a>
                    </div>
                    <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($wishlist as $item): ?>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <?php 
                                $images = json_decode($item['images'], true);
                                $image = !empty($images) ? $images[0] : 'https://via.placeholder.com/300x200?text=EV+Bike';
                                ?>
                                <img src="<?php echo htmlspecialchars($image); ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                    <p class="text-muted small mb-2"><?php echo htmlspecialchars($item['brand']); ?></p>
                                    <p class="price mb-2"><?php echo formatPrice($item['price']); ?></p>
                                    <div class="small text-muted mb-3">
                                        <?php if ($item['range_km']): ?>
                                        <span class="me-2"><i class="fas fa-road me-1"></i><?php echo $item['range_km']; ?> km</span>
                                        <?php endif; ?>
                                        <?php if ($item['top_speed']): ?>
                                        <span><i class="fas fa-tachometer-alt me-1"></i><?php echo $item['top_speed']; ?> km/h</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="bike-detail.php?id=<?php echo $item['product_id']; ?>" class="btn btn-primary btn-sm flex-grow-1">View</a>
                                        <a href="my-wishlist.php?remove=<?php echo $item['product_id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove from wishlist?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
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
