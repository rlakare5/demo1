<?php
session_start();
include '../include/config.php';
include 'include/header.php';

$pageTitle = 'Settings';
$admin_id = $_SESSION['admin_id'];
$success = '';
$error = '';

$admin = [];
$result = mysqli_query($con, "SELECT * FROM admin WHERE id = $admin_id");
if ($result) {
    $admin = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $showroom_name = sanitize($_POST['showroom_name'] ?? '');
        $full_name = sanitize($_POST['full_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $city = sanitize($_POST['city'] ?? '');
        $state = sanitize($_POST['state'] ?? '');
        $pincode = sanitize($_POST['pincode'] ?? '');
        $gst_number = sanitize($_POST['gst_number'] ?? '');
        
        $query = "UPDATE admin SET showroom_name = '$showroom_name', full_name = '$full_name', phone = '$phone', 
                  address = '$address', city = '$city', state = '$state', pincode = '$pincode', gst_number = '$gst_number' 
                  WHERE id = $admin_id";
        
        if (mysqli_query($con, $query)) {
            $_SESSION['showroom_name'] = $showroom_name;
            $success = 'Profile updated successfully!';
            $result = mysqli_query($con, "SELECT * FROM admin WHERE id = $admin_id");
            $admin = mysqli_fetch_assoc($result);
        } else {
            $error = 'Failed to update profile.';
        }
    }
    
    if ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($current_password) || empty($new_password)) {
            $error = 'Please fill in all password fields.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match.';
        } elseif (strlen($new_password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif (!password_verify($current_password, $admin['password'])) {
            $error = 'Current password is incorrect.';
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            if (mysqli_query($con, "UPDATE admin SET password = '$hashed' WHERE id = $admin_id")) {
                $success = 'Password changed successfully!';
            } else {
                $error = 'Failed to change password.';
            }
        }
    }
}
?>

<h2 class="mb-4"><i class="fas fa-cog me-2" style="color: var(--primary);"></i>Settings</h2>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card p-4 mb-4">
            <h4 class="mb-4"><i class="fas fa-store me-2" style="color: var(--primary);"></i>Showroom Information</h4>
            <form method="POST">
                <input type="hidden" name="action" value="update_profile">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Showroom Name *</label>
                        <input type="text" name="showroom_name" class="form-control" required value="<?php echo htmlspecialchars($admin['showroom_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Owner Name *</label>
                        <input type="text" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($admin['full_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email (cannot be changed)</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">GST Number</label>
                        <input type="text" name="gst_number" class="form-control" value="<?php echo htmlspecialchars($admin['gst_number'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" value="<?php echo htmlspecialchars($admin['pincode'] ?? ''); ?>">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($admin['address'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($admin['city'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($admin['state'] ?? ''); ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
        
        <div class="card p-4">
            <h4 class="mb-4"><i class="fas fa-lock me-2" style="color: var(--primary);"></i>Change Password</h4>
            <form method="POST">
                <input type="hidden" name="action" value="change_password">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning">Change Password</button>
            </form>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card p-4">
            <h5 class="mb-3"><i class="fas fa-info-circle me-2" style="color: var(--primary);"></i>Account Info</h5>
            <ul class="list-unstyled mb-0">
                <li class="mb-2"><strong>Username:</strong> <?php echo htmlspecialchars($admin['username'] ?? ''); ?></li>
                <li class="mb-2"><strong>Status:</strong> <span class="badge bg-success">Active</span></li>
                <li class="mb-2"><strong>Joined:</strong> <?php echo isset($admin['created_at']) ? date('M d, Y', strtotime($admin['created_at'])) : 'N/A'; ?></li>
                <li class="mb-0"><strong>Last Login:</strong> <?php echo isset($admin['last_login']) ? date('M d, Y H:i', strtotime($admin['last_login'])) : 'N/A'; ?></li>
            </ul>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>
