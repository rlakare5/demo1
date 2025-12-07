<?php
$pageTitle = 'All Products';
include 'include/header.php';

$products = [];
$result = mysqli_query($con, "SELECT p.*, c.name as category_name, a.showroom_name 
                              FROM products p 
                              LEFT JOIN categories c ON p.category_id = c.id 
                              LEFT JOIN admin a ON p.admin_id = a.id 
                              ORDER BY p.created_at DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-motorcycle me-2" style="color: var(--primary);"></i>All Products</h2>
    <span class="badge bg-primary fs-6"><?php echo count($products); ?> Total Products</span>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Showroom</th>
                    <th>Price</th>
                    <th>Range</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No products found.</td></tr>
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
                    <td><span class="badge bg-info"><?php echo htmlspecialchars($p['showroom_name'] ?? 'N/A'); ?></span></td>
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
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>
