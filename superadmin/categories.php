<?php
$pageTitle = 'Categories';
include 'include/header.php';

$success = '';
$error = '';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (mysqli_query($con, "DELETE FROM categories WHERE id = $id")) {
        $success = 'Category deleted successfully!';
    } else {
        $error = 'Failed to delete category.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    $name = sanitize($_POST['name'] ?? '');
    $slug = strtolower(str_replace(' ', '-', $name));
    $description = sanitize($_POST['description'] ?? '');
    $icon = sanitize($_POST['icon'] ?? 'fas fa-motorcycle');
    $image = sanitize($_POST['image'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if ($action === 'add') {
        $query = "INSERT INTO categories (name, slug, description, icon, image, is_active) 
                  VALUES ('$name', '$slug', '$description', '$icon', '$image', $is_active)";
        if (mysqli_query($con, $query)) {
            $success = 'Category added successfully!';
        } else {
            $error = 'Failed to add category: ' . mysqli_error($con);
        }
    }
    
    if ($action === 'edit' && $id > 0) {
        $query = "UPDATE categories SET name = '$name', slug = '$slug', description = '$description', 
                  icon = '$icon', image = '$image', is_active = $is_active WHERE id = $id";
        if (mysqli_query($con, $query)) {
            $success = 'Category updated successfully!';
        } else {
            $error = 'Failed to update category.';
        }
    }
}

$categories = [];
$result = mysqli_query($con, "SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count FROM categories c ORDER BY c.name");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
}

$edit_category = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = mysqli_query($con, "SELECT * FROM categories WHERE id = $edit_id");
    if ($result) {
        $edit_category = mysqli_fetch_assoc($result);
    }
}

$show_form = isset($_GET['action']) && $_GET['action'] == 'add' || $edit_category;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-list me-2" style="color: var(--primary);"></i>Categories</h2>
    <?php if (!$show_form): ?>
    <a href="categories.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Category</a>
    <?php else: ?>
    <a href="categories.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
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
    <h4 class="mb-4"><?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?></h4>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $edit_category ? 'edit' : 'add'; ?>">
        <?php if ($edit_category): ?>
        <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Category Name *</label>
                <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($edit_category['name'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Icon Class</label>
                <input type="text" name="icon" class="form-control" placeholder="fas fa-motorcycle" value="<?php echo htmlspecialchars($edit_category['icon'] ?? 'fas fa-motorcycle'); ?>">
                <small class="text-muted">FontAwesome icon class</small>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($edit_category['description'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Image URL</label>
                <input type="url" name="image" class="form-control" placeholder="https://example.com/image.jpg" value="<?php echo htmlspecialchars($edit_category['image'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check mt-4">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (!isset($edit_category) || $edit_category['is_active']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i><?php echo $edit_category ? 'Update Category' : 'Add Category'; ?></button>
    </form>
</div>

<?php else: ?>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No categories found.</td></tr>
                <?php else: ?>
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td><i class="<?php echo htmlspecialchars($c['icon'] ?? 'fas fa-motorcycle'); ?> fa-lg" style="color: var(--primary);"></i></td>
                    <td><strong><?php echo htmlspecialchars($c['name']); ?></strong></td>
                    <td><?php echo $c['product_count']; ?> products</td>
                    <td>
                        <span class="badge bg-<?php echo $c['is_active'] ? 'success' : 'secondary'; ?>">
                            <?php echo $c['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="categories.php?edit=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete('categories.php?delete=<?php echo $c['id']; ?>', '<?php echo htmlspecialchars($c['name']); ?>')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
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
