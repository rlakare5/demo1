<?php 
include 'include/config.php';
$pageTitle = 'EV Bikes - Vportal';

$products = [];
$categories = [];
$where = "WHERE p.is_active = 1";

if ($con) {
    $cat_result = mysqli_query($con, "SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC");
    if ($cat_result) {
        while ($row = mysqli_fetch_assoc($cat_result)) {
            $categories[] = $row;
        }
    }
    
    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $category_slug = sanitize($_GET['category']);
        $where .= " AND c.slug = '$category_slug'";
    }
    
    if (isset($_GET['brand']) && !empty($_GET['brand'])) {
        $brand = sanitize($_GET['brand']);
        $where .= " AND p.brand = '$brand'";
    }
    
    if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
        $min_price = floatval($_GET['min_price']);
        $where .= " AND p.price >= $min_price";
    }
    
    if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
        $max_price = floatval($_GET['max_price']);
        $where .= " AND p.price <= $max_price";
    }
    
    if (isset($_GET['range']) && is_numeric($_GET['range'])) {
        $range = intval($_GET['range']);
        $where .= " AND p.range_km >= $range";
    }
    
    $order = "ORDER BY p.created_at DESC";
    if (isset($_GET['sort'])) {
        switch ($_GET['sort']) {
            case 'price_low':
                $order = "ORDER BY p.price ASC";
                break;
            case 'price_high':
                $order = "ORDER BY p.price DESC";
                break;
            case 'name':
                $order = "ORDER BY p.name ASC";
                break;
            case 'range':
                $order = "ORDER BY p.range_km DESC";
                break;
        }
    }
    
    $query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              $where $order";
    
    $result = mysqli_query($con, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
}

include 'include/header.php';
?>

<div style="padding-top: 100px;"></div>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="card p-4 mb-4">
                    <h5 class="mb-3">Filters</h5>
                    <form method="GET" action="bikes.php">
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-control">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['slug']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['slug']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Minimum Range (km)</label>
                            <input type="number" name="range" class="form-control" placeholder="e.g. 100" value="<?php echo isset($_GET['range']) ? htmlspecialchars($_GET['range']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Sort By</label>
                            <select name="sort" class="form-control">
                                <option value="newest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                                <option value="price_low" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="range" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'range') ? 'selected' : ''; ?>>Range: Highest First</option>
                                <option value="name" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name') ? 'selected' : ''; ?>>Name: A-Z</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        <a href="bikes.php" class="btn btn-outline-primary w-100 mt-2">Clear Filters</a>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">EV Bikes <span style="color: var(--primary);">(<?php echo count($products); ?>)</span></h2>
                </div>
                
                <?php if (empty($products)): ?>
                <div class="card p-5 text-center">
                    <i class="fas fa-motorcycle mb-3" style="font-size: 4rem; color: var(--primary); opacity: 0.5;"></i>
                    <h4>No bikes found</h4>
                    <p class="text-muted">Try adjusting your filters or check back later for new arrivals.</p>
                    <a href="bikes.php" class="btn btn-primary">View All Bikes</a>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <?php 
                            $images = json_decode($product['images'], true);
                            $image = !empty($images) ? $images[0] : 'https://via.placeholder.com/400x250?text=EV+Bike';
                            ?>
                            <img src="<?php echo htmlspecialchars($image); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="card-body">
                                <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($product['category_name'] ?? 'EV Bike'); ?></span>
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="text-muted small mb-2"><?php echo htmlspecialchars($product['brand']); ?></p>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="price"><?php echo formatPrice($product['price']); ?></span>
                                    <?php if ($product['sale_price']): ?>
                                    <span class="text-muted text-decoration-line-through"><?php echo formatPrice($product['sale_price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="small text-muted mb-3">
                                    <?php if ($product['range_km']): ?>
                                    <span class="me-2"><i class="fas fa-road me-1"></i><?php echo $product['range_km']; ?> km</span>
                                    <?php endif; ?>
                                    <?php if ($product['top_speed']): ?>
                                    <span><i class="fas fa-tachometer-alt me-1"></i><?php echo $product['top_speed']; ?> km/h</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="bike-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary flex-grow-1">View Details</a>
                                    <?php if (isLoggedIn()): ?>
                                    <button class="btn btn-outline-primary" onclick="addToWishlist(<?php echo $product['id']; ?>)">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <?php endif; ?>
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
