<?php
$pageTitle = 'Settings';
include 'include/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'profile') {
        $full_name = sanitize($_POST['full_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        
        $query = "UPDATE super_admin SET full_name = '$full_name', email = '$email' WHERE id = " . $_SESSION['super_admin_id'];
        if (mysqli_query($con, $query)) {
            $success = 'Profile updated successfully!';
            $result = mysqli_query($con, "SELECT * FROM super_admin WHERE id = " . $_SESSION['super_admin_id']);
            if ($result) {
                $super_admin = mysqli_fetch_assoc($result);
            }
        } else {
            $error = 'Failed to update profile.';
        }
    }
    
    if ($action === 'password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if ($new_password !== $confirm_password) {
            $error = 'New passwords do not match.';
        } else {
            $result = mysqli_query($con, "SELECT password FROM super_admin WHERE id = " . $_SESSION['super_admin_id']);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                if (password_verify($current_password, $row['password'])) {
                    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    mysqli_query($con, "UPDATE super_admin SET password = '$hashed' WHERE id = " . $_SESSION['super_admin_id']);
                    $success = 'Password changed successfully!';
                } else {
                    $error = 'Current password is incorrect.';
                }
            }
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-cog me-2" style="color: var(--primary);"></i>Settings</h2>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card p-4 mb-4">
            <h5 class="mb-4"><i class="fas fa-user me-2" style="color: var(--primary);"></i>Profile Settings</h5>
            <form method="POST">
                <input type="hidden" name="action" value="profile">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($super_admin['full_name'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($super_admin['email'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Profile</button>
            </form>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card p-4 mb-4">
            <h5 class="mb-4"><i class="fas fa-lock me-2" style="color: var(--primary);"></i>Change Password</h5>
            <form method="POST">
                <input type="hidden" name="action" value="password">
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-warning"><i class="fas fa-key me-2"></i>Change Password</button>
            </form>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>
