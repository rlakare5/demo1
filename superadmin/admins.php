<?php
$pageTitle = 'Manage Admins';
include 'include/header.php';

$success = '';
$error = '';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (mysqli_query($con, "DELETE FROM admin WHERE id = $id")) {
        $success = 'Admin deleted successfully!';
    } else {
        $error = 'Failed to delete admin.';
    }
}

if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $result = mysqli_query($con, "SELECT is_active FROM admin WHERE id = $id");
    if ($result) {
        $admin = mysqli_fetch_assoc($result);
        $new_status = $admin['is_active'] ? 0 : 1;
        mysqli_query($con, "UPDATE admin SET is_active = $new_status WHERE id = $id");
        $success = 'Admin status updated!';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    $showroom_name = sanitize($_POST['showroom_name'] ?? '');
    $owner_name = sanitize($_POST['owner_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if ($action === 'add') {
        $password = password_hash($_POST['password'] ?? 'admin123', PASSWORD_DEFAULT);
        $query = "INSERT INTO admin (showroom_name, owner_name, email, password, phone, address, is_active) 
                  VALUES ('$showroom_name', '$owner_name', '$email', '$password', '$phone', '$address', $is_active)";
        if (mysqli_query($con, $query)) {
            $success = 'Admin added successfully!';
        } else {
            $error = 'Failed to add admin: ' . mysqli_error($con);
        }
    }
    
    if ($action === 'edit' && $id > 0) {
        $password_update = '';
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $password_update = ", password = '$password'";
        }
        $query = "UPDATE admin SET showroom_name = '$showroom_name', owner_name = '$owner_name', 
                  email = '$email', phone = '$phone', address = '$address', is_active = $is_active $password_update 
                  WHERE id = $id";
        if (mysqli_query($con, $query)) {
            $success = 'Admin updated successfully!';
        } else {
            $error = 'Failed to update admin.';
        }
    }
}

$admins = [];
$result = mysqli_query($con, "SELECT * FROM admin ORDER BY created_at DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $admins[] = $row;
    }
}

$edit_admin = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = mysqli_query($con, "SELECT * FROM admin WHERE id = $edit_id");
    if ($result) {
        $edit_admin = mysqli_fetch_assoc($result);
    }
}

$show_form = isset($_GET['action']) && $_GET['action'] == 'add' || $edit_admin;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-tie me-2" style="color: var(--primary);"></i>Manage Admins</h2>
    <?php if (!$show_form): ?>
    <a href="admins.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Admin</a>
    <?php else: ?>
    <a href="admins.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
    <?php endif; ?>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?php if ($show_form): ?>
<div class="card p-4">
    <h4 class="mb-4"><?php echo $edit_admin ? 'Edit Admin' : 'Add New Admin'; ?></h4>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $edit_admin ? 'edit' : 'add'; ?>">
        <?php if ($edit_admin): ?>
        <input type="hidden" name="id" value="<?php echo $edit_admin['id']; ?>">
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Showroom Name *</label>
                <input type="text" name="showroom_name" class="form-control" required value="<?php echo htmlspecialchars($edit_admin['showroom_name'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Owner Name *</label>
                <input type="text" name="owner_name" class="form-control" required value="<?php echo htmlspecialchars($edit_admin['owner_name'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($edit_admin['email'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Password <?php echo $edit_admin ? '(leave blank to keep current)' : '*'; ?></label>
                <input type="password" name="password" class="form-control" <?php echo !$edit_admin ? 'required' : ''; ?>>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($edit_admin['phone'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($edit_admin['address'] ?? ''); ?>">
            </div>
            <div class="col-12 mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (!isset($edit_admin) || $edit_admin['is_active']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i><?php echo $edit_admin ? 'Update Admin' : 'Add Admin'; ?></button>
    </form>
</div>

<?php else: ?>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Showroom</th>
                    <th>Owner</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($admins)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No admins found.</td></tr>
                <?php else: ?>
                <?php foreach ($admins as $a): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($a['showroom_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($a['owner_name']); ?></td>
                    <td><?php echo htmlspecialchars($a['email']); ?></td>
                    <td><?php echo htmlspecialchars($a['phone'] ?? '-'); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $a['is_active'] ? 'success' : 'secondary'; ?>">
                            <?php echo $a['is_active'] ? 'Active' : 'Blocked'; ?>
                        </span>
                    </td>
                    <td><?php echo date('d M Y', strtotime($a['created_at'])); ?></td>
                    <td>
                        <a href="admins.php?edit=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <a href="admins.php?toggle=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-warning" title="Toggle Status"><i class="fas fa-ban"></i></a>
                        <button onclick="confirmDelete('admins.php?delete=<?php echo $a['id']; ?>', '<?php echo htmlspecialchars($a['showroom_name']); ?>')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include 'include/footer.php'; ?>
