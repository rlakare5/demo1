<?php 
include 'include/config.php';
$pageTitle = 'Register - Vportal';

$error = '';
$success = '';

$admins = [];
if ($con) {
    $result = mysqli_query($con, "SELECT id, showroom_name FROM admin WHERE is_active = 1 ORDER BY showroom_name ASC");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $admins[] = $row;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $admin_id = intval($_POST['admin_id'] ?? 0);
    
    if (empty($full_name) || empty($email) || empty($password) || empty($admin_id)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $check = mysqli_query($con, "SELECT id FROM users WHERE email = '$email' AND admin_id = $admin_id");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Email already registered for this showroom.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $username = strtolower(str_replace(' ', '', $full_name)) . rand(100, 999);
            
            $query = "INSERT INTO users (admin_id, username, email, password, full_name, phone) 
                      VALUES ($admin_id, '$username', '$email', '$hashed_password', '$full_name', '$phone')";
            
            if (mysqli_query($con, $query)) {
                $user_id = mysqli_insert_id($con);
                logActivity('user', $user_id, 'register', 'New user registration');
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

include 'include/header.php';
?>

<div style="padding-top: 120px;"></div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus" style="font-size: 4rem; color: var(--primary);"></i>
                        <h2 class="mt-3">Create Account</h2>
                        <p class="text-muted">Join the EV revolution</p>
                    </div>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php else: ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="Enter your phone number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Select Showroom *</label>
                            <select name="admin_id" class="form-control" required>
                                <option value="">Choose a showroom</option>
                                <?php foreach ($admins as $admin): ?>
                                <option value="<?php echo $admin['id']; ?>" <?php echo (isset($_POST['admin_id']) && $_POST['admin_id'] == $admin['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($admin['showroom_name']); ?>
                                </option>
                                <?php endforeach; ?>
                                <?php if (empty($admins)): ?>
                                <option value="1">Default Showroom</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-control" placeholder="Create password" required minlength="6">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label text-muted" for="terms">
                                    I agree to the <a href="#" style="color: var(--primary);">Terms of Service</a> and <a href="#" style="color: var(--primary);">Privacy Policy</a>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Create Account</button>
                        
                        <p class="text-center text-muted mb-0">
                            Already have an account? <a href="login.php" class="text-decoration-none" style="color: var(--primary);">Login</a>
                        </p>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'include/footer.php'; ?>
