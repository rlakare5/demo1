<?php
session_start();
include '../include/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $query = "SELECT * FROM admin WHERE email = '$email' AND is_active = 1";
        $result = mysqli_query($con, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $admin = mysqli_fetch_assoc($result);
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['showroom_name'] = $admin['showroom_name'];
                
                mysqli_query($con, "UPDATE admin SET last_login = NOW() WHERE id = " . $admin['id']);
                logActivity('admin', $admin['id'], 'login', 'Admin logged in');
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Vportal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary: #00ff88; --secondary: #1a1a2e; --accent: #16213e; }
        body { background: var(--secondary); color: #fff; font-family: 'Poppins', sans-serif; min-height: 100vh; display: flex; align-items: center; }
        .card { background: var(--accent); border: 1px solid rgba(0,255,136,0.1); border-radius: 15px; }
        .form-control { background: rgba(255,255,255,0.1); border: 1px solid rgba(0,255,136,0.2); color: #fff; }
        .form-control:focus { background: rgba(255,255,255,0.15); border-color: var(--primary); color: #fff; }
        .btn-primary { background: linear-gradient(135deg, #00ff88 0%, #00d4ff 100%); border: none; color: var(--secondary); }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-shield" style="font-size: 4rem; color: var(--primary);"></i>
                        <h2 class="mt-3">Admin Login</h2>
                        <p class="text-muted">Showroom Management</p>
                    </div>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    
                    <p class="text-center mt-3 text-muted small">
                        <a href="../index.php" class="text-muted">Back to Website</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
