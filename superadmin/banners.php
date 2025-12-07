<?php
$pageTitle = 'Banners';
include 'include/header.php';

$success = '';
$error = '';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (mysqli_query($con, "DELETE FROM banners WHERE id = $id")) {
        $success = 'Banner deleted successfully!';
    } else {
        $error = 'Failed to delete banner.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    $title = sanitize($_POST['title'] ?? '');
    $subtitle = sanitize($_POST['subtitle'] ?? '');
    $image_url = sanitize($_POST['image_url'] ?? '');
    $link_url = sanitize($_POST['link_url'] ?? '');
    $position = intval($_POST['position'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if ($action === 'add') {
        $query = "INSERT INTO banners (title, subtitle, image_url, link_url, position, is_active) 
                  VALUES ('$title', '$subtitle', '$image_url', '$link_url', $position, $is_active)";
        if (mysqli_query($con, $query)) {
            $success = 'Banner added successfully!';
        } else {
            $error = 'Failed to add banner: ' . mysqli_error($con);
        }
    }
    
    if ($action === 'edit' && $id > 0) {
        $query = "UPDATE banners SET title = '$title', subtitle = '$subtitle', image_url = '$image_url', 
                  link_url = '$link_url', position = $position, is_active = $is_active WHERE id = $id";
        if (mysqli_query($con, $query)) {
            $success = 'Banner updated successfully!';
        } else {
            $error = 'Failed to update banner.';
        }
    }
}

$banners = [];
$result = mysqli_query($con, "SELECT * FROM banners ORDER BY position, id");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $banners[] = $row;
    }
}

$edit_banner = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = mysqli_query($con, "SELECT * FROM banners WHERE id = $edit_id");
    if ($result) {
        $edit_banner = mysqli_fetch_assoc($result);
    }
}

$show_form = isset($_GET['action']) && $_GET['action'] == 'add' || $edit_banner;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-images me-2" style="color: var(--primary);"></i>Banners</h2>
    <?php if (!$show_form): ?>
    <a href="banners.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Banner</a>
    <?php else: ?>
    <a href="banners.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
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
    <h4 class="mb-4"><?php echo $edit_banner ? 'Edit Banner' : 'Add New Banner'; ?></h4>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $edit_banner ? 'edit' : 'add'; ?>">
        <?php if ($edit_banner): ?>
        <input type="hidden" name="id" value="<?php echo $edit_banner['id']; ?>">
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($edit_banner['title'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Subtitle</label>
                <input type="text" name="subtitle" class="form-control" value="<?php echo htmlspecialchars($edit_banner['subtitle'] ?? ''); ?>">
            </div>
            <div class="col-md-8 mb-3">
                <label class="form-label">Image URL *</label>
                <input type="url" name="image_url" class="form-control" required placeholder="https://example.com/banner.jpg" value="<?php echo htmlspecialchars($edit_banner['image_url'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Position</label>
                <input type="number" name="position" class="form-control" value="<?php echo $edit_banner['position'] ?? 0; ?>">
            </div>
            <div class="col-md-8 mb-3">
                <label class="form-label">Link URL</label>
                <input type="url" name="link_url" class="form-control" placeholder="https://example.com/page" value="<?php echo htmlspecialchars($edit_banner['link_url'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <div class="form-check mt-4">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (!isset($edit_banner) || $edit_banner['is_active']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i><?php echo $edit_banner ? 'Update Banner' : 'Add Banner'; ?></button>
    </form>
</div>

<?php else: ?>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($banners)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No banners found.</td></tr>
                <?php else: ?>
                <?php foreach ($banners as $b): ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars($b['image_url']); ?>" style="width: 120px; height: 60px; object-fit: cover; border-radius: 5px;">
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($b['title']); ?></strong>
                        <?php if ($b['subtitle']): ?>
                        <br><small class="text-muted"><?php echo htmlspecialchars($b['subtitle']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $b['position']; ?></td>
                    <td>
                        <span class="badge bg-<?php echo $b['is_active'] ? 'success' : 'secondary'; ?>">
                            <?php echo $b['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="banners.php?edit=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete('banners.php?delete=<?php echo $b['id']; ?>', '<?php echo htmlspecialchars($b['title']); ?>')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
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
