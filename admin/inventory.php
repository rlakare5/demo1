<?php
session_start();
include '../include/config.php';
include 'include/header.php';

$pageTitle = 'Inventory';
$admin_id = $_SESSION['admin_id'];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $min_stock = intval($_POST['min_stock_alert']);
    
    $check = mysqli_query($con, "SELECT id FROM inventory WHERE product_id = $product_id AND admin_id = $admin_id");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($con, "UPDATE inventory SET quantity = $quantity, min_stock_alert = $min_stock, last_restocked = NOW() WHERE product_id = $product_id AND admin_id = $admin_id");
    } else {
        mysqli_query($con, "INSERT INTO inventory (product_id, admin_id, quantity, min_stock_alert, last_restocked) VALUES ($product_id, $admin_id, $quantity, $min_stock, NOW())");
    }
    $success = 'Inventory updated!';
}

$products = [];
$result = mysqli_query($con, "
    SELECT p.*, COALESCE(i.quantity, 0) as stock, COALESCE(i.reserved, 0) as reserved, COALESCE(i.min_stock_alert, 5) as min_stock_alert, i.last_restocked
    FROM products p 
    LEFT JOIN inventory i ON p.id = i.product_id AND i.admin_id = $admin_id
    WHERE p.admin_id = $admin_id 
    ORDER BY p.name
");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

$low_stock = array_filter($products, function($p) { return $p['stock'] <= $p['min_stock_alert']; });
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-warehouse me-2" style="color: var(--primary);"></i>Inventory</h2>
    <?php if (count($low_stock) > 0): ?>
    <span class="badge bg-danger fs-6"><i class="fas fa-exclamation-triangle me-1"></i><?php echo count($low_stock); ?> Low Stock</span>
    <?php endif; ?>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card text-center">
            <i class="fas fa-box mb-2" style="font-size: 1.5rem; color: var(--primary);"></i>
            <h3><?php echo count($products); ?></h3>
            <p class="text-muted mb-0">Total Products</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <i class="fas fa-cubes mb-2" style="font-size: 1.5rem; color: var(--primary);"></i>
            <h3><?php echo array_sum(array_column($products, 'stock')); ?></h3>
            <p class="text-muted mb-0">Total Stock</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <i class="fas fa-lock mb-2" style="font-size: 1.5rem; color: #ffc107;"></i>
            <h3><?php echo array_sum(array_column($products, 'reserved')); ?></h3>
            <p class="text-muted mb-0">Reserved</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <i class="fas fa-exclamation-triangle mb-2" style="font-size: 1.5rem; color: #dc3545;"></i>
            <h3><?php echo count($low_stock); ?></h3>
            <p class="text-muted mb-0">Low Stock</p>
        </div>
    </div>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Available</th>
                    <th>Reserved</th>
                    <th>Min Alert</th>
                    <th>Status</th>
                    <th>Last Restocked</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No products. Add products first.</td></tr>
                <?php else: ?>
                <?php foreach ($products as $p): ?>
                <tr class="<?php echo $p['stock'] <= $p['min_stock_alert'] ? 'table-danger' : ''; ?>">
                    <td>
                        <strong><?php echo htmlspecialchars($p['name']); ?></strong>
                        <br><small class="text-muted"><?php echo htmlspecialchars($p['brand']); ?></small>
                    </td>
                    <td><span class="badge bg-<?php echo $p['stock'] > 0 ? 'success' : 'danger'; ?> fs-6"><?php echo $p['stock']; ?></span></td>
                    <td><span class="badge bg-warning"><?php echo $p['reserved']; ?></span></td>
                    <td><?php echo $p['min_stock_alert']; ?></td>
                    <td>
                        <?php if ($p['stock'] == 0): ?>
                        <span class="badge bg-danger">Out of Stock</span>
                        <?php elseif ($p['stock'] <= $p['min_stock_alert']): ?>
                        <span class="badge bg-warning">Low Stock</span>
                        <?php else: ?>
                        <span class="badge bg-success">In Stock</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $p['last_restocked'] ? date('M d, Y', strtotime($p['last_restocked'])) : 'Never'; ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#stockModal<?php echo $p['id']; ?>">
                            <i class="fas fa-edit"></i> Update
                        </button>
                    </td>
                </tr>

                <div class="modal fade" id="stockModal<?php echo $p['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content" style="background: var(--accent); color: #fff;">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">Update Stock</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <input type="hidden" name="update_stock" value="1">
                                    
                                    <p class="mb-3"><strong><?php echo htmlspecialchars($p['name']); ?></strong></p>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" name="quantity" class="form-control" value="<?php echo $p['stock']; ?>" min="0">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Low Stock Alert</label>
                                        <input type="number" name="min_stock_alert" class="form-control" value="<?php echo $p['min_stock_alert']; ?>" min="0">
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="submit" class="btn btn-primary w-100">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>
