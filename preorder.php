<?php 
include 'include/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$pageTitle = 'Pre-Order - Vportal';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id'] ?? 0);
    $color = sanitize($_POST['color'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');
    $user_id = $_SESSION['user_id'];
    $admin_id = $_SESSION['admin_id'];
    
    if ($product_id <= 0) {
        $error = 'Invalid product selected.';
    } else {
        $product_check = mysqli_query($con, "SELECT * FROM products WHERE id = $product_id AND is_active = 1");
        if (mysqli_num_rows($product_check) == 0) {
            $error = 'Product not available.';
        } else {
            $order_number = 'PRE' . date('Ymd') . strtoupper(substr(uniqid(), -6));
            
            $query = "INSERT INTO preorders (order_number, user_id, admin_id, product_id, color, notes, status) 
                      VALUES ('$order_number', $user_id, $admin_id, $product_id, '$color', '$notes', 'pending')";
            
            if (mysqli_query($con, $query)) {
                logActivity('user', $user_id, 'preorder', "Created pre-order $order_number");
                $success = "Pre-order placed successfully! Your order number is: $order_number";
            } else {
                $error = 'Failed to place pre-order. Please try again.';
            }
        }
    }
}

$product_id = intval($_GET['product_id'] ?? $_POST['product_id'] ?? 0);
$product = null;

if ($product_id > 0 && $con) {
    $result = mysqli_query($con, "SELECT * FROM products WHERE id = $product_id AND is_active = 1");
    if ($result) {
        $product = mysqli_fetch_assoc($result);
    }
}

include 'include/header.php';
?>

<div style="padding-top: 100px;"></div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if ($success): ?>
                <div class="card p-5 text-center">
                    <i class="fas fa-check-circle mb-3" style="font-size: 5rem; color: var(--primary);"></i>
                    <h2>Pre-Order Confirmed!</h2>
                    <p class="lead text-muted"><?php echo $success; ?></p>
                    <p class="text-muted">We will contact you soon with further details.</p>
                    <div class="mt-4">
                        <a href="dashboard.php" class="btn btn-primary me-2">View Dashboard</a>
                        <a href="bikes.php" class="btn btn-outline-primary">Continue Shopping</a>
                    </div>
                </div>
                <?php elseif ($product): ?>
                <div class="card p-4">
                    <h2 class="mb-4">Pre-Order Request</h2>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <?php 
                            $images = json_decode($product['images'], true);
                            $image = !empty($images) ? $images[0] : 'https://via.placeholder.com/200x150?text=EV+Bike';
                            ?>
                            <img src="<?php echo htmlspecialchars($image); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="col-md-8">
                            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                            <p class="text-muted"><?php echo htmlspecialchars($product['brand']); ?></p>
                            <p class="price"><?php echo formatPrice($product['price']); ?></p>
                        </div>
                    </div>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <?php 
                        $colors = json_decode($product['colors'], true) ?: [];
                        if (!empty($colors)): 
                        ?>
                        <div class="mb-3">
                            <label class="form-label">Select Color</label>
                            <select name="color" class="form-control">
                                <option value="">Choose a color</option>
                                <?php foreach ($colors as $color): ?>
                                <option value="<?php echo htmlspecialchars($color); ?>"><?php echo htmlspecialchars($color); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Additional Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Any specific requirements or questions..."></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            By placing a pre-order, you express your interest in purchasing this vehicle. Our team will contact you to confirm availability and payment details.
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-shopping-cart me-2"></i>Confirm Pre-Order
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="card p-5 text-center">
                    <i class="fas fa-exclamation-circle mb-3" style="font-size: 4rem; color: #ff4444;"></i>
                    <h3>Product Not Found</h3>
                    <p class="text-muted">The product you're looking for is not available.</p>
                    <a href="bikes.php" class="btn btn-primary">Browse EV Bikes</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'include/footer.php'; ?>
