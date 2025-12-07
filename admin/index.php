<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

include '../include/config.php';

$admin_id = $_SESSION['admin_id'];
$admin = [];
$stats = [
    'products' => 0,
    'orders' => 0,
    'users' => 0,
    'revenue' => 0
];

if ($con) {
    $result = mysqli_query($con, "SELECT * FROM admin WHERE id = $admin_id");
    if ($result) {
        $admin = mysqli_fetch_assoc($result);
    }
    
    $products_result = mysqli_query($con, "SELECT COUNT(*) as count FROM products WHERE admin_id = $admin_id");
    if ($products_result) {
        $stats['products'] = mysqli_fetch_assoc($products_result)['count'];
    }
    
    $orders_result = mysqli_query($con, "SELECT COUNT(*) as count FROM orders WHERE admin_id = $admin_id");
    if ($orders_result) {
        $stats['orders'] = mysqli_fetch_assoc($orders_result)['count'];
    }
    
    $users_result = mysqli_query($con, "SELECT COUNT(*) as count FROM users WHERE admin_id = $admin_id");
    if ($users_result) {
        $stats['users'] = mysqli_fetch_assoc($users_result)['count'];
    }
    
    $revenue_result = mysqli_query($con, "SELECT SUM(total_amount) as total FROM orders WHERE admin_id = $admin_id AND payment_status = 'paid'");
    if ($revenue_result) {
        $row = mysqli_fetch_assoc($revenue_result);
        $stats['revenue'] = $row['total'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Vportal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #00ff88;
            --secondary: #1a1a2e;
            --accent: #16213e;
        }
        body { background: var(--secondary); color: #fff; font-family: 'Poppins', sans-serif; }
        .sidebar { background: var(--accent); min-height: 100vh; padding: 20px 0; }
        .sidebar .nav-link { color: #aaa; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: var(--primary); background: rgba(0,255,136,0.1); }
        .sidebar .nav-link i { width: 25px; }
        .main-content { padding: 30px; }
        .stat-card { background: var(--accent); border: 1px solid rgba(0,255,136,0.1); border-radius: 15px; padding: 25px; }
        .stat-card h3 { color: var(--primary); font-size: 2rem; }
        .card { background: var(--accent); border: 1px solid rgba(0,255,136,0.1); border-radius: 15px; }
        .table { color: #fff; }
        .table thead th { border-color: rgba(0,255,136,0.1); }
        .table td, .table th { border-color: rgba(0,255,136,0.1); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <h4 class="text-center mb-4" style="color: var(--primary);"><i class="fas fa-bolt me-2"></i>Vportal</h4>
                <p class="text-center text-muted small"><?php echo htmlspecialchars($admin['showroom_name'] ?? 'Admin Panel'); ?></p>
                <ul class="nav flex-column mt-4">
                    <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php"><i class="fas fa-motorcycle me-2"></i>Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="orders.php"><i class="fas fa-shopping-bag me-2"></i>Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="preorders.php"><i class="fas fa-clock me-2"></i>Pre-Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="customers.php"><i class="fas fa-users me-2"></i>Customers</a></li>
                    <li class="nav-item"><a class="nav-link" href="invoices.php"><i class="fas fa-file-invoice me-2"></i>Invoices</a></li>
                    <li class="nav-item"><a class="nav-link" href="offers.php"><i class="fas fa-tags me-2"></i>Offers</a></li>
                    <li class="nav-item"><a class="nav-link" href="inventory.php"><i class="fas fa-warehouse me-2"></i>Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
            
            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
                    <span class="text-muted">Welcome, <?php echo htmlspecialchars($admin['full_name'] ?? 'Admin'); ?></span>
                </div>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Total Products</p>
                                    <h3><?php echo $stats['products']; ?></h3>
                                </div>
                                <i class="fas fa-motorcycle fa-2x" style="color: var(--primary); opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Total Orders</p>
                                    <h3><?php echo $stats['orders']; ?></h3>
                                </div>
                                <i class="fas fa-shopping-bag fa-2x" style="color: var(--primary); opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Customers</p>
                                    <h3><?php echo $stats['users']; ?></h3>
                                </div>
                                <i class="fas fa-users fa-2x" style="color: var(--primary); opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Revenue</p>
                                    <h3><?php echo formatPrice($stats['revenue']); ?></h3>
                                </div>
                                <i class="fas fa-rupee-sign fa-2x" style="color: var(--primary); opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card p-4">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="products.php?action=add" class="btn btn-outline-success w-100"><i class="fas fa-plus me-2"></i>Add Product</a>
                        </div>
                        <div class="col-md-3">
                            <a href="preorders.php" class="btn btn-outline-warning w-100"><i class="fas fa-clock me-2"></i>View Pre-Orders</a>
                        </div>
                        <div class="col-md-3">
                            <a href="invoices.php?action=create" class="btn btn-outline-info w-100"><i class="fas fa-file-invoice me-2"></i>Create Invoice</a>
                        </div>
                        <div class="col-md-3">
                            <a href="offers.php?action=add" class="btn btn-outline-primary w-100"><i class="fas fa-tags me-2"></i>New Offer</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
