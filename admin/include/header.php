<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Panel'; ?> - Vportal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary: #00ff88; --secondary: #1a1a2e; --accent: #16213e; }
        body { background: var(--secondary); color: #fff; font-family: 'Poppins', sans-serif; }
        .sidebar { background: var(--accent); min-height: 100vh; padding: 20px 0; position: fixed; width: 220px; }
        .sidebar .nav-link { color: #aaa; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: var(--primary); background: rgba(0,255,136,0.1); }
        .sidebar .nav-link i { width: 25px; }
        .main-content { margin-left: 220px; padding: 30px; min-height: 100vh; }
        .card { background: var(--accent); border: 1px solid rgba(0,255,136,0.1); border-radius: 15px; }
        .table { color: #fff; }
        .table thead th { border-color: rgba(0,255,136,0.1); color: var(--primary); }
        .table td, .table th { border-color: rgba(0,255,136,0.1); vertical-align: middle; }
        .form-control, .form-select { background: rgba(255,255,255,0.1); border: 1px solid rgba(0,255,136,0.2); color: #fff; }
        .form-control:focus, .form-select:focus { background: rgba(255,255,255,0.15); border-color: var(--primary); color: #fff; box-shadow: none; }
        .form-control::placeholder { color: rgba(255,255,255,0.5); }
        .btn-primary { background: linear-gradient(135deg, #00ff88 0%, #00d4ff 100%); border: none; color: var(--secondary); font-weight: 600; }
        .btn-primary:hover { background: linear-gradient(135deg, #00d4ff 0%, #00ff88 100%); color: var(--secondary); }
        .stat-card { background: var(--accent); border: 1px solid rgba(0,255,136,0.1); border-radius: 15px; padding: 20px; }
        .stat-card h3 { color: var(--primary); }
        .badge-success { background: var(--primary) !important; color: var(--secondary) !important; }
        .form-select option { background: var(--accent); color: #fff; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="text-center mb-4" style="color: var(--primary);"><i class="fas fa-bolt me-2"></i>Vportal</h4>
        <p class="text-center text-muted small"><?php echo htmlspecialchars($_SESSION['showroom_name'] ?? 'Admin'); ?></p>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $current_page == 'products.php' ? 'active' : ''; ?>" href="products.php"><i class="fas fa-motorcycle me-2"></i>Products</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>" href="orders.php"><i class="fas fa-shopping-bag me-2"></i>Orders</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $current_page == 'preorders.php' ? 'active' : ''; ?>" href="preorders.php"><i class="fas fa-clock me-2"></i>Pre-Orders</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $current_page == 'customers.php' ? 'active' : ''; ?>" href="customers.php"><i class="fas fa-users me-2"></i>Customers</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $current_page == 'invoices.php' ? 'active' : ''; ?>" href="invoices.php"><i class="fas fa-file-invoice me-2"></i>Invoices</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $current_page == 'offers.php' ? 'active' : ''; ?>" href="offers.php"><i class="fas fa-tags me-2"></i>Offers</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $current_page == 'inventory.php' ? 'active' : ''; ?>" href="inventory.php"><i class="fas fa-warehouse me-2"></i>Inventory</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
