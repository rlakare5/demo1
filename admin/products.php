<?php
session_start();
include '../include/config.php';
include 'include/header.php';

$pageTitle = 'Products';
$admin_id = $_SESSION['admin_id'];
$success = '';
$error = '';

$categories = [];
$cat_result = mysqli_query($con, "SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
if ($cat_result) {
    while ($row = mysqli_fetch_assoc($cat_result)) {
        $categories[] = $row;
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (mysqli_query($con, "DELETE FROM products WHERE id = $id AND admin_id = $admin_id")) {
        $success = 'Product deleted successfully!';
    } else {
        $error = 'Failed to delete product.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    $name = sanitize($_POST['name'] ?? '');
    $slug = strtolower(str_replace(' ', '-', $name)) . '-' . rand(100, 999);
    $category_id = intval($_POST['category_id'] ?? 0);
    $brand = sanitize($_POST['brand'] ?? '');
    $model = sanitize($_POST['model'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $sale_price = floatval($_POST['sale_price'] ?? 0);
    $battery_type = sanitize($_POST['battery_type'] ?? '');
    $battery_capacity = sanitize($_POST['battery_capacity'] ?? '');
    $range_km = intval($_POST['range_km'] ?? 0);
    $top_speed = intval($_POST['top_speed'] ?? 0);
    $charging_time = sanitize($_POST['charging_time'] ?? '');
    $motor_power = sanitize($_POST['motor_power'] ?? '');
    $weight = floatval($_POST['weight'] ?? 0);
    $warranty = sanitize($_POST['warranty'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $colors = isset($_POST['colors']) ? json_encode(array_filter(explode(',', $_POST['colors']))) : '[]';
    $images = isset($_POST['images']) ? json_encode(array_filter(explode("\n", trim($_POST['images'])))) : '[]';
    
    if ($action === 'add') {
        $query = "INSERT INTO products (admin_id, category_id, name, slug, brand, model, description, price, sale_price, 
                  battery_type, battery_capacity, range_km, top_speed, charging_time, motor_power, weight, warranty, 
                  colors, images, is_featured, is_active) 
                  VALUES ($admin_id, $category_id, '$name', '$slug', '$brand', '$model', '$description', $price, $sale_price, 
                  '$battery_type', '$battery_capacity', $range_km, $top_speed, '$charging_time', '$motor_power', $weight, '$warranty', 
                  '$colors', '$images', $is_featured, $is_active)";
        if (mysqli_query($con, $query)) {
            $success = 'Product added successfully!';
        } else {
            $error = 'Failed to add product: ' . mysqli_error($con);
        }
    }
    
    if ($action === 'edit' && $id > 0) {
        $query = "UPDATE products SET category_id = $category_id, name = '$name', brand = '$brand', model = '$model', 
                  description = '$description', price = $price, sale_price = $sale_price, battery_type = '$battery_type', 
                  battery_capacity = '$battery_capacity', range_km = $range_km, top_speed = $top_speed, 
                  charging_time = '$charging_time', motor_power = '$motor_power', weight = $weight, warranty = '$warranty', 
                  colors = '$colors', images = '$images', is_featured = $is_featured, is_active = $is_active 
                  WHERE id = $id AND admin_id = $admin_id";
        if (mysqli_query($con, $query)) {
            $success = 'Product updated successfully!';
        } else {
            $error = 'Failed to update product.';
        }
    }
}

$products = [];
$result = mysqli_query($con, "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.admin_id = $admin_id ORDER BY p.created_at DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

$edit_product = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = mysqli_query($con, "SELECT * FROM products WHERE id = $edit_id AND admin_id = $admin_id");
    if ($result) {
        $edit_product = mysqli_fetch_assoc($result);
    }
}

$show_form = isset($_GET['action']) && $_GET['action'] == 'add' || $edit_product;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-motorcycle me-2" style="color: var(--primary);"></i>Products</h2>
    <?php if (!$show_form): ?>
    <a href="products.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Product</a>
    <?php else: ?>
    <a href="products.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
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
    <h4 class="mb-4"><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?></h4>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $edit_product ? 'edit' : 'add'; ?>">
        <?php if ($edit_product): ?>
        <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Product Name *</label>
                <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($edit_product['name'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="0">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo (isset($edit_product['category_id']) && $edit_product['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Brand</label>
                <input type="text" name="brand" class="form-control" value="<?php echo htmlspecialchars($edit_product['brand'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Model</label>
                <input type="text" name="model" class="form-control" value="<?php echo htmlspecialchars($edit_product['model'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Warranty</label>
                <input type="text" name="warranty" class="form-control" placeholder="e.g. 3 Years" value="<?php echo htmlspecialchars($edit_product['warranty'] ?? ''); ?>">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($edit_product['description'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Price *</label>
                <input type="number" step="0.01" name="price" class="form-control" required value="<?php echo $edit_product['price'] ?? ''; ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Sale Price (Original)</label>
                <input type="number" step="0.01" name="sale_price" class="form-control" value="<?php echo $edit_product['sale_price'] ?? ''; ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Range (km)</label>
                <input type="number" name="range_km" class="form-control" value="<?php echo $edit_product['range_km'] ?? ''; ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Top Speed (km/h)</label>
                <input type="number" name="top_speed" class="form-control" value="<?php echo $edit_product['top_speed'] ?? ''; ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Battery Type</label>
                <input type="text" name="battery_type" class="form-control" placeholder="e.g. Lithium-ion" value="<?php echo htmlspecialchars($edit_product['battery_type'] ?? ''); ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Battery Capacity</label>
                <input type="text" name="battery_capacity" class="form-control" placeholder="e.g. 3.6 kWh" value="<?php echo htmlspecialchars($edit_product['battery_capacity'] ?? ''); ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Charging Time</label>
                <input type="text" name="charging_time" class="form-control" placeholder="e.g. 4-5 hours" value="<?php echo htmlspecialchars($edit_product['charging_time'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Motor Power</label>
                <input type="text" name="motor_power" class="form-control" placeholder="e.g. 4.5 kW" value="<?php echo htmlspecialchars($edit_product['motor_power'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Weight (kg)</label>
                <input type="number" step="0.01" name="weight" class="form-control" value="<?php echo $edit_product['weight'] ?? ''; ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Colors (comma separated)</label>
                <input type="text" name="colors" class="form-control" placeholder="Red, Blue, Black" value="<?php echo htmlspecialchars(implode(', ', json_decode($edit_product['colors'] ?? '[]', true) ?: [])); ?>">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Image URLs (one per line)</label>
                <textarea name="images" class="form-control" rows="3" placeholder="https://example.com/image1.jpg"><?php echo htmlspecialchars(implode("\n", json_decode($edit_product['images'] ?? '[]', true) ?: [])); ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" <?php echo (isset($edit_product['is_featured']) && $edit_product['is_featured']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_featured">Featured Product</label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (!isset($edit_product) || $edit_product['is_active']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i><?php echo $edit_product ? 'Update Product' : 'Add Product'; ?></button>
    </form>
</div>

<?php else: ?>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Range</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No products found. Add your first product!</td></tr>
                <?php else: ?>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <?php 
                        $images = json_decode($p['images'], true);
                        $img = !empty($images) ? $images[0] : 'https://via.placeholder.com/60x40?text=No+Image';
                        ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" style="width: 60px; height: 40px; object-fit: cover; border-radius: 5px;">
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($p['name']); ?></strong>
                        <br><small class="text-muted"><?php echo htmlspecialchars($p['brand']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($p['category_name'] ?? 'N/A'); ?></td>
                    <td class="text-success"><?php echo formatPrice($p['price']); ?></td>
                    <td><?php echo $p['range_km'] ? $p['range_km'] . ' km' : '-'; ?></td>
                    <td>
                        <span class="badge bg-<?php echo $p['is_active'] ? 'success' : 'secondary'; ?>">
                            <?php echo $p['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                        <?php if ($p['is_featured']): ?>
                        <span class="badge bg-warning">Featured</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="products.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete('products.php?delete=<?php echo $p['id']; ?>', '<?php echo htmlspecialchars($p['name']); ?>')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
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
