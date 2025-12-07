<?php 
include 'include/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('bikes.php');
}

$product_id = intval($_GET['id']);
$product = null;

if ($con) {
    $query = "SELECT p.*, c.name as category_name, a.showroom_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              LEFT JOIN admin a ON p.admin_id = a.id 
              WHERE p.id = $product_id AND p.is_active = 1";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        mysqli_query($con, "UPDATE products SET views = views + 1 WHERE id = $product_id");
    }
}

if (!$product) {
    redirect('bikes.php');
}

$pageTitle = htmlspecialchars($product['name']) . ' - Vportal';
include 'include/header.php';

$images = json_decode($product['images'], true) ?: ['https://via.placeholder.com/600x400?text=EV+Bike'];
$colors = json_decode($product['colors'], true) ?: [];
$specifications = json_decode($product['specifications'], true) ?: [];
?>

<div style="padding-top: 100px;"></div>

<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none" style="color: var(--primary);">Home</a></li>
                <li class="breadcrumb-item"><a href="bikes.php" class="text-decoration-none" style="color: var(--primary);">EV Bikes</a></li>
                <li class="breadcrumb-item active text-muted"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card p-3">
                    <img src="<?php echo htmlspecialchars($images[0]); ?>" class="img-fluid rounded" id="mainImage" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php if (count($images) > 1): ?>
                    <div class="d-flex gap-2 mt-3">
                        <?php foreach ($images as $index => $img): ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover; cursor: pointer;" onclick="document.getElementById('mainImage').src = this.src">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-6">
                <span class="badge bg-success mb-2"><?php echo htmlspecialchars($product['category_name'] ?? 'Electric Vehicle'); ?></span>
                <h1 class="mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="text-muted mb-3">by <?php echo htmlspecialchars($product['brand']); ?></p>
                
                <div class="mb-4">
                    <span class="price" style="font-size: 2rem;"><?php echo formatPrice($product['price']); ?></span>
                    <?php if ($product['sale_price'] && $product['sale_price'] > $product['price']): ?>
                    <span class="text-muted text-decoration-line-through ms-2"><?php echo formatPrice($product['sale_price']); ?></span>
                    <?php endif; ?>
                </div>
                
                <p class="mb-4"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                
                <div class="row g-3 mb-4">
                    <?php if ($product['range_km']): ?>
                    <div class="col-6 col-md-3">
                        <div class="card p-3 text-center h-100">
                            <i class="fas fa-road mb-2" style="color: var(--primary);"></i>
                            <div class="fw-bold"><?php echo $product['range_km']; ?> km</div>
                            <small class="text-muted">Range</small>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($product['top_speed']): ?>
                    <div class="col-6 col-md-3">
                        <div class="card p-3 text-center h-100">
                            <i class="fas fa-tachometer-alt mb-2" style="color: var(--primary);"></i>
                            <div class="fw-bold"><?php echo $product['top_speed']; ?> km/h</div>
                            <small class="text-muted">Top Speed</small>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($product['charging_time']): ?>
                    <div class="col-6 col-md-3">
                        <div class="card p-3 text-center h-100">
                            <i class="fas fa-bolt mb-2" style="color: var(--primary);"></i>
                            <div class="fw-bold"><?php echo htmlspecialchars($product['charging_time']); ?></div>
                            <small class="text-muted">Charging</small>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($product['motor_power']): ?>
                    <div class="col-6 col-md-3">
                        <div class="card p-3 text-center h-100">
                            <i class="fas fa-cog mb-2" style="color: var(--primary);"></i>
                            <div class="fw-bold"><?php echo htmlspecialchars($product['motor_power']); ?></div>
                            <small class="text-muted">Motor</small>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($colors)): ?>
                <div class="mb-4">
                    <h5>Available Colors</h5>
                    <div class="d-flex gap-2">
                        <?php foreach ($colors as $color): ?>
                        <div class="rounded-circle border border-2" style="width: 35px; height: 35px; background: <?php echo htmlspecialchars($color); ?>; cursor: pointer;" title="<?php echo htmlspecialchars($color); ?>"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (isLoggedIn()): ?>
                <form action="preorder.php" method="POST" class="mb-3">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                            <i class="fas fa-shopping-cart me-2"></i>Pre-Order Now
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-lg" onclick="addToWishlist(<?php echo $product['id']; ?>)">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </form>
                <?php else: ?>
                <div class="alert alert-info">
                    <a href="login.php" class="alert-link">Login</a> or <a href="register.php" class="alert-link">Register</a> to pre-order this vehicle.
                </div>
                <?php endif; ?>
                
                <?php if ($product['warranty']): ?>
                <p class="text-muted"><i class="fas fa-shield-alt me-2"></i>Warranty: <?php echo htmlspecialchars($product['warranty']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#specs">Specifications</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#features">Features</button>
                    </li>
                </ul>
                <div class="tab-content card p-4" style="border-top: none; border-radius: 0 0 15px 15px;">
                    <div class="tab-pane fade show active" id="specs">
                        <ul class="specs-list">
                            <?php if ($product['battery_type']): ?>
                            <li><span>Battery Type</span><span><?php echo htmlspecialchars($product['battery_type']); ?></span></li>
                            <?php endif; ?>
                            <?php if ($product['battery_capacity']): ?>
                            <li><span>Battery Capacity</span><span><?php echo htmlspecialchars($product['battery_capacity']); ?></span></li>
                            <?php endif; ?>
                            <?php if ($product['range_km']): ?>
                            <li><span>Range</span><span><?php echo $product['range_km']; ?> km</span></li>
                            <?php endif; ?>
                            <?php if ($product['top_speed']): ?>
                            <li><span>Top Speed</span><span><?php echo $product['top_speed']; ?> km/h</span></li>
                            <?php endif; ?>
                            <?php if ($product['charging_time']): ?>
                            <li><span>Charging Time</span><span><?php echo htmlspecialchars($product['charging_time']); ?></span></li>
                            <?php endif; ?>
                            <?php if ($product['motor_power']): ?>
                            <li><span>Motor Power</span><span><?php echo htmlspecialchars($product['motor_power']); ?></span></li>
                            <?php endif; ?>
                            <?php if ($product['weight']): ?>
                            <li><span>Weight</span><span><?php echo $product['weight']; ?> kg</span></li>
                            <?php endif; ?>
                            <?php if ($product['warranty']): ?>
                            <li><span>Warranty</span><span><?php echo htmlspecialchars($product['warranty']); ?></span></li>
                            <?php endif; ?>
                            <?php foreach ($specifications as $key => $value): ?>
                            <li><span><?php echo htmlspecialchars($key); ?></span><span><?php echo htmlspecialchars($value); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="features">
                        <ul>
                            <li>Zero Emissions - Eco-friendly transportation</li>
                            <li>Low Running Cost - Save on fuel expenses</li>
                            <li>Smart Connectivity - Mobile app integration</li>
                            <li>Regenerative Braking - Extended range</li>
                            <li>Silent Operation - Noise-free riding</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function addToWishlist(productId) {
    fetch('ajax/wishlist.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=add&product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success', 'Added to wishlist!', 'success');
        } else {
            Swal.fire('Error', data.message || 'Failed to add to wishlist', 'error');
        }
    });
}
</script>

<?php include 'include/footer.php'; ?>
