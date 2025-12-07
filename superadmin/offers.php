<?php
$pageTitle = 'Global Offers';
include 'include/header.php';

$success = '';
$error = '';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (mysqli_query($con, "DELETE FROM offers WHERE id = $id AND is_global = 1")) {
        $success = 'Offer deleted successfully!';
    } else {
        $error = 'Failed to delete offer.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $discount_type = sanitize($_POST['discount_type'] ?? 'percentage');
    $discount_value = floatval($_POST['discount_value'] ?? 0);
    $min_purchase = floatval($_POST['min_purchase'] ?? 0);
    $max_discount = floatval($_POST['max_discount'] ?? 0);
    $code = strtoupper(sanitize($_POST['code'] ?? ''));
    $start_date = sanitize($_POST['start_date'] ?? '');
    $end_date = sanitize($_POST['end_date'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if ($action === 'add') {
        $query = "INSERT INTO offers (title, description, discount_type, discount_value, min_purchase, max_discount, code, start_date, end_date, is_global, is_active) 
                  VALUES ('$title', '$description', '$discount_type', $discount_value, $min_purchase, $max_discount, '$code', '$start_date', '$end_date', 1, $is_active)";
        if (mysqli_query($con, $query)) {
            $success = 'Offer added successfully!';
        } else {
            $error = 'Failed to add offer: ' . mysqli_error($con);
        }
    }
    
    if ($action === 'edit' && $id > 0) {
        $query = "UPDATE offers SET title = '$title', description = '$description', discount_type = '$discount_type', 
                  discount_value = $discount_value, min_purchase = $min_purchase, max_discount = $max_discount, 
                  code = '$code', start_date = '$start_date', end_date = '$end_date', is_active = $is_active 
                  WHERE id = $id AND is_global = 1";
        if (mysqli_query($con, $query)) {
            $success = 'Offer updated successfully!';
        } else {
            $error = 'Failed to update offer.';
        }
    }
}

$offers = [];
$result = mysqli_query($con, "SELECT * FROM offers WHERE is_global = 1 ORDER BY created_at DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $offers[] = $row;
    }
}

$edit_offer = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = mysqli_query($con, "SELECT * FROM offers WHERE id = $edit_id AND is_global = 1");
    if ($result) {
        $edit_offer = mysqli_fetch_assoc($result);
    }
}

$show_form = isset($_GET['action']) && $_GET['action'] == 'add' || $edit_offer;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tags me-2" style="color: var(--primary);"></i>Global Offers</h2>
    <?php if (!$show_form): ?>
    <a href="offers.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Offer</a>
    <?php else: ?>
    <a href="offers.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
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
    <h4 class="mb-4"><?php echo $edit_offer ? 'Edit Offer' : 'Add New Offer'; ?></h4>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $edit_offer ? 'edit' : 'add'; ?>">
        <?php if ($edit_offer): ?>
        <input type="hidden" name="id" value="<?php echo $edit_offer['id']; ?>">
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($edit_offer['title'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Coupon Code</label>
                <input type="text" name="code" class="form-control" value="<?php echo htmlspecialchars($edit_offer['code'] ?? ''); ?>">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($edit_offer['description'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Discount Type</label>
                <select name="discount_type" class="form-select">
                    <option value="percentage" <?php echo (isset($edit_offer['discount_type']) && $edit_offer['discount_type'] == 'percentage') ? 'selected' : ''; ?>>Percentage</option>
                    <option value="fixed" <?php echo (isset($edit_offer['discount_type']) && $edit_offer['discount_type'] == 'fixed') ? 'selected' : ''; ?>>Fixed Amount</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Discount Value</label>
                <input type="number" step="0.01" name="discount_value" class="form-control" value="<?php echo $edit_offer['discount_value'] ?? ''; ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Max Discount</label>
                <input type="number" step="0.01" name="max_discount" class="form-control" value="<?php echo $edit_offer['max_discount'] ?? ''; ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Min Purchase</label>
                <input type="number" step="0.01" name="min_purchase" class="form-control" value="<?php echo $edit_offer['min_purchase'] ?? ''; ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?php echo $edit_offer['start_date'] ?? ''; ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?php echo $edit_offer['end_date'] ?? ''; ?>">
            </div>
            <div class="col-12 mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (!isset($edit_offer) || $edit_offer['is_active']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i><?php echo $edit_offer ? 'Update Offer' : 'Add Offer'; ?></button>
    </form>
</div>

<?php else: ?>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($offers)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No global offers found.</td></tr>
                <?php else: ?>
                <?php foreach ($offers as $o): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($o['title']); ?></strong></td>
                    <td><code><?php echo htmlspecialchars($o['code'] ?: 'N/A'); ?></code></td>
                    <td>
                        <?php if ($o['discount_type'] == 'percentage'): ?>
                        <?php echo $o['discount_value']; ?>%
                        <?php else: ?>
                        <?php echo formatPrice($o['discount_value']); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($o['start_date'] && $o['end_date']): ?>
                        <?php echo date('d M', strtotime($o['start_date'])); ?> - <?php echo date('d M Y', strtotime($o['end_date'])); ?>
                        <?php else: ?>
                        No limit
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo $o['is_active'] ? 'success' : 'secondary'; ?>">
                            <?php echo $o['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="offers.php?edit=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete('offers.php?delete=<?php echo $o['id']; ?>', '<?php echo htmlspecialchars($o['title']); ?>')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
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
