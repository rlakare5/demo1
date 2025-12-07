<?php
session_start();

$db_host = "localhost";
$db_name = "ev_showroom";
$db_user = "root";
$db_pass = "";

$con = null;
$db_connected = false;

try {
    @$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if ($con) {
        mysqli_set_charset($con, "utf8mb4");
        $db_connected = true;
    }
} catch (Exception $e) {
    $con = null;
    $db_connected = false;
}

define('SITE_URL', 'http://localhost/ev-showroom');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('DB_CONNECTED', $db_connected);

function sanitize($data) {
    global $con;
    if ($con) {
        return mysqli_real_escape_string($con, htmlspecialchars(trim($data)));
    }
    return htmlspecialchars(trim($data));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function isSuperAdmin() {
    return isset($_SESSION['super_admin_id']) && !empty($_SESSION['super_admin_id']);
}

function getSetting($key) {
    global $con;
    if (!$con) return null;
    $key = sanitize($key);
    $result = mysqli_query($con, "SELECT setting_value FROM global_settings WHERE setting_key = '$key'");
    if ($result && $row = mysqli_fetch_assoc($result)) {
        return $row['setting_value'];
    }
    return null;
}

function formatPrice($amount) {
    $symbol = getSetting('currency_symbol') ?: '₹';
    return $symbol . number_format($amount, 2);
}

function generateOrderNumber() {
    $prefix = getSetting('order_prefix') ?: 'ORD';
    return $prefix . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

function generateInvoiceNumber() {
    $prefix = getSetting('invoice_prefix') ?: 'INV';
    return $prefix . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

function logActivity($user_type, $user_id, $action, $description = '') {
    global $con;
    if (!$con) return;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $stmt = mysqli_prepare($con, "INSERT INTO system_logs (user_type, user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sissss", $user_type, $user_id, $action, $description, $ip, $user_agent);
        mysqli_stmt_execute($stmt);
    }
}

function showSetupGuide() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Setup Required - Vportal</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            :root { --primary: #00ff88; --secondary: #1a1a2e; --accent: #16213e; }
            body { background: var(--secondary); color: #fff; font-family: 'Poppins', sans-serif; min-height: 100vh; }
            .card { background: var(--accent); border: 1px solid rgba(0,255,136,0.2); border-radius: 15px; }
            code { background: rgba(0,0,0,0.3); padding: 2px 8px; border-radius: 5px; color: var(--primary); }
            pre { background: rgba(0,0,0,0.3); padding: 15px; border-radius: 10px; overflow-x: auto; }
            .step { background: rgba(0,255,136,0.1); border-left: 4px solid var(--primary); padding: 15px; margin-bottom: 15px; border-radius: 0 10px 10px 0; }
            .step-number { background: var(--primary); color: var(--secondary); width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 10px; }
        </style>
    </head>
    <body>
        <div class="container py-5">
            <div class="text-center mb-5">
                <i class="fas fa-bolt" style="font-size: 5rem; color: var(--primary);"></i>
                <h1 class="mt-3">Vportal - EV Showroom Platform</h1>
                <p class="lead text-muted">Setup Required for XAMPP</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card p-4 mb-4">
                        <h3 class="mb-4"><i class="fas fa-info-circle me-2" style="color: var(--primary);"></i>About This Project</h3>
                        <p>This is a complete EV Showroom Platform with:</p>
                        <ul>
                            <li>E-commerce website for electric vehicles</li>
                            <li>Multi-level admin system (Super Admin, Admin, User)</li>
                            <li>Billing and invoice system with GST</li>
                            <li>Offer and notification system</li>
                            <li>Pre-order management</li>
                        </ul>
                        <p class="mb-0"><strong>Tech Stack:</strong> PHP 8.x + MySQL</p>
                    </div>
                    
                    <div class="card p-4 mb-4">
                        <h3 class="mb-4"><i class="fas fa-server me-2" style="color: var(--primary);"></i>Setup Instructions (XAMPP)</h3>
                        
                        <div class="step">
                            <span class="step-number">1</span>
                            <strong>Install XAMPP</strong>
                            <p class="mb-0 mt-2 text-muted">Download from <a href="https://www.apachefriends.org/" target="_blank" style="color: var(--primary);">apachefriends.org</a></p>
                        </div>
                        
                        <div class="step">
                            <span class="step-number">2</span>
                            <strong>Copy Project Files</strong>
                            <p class="mb-0 mt-2 text-muted">Copy this folder to <code>C:\xampp\htdocs\ev-showroom\</code></p>
                        </div>
                        
                        <div class="step">
                            <span class="step-number">3</span>
                            <strong>Start XAMPP Services</strong>
                            <p class="mb-0 mt-2 text-muted">Open XAMPP Control Panel and start <strong>Apache</strong> and <strong>MySQL</strong></p>
                        </div>
                        
                        <div class="step">
                            <span class="step-number">4</span>
                            <strong>Create Database</strong>
                            <p class="mt-2 text-muted">Open <a href="http://localhost/phpmyadmin" target="_blank" style="color: var(--primary);">phpMyAdmin</a>, create database <code>ev_showroom</code>, then import <code>DATABASE.sql</code></p>
                        </div>
                        
                        <div class="step">
                            <span class="step-number">5</span>
                            <strong>Access the Application</strong>
                            <ul class="mb-0 mt-2 text-muted">
                                <li>Website: <code>http://localhost/ev-showroom/</code></li>
                                <li>Admin: <code>http://localhost/ev-showroom/admin/</code></li>
                                <li>Super Admin: <code>http://localhost/ev-showroom/superadmin/</code></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card p-4">
                        <h3 class="mb-4"><i class="fas fa-key me-2" style="color: var(--primary);"></i>Default Credentials</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Super Admin</h5>
                                <p class="text-muted mb-1">Email: <code>superadmin@evshowroom.com</code></p>
                                <p class="text-muted">Password: <code>admin123</code></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>
