<?php 
include 'include/config.php';
$pageTitle = 'Login - Vportal';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $query = "SELECT * FROM users WHERE email = '$email' AND is_active = 1 AND is_blocked = 0";
        $result = mysqli_query($con, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['admin_id'] = $user['admin_id'];
                
                mysqli_query($con, "UPDATE users SET last_login = NOW() WHERE id = " . $user['id']);
                logActivity('user', $user['id'], 'login', 'User logged in');
                
                redirect('dashboard.php');
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

include 'include/header.php';
?>

<div style="padding-top: 120px;"></div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle" style="font-size: 4rem; color: var(--primary);"></i>
                        <h2 class="mt-3">Welcome Back</h2>
                        <p class="text-muted">Login to your account</p>
                    </div>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                        
                        <div class="mb-3 d-flex justify-content-between">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label text-muted" for="remember">Remember me</label>
                            </div>
                            <a href="forgot-password.php" class="text-decoration-none" style="color: var(--primary);">Forgot Password?</a>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                        
                        <p class="text-center text-muted mb-0">
                            Don't have an account? <a href="register.php" class="text-decoration-none" style="color: var(--primary);">Sign Up</a>
                        </p>
                    </form>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted small">
                        <a href="admin/login.php" class="text-muted">Admin Login</a> | 
                        <a href="superadmin/login.php" class="text-muted">Super Admin</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'include/footer.php'; ?>
