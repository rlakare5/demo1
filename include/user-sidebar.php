<?php
$user = [];
if ($con && isLoggedIn()) {
    $result = mysqli_query($con, "SELECT * FROM users WHERE id = " . $_SESSION['user_id']);
    if ($result) {
        $user = mysqli_fetch_assoc($result);
    }
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="card p-4">
    <div class="text-center mb-4">
        <i class="fas fa-user-circle" style="font-size: 5rem; color: var(--primary);"></i>
        <h4 class="mt-3"><?php echo htmlspecialchars($user['full_name'] ?? $_SESSION['user_name'] ?? 'User'); ?></h4>
        <p class="text-muted small"><?php echo htmlspecialchars($user['email'] ?? $_SESSION['user_email'] ?? ''); ?></p>
    </div>
    
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-home me-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'my-orders.php' ? 'active' : ''; ?>" href="my-orders.php">
                <i class="fas fa-shopping-bag me-2"></i>My Orders
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'my-preorders.php' ? 'active' : ''; ?>" href="my-preorders.php">
                <i class="fas fa-clock me-2"></i>Pre-Orders
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'my-wishlist.php' ? 'active' : ''; ?>" href="my-wishlist.php">
                <i class="fas fa-heart me-2"></i>Wishlist
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                <i class="fas fa-user-cog me-2"></i>Profile
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </li>
    </ul>
</div>

<style>
.nav-link { color: #aaa; padding: 10px 15px; border-radius: 8px; margin-bottom: 5px; }
.nav-link:hover, .nav-link.active { color: var(--primary); background: rgba(0,255,136,0.1); }
</style>
