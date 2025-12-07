<?php
session_start();
if (!isset($_SESSION['super_admin_id'])) {
    header('Location: login.php');
    exit;
}

include '../include/config.php';

$super_admin = [];
if ($con) {
    $result = mysqli_query($con, "SELECT * FROM super_admin WHERE id = " . $_SESSION['super_admin_id']);
    if ($result) {
        $super_admin = mysqli_fetch_assoc($result);
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Super Admin'; ?> - Vportal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary: #00ff88; --secondary: #1a1a2e; --accent: #16213e; }
        body { background: var(--secondary); color: #fff; font-family: 'Poppins', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #16213e 0%, #0f0f1a 100%); min-height: 100vh; padding: 20px 0; position: fixed; width: 16.666667%; }
        .sidebar .nav-link { color: #aaa; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: var(--primary); background: rgba(0,255,136,0.1); }
        .sidebar .nav-link i { width: 25px; }
        .main-content { padding: 30px; margin-left: 16.666667%; }
        .stat-card { background: var(--accent); border: 1px solid rgba(0,255,136,0.1); border-radius: 15px; padding: 25px; }
        .stat-card h3 { color: var(--primary); font-size: 2rem; }
        .card { background: var(--accent); border: 1px solid rgba(0,255,136,0.1); border-radius: 15px; }
        .table { color: #fff; }
        .table th { border-color: rgba(255,255,255,0.1); color: var(--primary); }
        .table td { border-color: rgba(255,255,255,0.1); vertical-align: middle; }
        .btn-primary { background: var(--primary); border-color: var(--primary); color: var(--secondary); font-weight: 600; }
        .btn-primary:hover { background: #00cc6a; border-color: #00cc6a; color: var(--secondary); }
        .form-control, .form-select { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; }
        .form-control:focus, .form-select:focus { background: rgba(255,255,255,0.1); border-color: var(--primary); color: #fff; box-shadow: none; }
        .form-control::placeholder { color: #666; }
        .form-select option { background: var(--secondary); color: #fff; }
        .alert { border-radius: 10px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <h4 class="text-center mb-2" style="color: var(--primary);"><i class="fas fa-crown me-2"></i>Vportal</h4>
                <p class="text-center text-muted small mb-4">Super Admin</p>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'admins.php' ? 'active' : ''; ?>" href="admins.php"><i class="fas fa-user-tie me-2"></i>Manage Admins</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>" href="users.php"><i class="fas fa-users me-2"></i>All Users</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'categories.php' ? 'active' : ''; ?>" href="categories.php"><i class="fas fa-list me-2"></i>Categories</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'products.php' ? 'active' : ''; ?>" href="products.php"><i class="fas fa-motorcycle me-2"></i>All Products</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'billing.php' ? 'active' : ''; ?>" href="billing.php"><i class="fas fa-file-invoice me-2"></i>Billing Config</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'offers.php' ? 'active' : ''; ?>" href="offers.php"><i class="fas fa-tags me-2"></i>Global Offers</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'banners.php' ? 'active' : ''; ?>" href="banners.php"><i class="fas fa-images me-2"></i>Banners</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'themes.php' ? 'active' : ''; ?>" href="themes.php"><i class="fas fa-palette me-2"></i>Themes</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'logs.php' ? 'active' : ''; ?>" href="logs.php"><i class="fas fa-history me-2"></i>System Logs</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
            
            <div class="col-md-10 main-content">
