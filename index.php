<?php 
include 'include/config.php';

if (!DB_CONNECTED) {
    showSetupGuide();
}

$pageTitle = 'Vportal - EV Showroom | Home';

$featured_products = [];
$categories = [];

if ($con) {
    $result = mysqli_query($con, "SELECT * FROM products WHERE is_featured = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT 6");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $featured_products[] = $row;
        }
    }
    
    $cat_result = mysqli_query($con, "SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC");
    if ($cat_result) {
        while ($row = mysqli_fetch_assoc($cat_result)) {
            $categories[] = $row;
        }
    }
}

include 'include/header.php';
?>

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title">
                    The Future of <span>Electric</span> Mobility
                </h1>
                <p class="lead mb-4">Experience the next generation of eco-friendly transportation. Zero emissions, maximum performance, endless possibilities.</p>
                <div class="d-flex gap-3">
                    <a href="bikes.php" class="btn btn-primary btn-lg">Explore Bikes</a>
                    <a href="about.php" class="btn btn-outline-primary btn-lg">Learn More</a>
                </div>
                <div class="row mt-5">
                    <div class="col-4 text-center">
                        <h3 class="mb-0" style="color: var(--primary);">150+</h3>
                        <small class="text-muted">KM Range</small>
                    </div>
                    <div class="col-4 text-center">
                        <h3 class="mb-0" style="color: var(--primary);">3hrs</h3>
                        <small class="text-muted">Fast Charge</small>
                    </div>
                    <div class="col-4 text-center">
                        <h3 class="mb-0" style="color: var(--primary);">80+</h3>
                        <small class="text-muted">km/h Speed</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="position-relative">
                    <div style="width: 400px; height: 400px; background: var(--gradient); border-radius: 50%; opacity: 0.1; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></div>
                    <i class="fas fa-motorcycle" style="font-size: 15rem; color: var(--primary); position: relative; z-index: 1;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: var(--accent);">
    <div class="container">
        <h2 class="section-title">Why Choose <span>Electric</span>?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h4>Eco-Friendly</h4>
                    <p class="text-muted">Zero emissions mean a cleaner environment for future generations.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <h4>Cost Effective</h4>
                    <p class="text-muted">Save up to 80% on fuel costs compared to petrol vehicles.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h4>Low Maintenance</h4>
                    <p class="text-muted">Fewer moving parts mean less maintenance and repairs.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="section-title">Browse by <span>Category</span></h2>
        <div class="row g-4">
            <?php if (empty($categories)): ?>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <i class="fas fa-motorcycle mb-3" style="font-size: 3rem; color: var(--primary);"></i>
                        <h4>Electric Scooters</h4>
                        <p class="text-muted">Two-wheeler electric scooters</p>
                        <a href="bikes.php?category=electric-scooters" class="btn btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <i class="fas fa-bicycle mb-3" style="font-size: 3rem; color: var(--primary);"></i>
                        <h4>Electric Bikes</h4>
                        <p class="text-muted">Electric motorcycles and bikes</p>
                        <a href="bikes.php?category=electric-bikes" class="btn btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <i class="fas fa-biking mb-3" style="font-size: 3rem; color: var(--primary);"></i>
                        <h4>Electric Cycles</h4>
                        <p class="text-muted">Electric bicycles and e-cycles</p>
                        <a href="bikes.php?category=electric-cycles" class="btn btn-outline-primary">View All</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <i class="fas fa-motorcycle mb-3" style="font-size: 3rem; color: var(--primary);"></i>
                        <h4><?php echo htmlspecialchars($category['name']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($category['description']); ?></p>
                        <a href="bikes.php?category=<?php echo htmlspecialchars($category['slug']); ?>" class="btn btn-outline-primary">View All</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (!empty($featured_products)): ?>
<section class="py-5" style="background: var(--accent);">
    <div class="container">
        <h2 class="section-title">Featured <span>EV Bikes</span></h2>
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <?php 
                    $images = json_decode($product['images'], true);
                    $image = !empty($images) ? $images[0] : 'https://via.placeholder.com/400x250?text=EV+Bike';
                    ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="text-muted small"><?php echo htmlspecialchars($product['brand']); ?></p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="price"><?php echo formatPrice($product['price']); ?></span>
                            <?php if ($product['range_km']): ?>
                            <span class="badge bg-success"><?php echo $product['range_km']; ?> km range</span>
                            <?php endif; ?>
                        </div>
                        <a href="bike-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="section-title text-start">Ready to <span>Go Electric</span>?</h2>
                <p class="lead text-muted">Join thousands of riders who have already made the switch to electric. Experience the thrill of silent, powerful, and sustainable mobility.</p>
                <ul class="list-unstyled">
                    <li class="mb-3"><i class="fas fa-check-circle me-2" style="color: var(--primary);"></i> Government subsidies available</li>
                    <li class="mb-3"><i class="fas fa-check-circle me-2" style="color: var(--primary);"></i> Easy EMI options</li>
                    <li class="mb-3"><i class="fas fa-check-circle me-2" style="color: var(--primary);"></i> Free home charging setup</li>
                    <li class="mb-3"><i class="fas fa-check-circle me-2" style="color: var(--primary);"></i> 3-year warranty included</li>
                </ul>
                <a href="register.php" class="btn btn-primary btn-lg">Get Started Today</a>
            </div>
            <div class="col-lg-6 text-center">
                <i class="fas fa-charging-station" style="font-size: 12rem; color: var(--primary); opacity: 0.8;"></i>
            </div>
        </div>
    </div>
</section>

<?php include 'include/footer.php'; ?>
