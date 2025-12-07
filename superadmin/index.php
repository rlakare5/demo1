<?php
session_start();
if (!isset($_SESSION['super_admin_id'])) {
    header('Location: login.php');
    exit;
}

include '../include/config.php';

$super_admin = [];
$stats = [
    'admins' => 0,
    'users' => 0,
    'products' => 0,
    'orders' => 0,
    'revenue' => 0
];

if ($con) {
    $result = mysqli_query($con, "SELECT * FROM super_admin WHERE id = " . $_SESSION['super_admin_id']);
    if ($result) {
        $super_admin = mysqli_fetch_assoc($result);
    }
    
    $admins_result = mysqli_query($con, "SELECT COUNT(*) as count FROM admin");
    if ($admins_result) {
        $stats['admins'] = mysqli_fetch_assoc($admins_result)['count'];
    }
    
    $users_result = mysqli_query($con, "SELECT COUNT(*) as count FROM users");
    if ($users_result) {
        $stats['users'] = mysqli_fetch_assoc($users_result)['count'];
    }
    
    $products_result = mysqli_query($con, "SELECT COUNT(*) as count FROM products");
    if ($products_result) {
        $stats['products'] = mysqli_fetch_assoc($products_result)['count'];
    }
    
    $orders_result = mysqli_query($con, "SELECT COUNT(*) as count FROM orders");
    if ($orders_result) {
        $stats['orders'] = mysqli_fetch_assoc($orders_result)['count'];
    }
    
    $revenue_result = mysqli_query($con, "SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid'");
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
    <title>Super Admin Dashboard - Vportal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary: #00ff88; --secondary: #1a1a2e; --accent: #16213e; }
        body { background: var(--secondary); color: #fff; font-family: 'Poppins', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #16213e 0%, #0f0f1a 100%); min-height: 100vh; padding: 20px 0; }
        .sidebar .nav-link { color: #aaa; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: var(--primary); background: rgba(0,255,136,0.1); }
        .sidebar .nav-link i { width: 25px; }
        .main-content { padding: 30px; }
        .stat-card { background: var(--accent); border: 1px solid rgba(0,255,136,0.1); border-radius: 15px; padding: 25px; }
        .stat-card h3 { color: var(--primary); font-size: 2rem; }
        .card { background: var(--accent); border: 1px solid rgba(0,255,136,0.1); border-radius: 15px; }
        .table { color: #fff; }
        .badge-success { background: var(--primary); color: var(--secondary); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <h4 class="text-center mb-2" style="color: var(--primary);"><i class="fas fa-crown me-2"></i>Vportal</h4>
                <p class="text-center text-muted small mb-4">Super Admin</p>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="admins.php"><i class="fas fa-user-tie me-2"></i>Manage Admins</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php"><i class="fas fa-users me-2"></i>All Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="categories.php"><i class="fas fa-list me-2"></i>Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php"><i class="fas fa-motorcycle me-2"></i>All Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="billing.php"><i class="fas fa-file-invoice me-2"></i>Billing Config</a></li>
                    <li class="nav-item"><a class="nav-link" href="offers.php"><i class="fas fa-tags me-2"></i>Global Offers</a></li>
                    <li class="nav-item"><a class="nav-link" href="banners.php"><i class="fas fa-images me-2"></i>Banners</a></li>
                    <li class="nav-item"><a class="nav-link" href="themes.php"><i class="fas fa-palette me-2"></i>Themes</a></li>
                    <li class="nav-item"><a class="nav-link" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li class="nav-item"><a class="nav-link" href="logs.php"><i class="fas fa-history me-2"></i>System Logs</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
            
            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-crown me-2" style="color: var(--primary);"></i>Super Admin Dashboard</h2>
                    <span class="text-muted">Welcome, <?php echo htmlspecialchars($super_admin['full_name'] ?? 'Super Admin'); ?></span>
                </div>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-2">
                        <div class="stat-card text-center">
                            <i class="fas fa-user-tie mb-2" style="font-size: 1.5rem; color: var(--primary);"></i>
                            <h3><?php echo $stats['admins']; ?></h3>
                            <p class="text-muted mb-0 small">Showrooms</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card text-center">
                            <i class="fas fa-users mb-2" style="font-size: 1.5rem; color: var(--primary);"></i>
                            <h3><?php echo $stats['users']; ?></h3>
                            <p class="text-muted mb-0 small">Users</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card text-center">
                            <i class="fas fa-motorcycle mb-2" style="font-size: 1.5rem; color: var(--primary);"></i>
                            <h3><?php echo $stats['products']; ?></h3>
                            <p class="text-muted mb-0 small">Products</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card text-center">
                            <i class="fas fa-shopping-bag mb-2" style="font-size: 1.5rem; color: var(--primary);"></i>
                            <h3><?php echo $stats['orders']; ?></h3>
                            <p class="text-muted mb-0 small">Orders</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center">
                            <i class="fas fa-rupee-sign mb-2" style="font-size: 1.5rem; color: var(--primary);"></i>
                            <h3><?php echo formatPrice($stats['revenue']); ?></h3>
                            <p class="text-muted mb-0 small">Total Revenue</p>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card p-4">
                            <h5 class="mb-3"><i class="fas fa-bolt me-2" style="color: var(--primary);"></i>Quick Actions</h5>
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="admins.php?action=add" class="btn btn-outline-success w-100 mb-2"><i class="fas fa-plus me-2"></i>Add Showroom</a>
                                </div>
                                <div class="col-6">
                                    <a href="categories.php?action=add" class="btn btn-outline-info w-100 mb-2"><i class="fas fa-plus me-2"></i>Add Category</a>
                                </div>
                                <div class="col-6">
                                    <a href="offers.php?action=add" class="btn btn-outline-warning w-100"><i class="fas fa-tags me-2"></i>Create Offer</a>
                                </div>
                                <div class="col-6">
                                    <a href="banners.php?action=add" class="btn btn-outline-primary w-100"><i class="fas fa-images me-2"></i>Add Banner</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-4">
                            <h5 class="mb-3"><i class="fas fa-info-circle me-2" style="color: var(--primary);"></i>System Info</h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><strong>Version:</strong> 1.0.0</li>
                                <li class="mb-2"><strong>PHP:</strong> <?php echo phpversion(); ?></li>
                                <li class="mb-2"><strong>Database:</strong> MySQL</li>
                                <li class="mb-0"><strong>Status:</strong> <span class="badge bg-success">Active</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
