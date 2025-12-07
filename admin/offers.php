<?php
session_start();
include '../include/config.php';
include 'include/header.php';

$pageTitle = 'Offers';
$admin_id = $_SESSION['admin_id'];
$success = '';
$error = '';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (mysqli_query($con, "DELETE FROM offers WHERE id = $id AND admin_id = $admin_id")) {
        $success = 'Offer deleted!';
    }
}

if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    mysqli_query($con, "UPDATE offers SET is_active = NOT is_active WHERE id = $id AND admin_id = $admin_id");
    $success = 'Offer status updated!';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $offer_type = sanitize($_POST['offer_type'] ?? 'discount');
    $discount_type = sanitize($_POST['discount_type'] ?? 'percentage');
    $discount_value = floatval($_POST['discount_value'] ?? 0);
    $coupon_code = sanitize($_POST['coupon_code'] ?? '');
    $min_order = floatval($_POST['min_order_amount'] ?? 0);
    $max_discount = floatval($_POST['max_discount'] ?? 0);
    $usage_limit = intval($_POST['usage_limit'] ?? 0);
    $start_date = sanitize($_POST['start_date'] ?? date('Y-m-d'));
    $end_date = sanitize($_POST['end_date'] ?? date('Y-m-d', strtotime('+30 days')));
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if ($action === 'add') {
        $query = "INSERT INTO offers (admin_id, title, description, offer_type, discount_type, discount_value, coupon_code, 
                  min_order_amount, max_discount, usage_limit, start_date, end_date, is_active)
                  VALUES ($admin_id, '$title', '$description', '$offer_type', '$discount_type', $discount_value, 
                  " . ($coupon_code ? "'$coupon_code'" : "NULL") . ", $min_order, $max_discount, $usage_limit, '$start_date', '$end_date', $is_active)";
        if (mysqli_query($con, $query)) {
            $success = 'Offer created successfully!';
        } else {
            $error = 'Failed to create offer: ' . mysqli_error($con);
        }
    }
    
    if ($action === 'edit' && $id > 0) {
        $query = "UPDATE offers SET title = '$title', description = '$description', offer_type = '$offer_type', 
                  discount_type = '$discount_type', discount_value = $discount_value, 
                  coupon_code = " . ($coupon_code ? "'$coupon_code'" : "NULL") . ", 
                  min_order_amount = $min_order, max_discount = $max_discount, usage_limit = $usage_limit, 
                  start_date = '$start_date', end_date = '$end_date', is_active = $is_active 
                  WHERE id = $id AND admin_id = $admin_id";
        if (mysqli_query($con, $query)) {
            $success = 'Offer updated successfully!';
        } else {
            $error = 'Failed to update offer.';
        }
    }
}

$offers = [];
$result = mysqli_query($con, "SELECT * FROM offers WHERE admin_id = $admin_id ORDER BY created_at DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $offers[] = $row;
    }
}

$edit_offer = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = mysqli_query($con, "SELECT * FROM offers WHERE id = $edit_id AND admin_id = $admin_id");
    if ($result) {
        $edit_offer = mysqli_fetch_assoc($result);
    }
}

$show_form = isset($_GET['action']) && $_GET['action'] == 'add' || $edit_offer;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tags me-2" style="color: var(--primary);"></i>Offers</h2>
    <?php if (!$show_form): ?>
    <a href="offers.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Create Offer</a>
    <?php else: ?>
    <a href="offers.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
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
    <h4 class="mb-4"><?php echo $edit_offer ? 'Edit Offer' : 'Create New Offer'; ?></h4>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $edit_offer ? 'edit' : 'add'; ?>">
        <?php if ($edit_offer): ?>
        <input type="hidden" name="id" value="<?php echo $edit_offer['id']; ?>">
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Offer Title *</label>
                <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($edit_offer['title'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Offer Type</label>
                <select name="offer_type" class="form-select">
                    <option value="discount" <?php echo ($edit_offer['offer_type'] ?? '') == 'discount' ? 'selected' : ''; ?>>Discount</option>
                    <option value="exchange" <?php echo ($edit_offer['offer_type'] ?? '') == 'exchange' ? 'selected' : ''; ?>>Exchange</option>
                    <option value="festive" <?php echo ($edit_offer['offer_type'] ?? '') == 'festive' ? 'selected' : ''; ?>>Festive Sale</option>
                    <option value="launch" <?php echo ($edit_offer['offer_type'] ?? '') == 'launch' ? 'selected' : ''; ?>>Launch Offer</option>
                    <option value="coupon" <?php echo ($edit_offer['offer_type'] ?? '') == 'coupon' ? 'selected' : ''; ?>>Coupon</option>
                </select>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($edit_offer['description'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Discount Type</label>
                <select name="discount_type" class="form-select">
                    <option value="percentage" <?php echo ($edit_offer['discount_type'] ?? '') == 'percentage' ? 'selected' : ''; ?>>Percentage (%)</option>
                    <option value="fixed" <?php echo ($edit_offer['discount_type'] ?? '') == 'fixed' ? 'selected' : ''; ?>>Fixed Amount</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Discount Value</label>
                <input type="number" step="0.01" name="discount_value" class="form-control" value="<?php echo $edit_offer['discount_value'] ?? ''; ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Coupon Code</label>
                <input type="text" name="coupon_code" class="form-control" placeholder="e.g. SAVE20" value="<?php echo htmlspecialchars($edit_offer['coupon_code'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Min Order Amount</label>
                <input type="number" step="0.01" name="min_order_amount" class="form-control" value="<?php echo $edit_offer['min_order_amount'] ?? '0'; ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Max Discount</label>
                <input type="number" step="0.01" name="max_discount" class="form-control" value="<?php echo $edit_offer['max_discount'] ?? ''; ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Usage Limit</label>
                <input type="number" name="usage_limit" class="form-control" placeholder="0 = unlimited" value="<?php echo $edit_offer['usage_limit'] ?? '0'; ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?php echo $edit_offer['start_date'] ?? date('Y-m-d'); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?php echo $edit_offer['end_date'] ?? date('Y-m-d', strtotime('+30 days')); ?>">
            </div>
            <div class="col-12 mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (!isset($edit_offer) || $edit_offer['is_active']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i><?php echo $edit_offer ? 'Update' : 'Create'; ?> Offer</button>
    </form>
</div>

<?php else: ?>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Discount</th>
                    <th>Code</th>
                    <th>Validity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($offers)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No offers yet.</td></tr>
                <?php else: ?>
                <?php foreach ($offers as $o): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($o['title']); ?></strong></td>
                    <td><span class="badge bg-info"><?php echo ucfirst($o['offer_type']); ?></span></td>
                    <td>
                        <?php echo $o['discount_value']; ?><?php echo $o['discount_type'] == 'percentage' ? '%' : ' ₹'; ?>
                    </td>
                    <td><?php echo $o['coupon_code'] ? '<code>' . htmlspecialchars($o['coupon_code']) . '</code>' : '-'; ?></td>
                    <td>
                        <?php echo date('M d', strtotime($o['start_date'])); ?> - <?php echo date('M d, Y', strtotime($o['end_date'])); ?>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo $o['is_active'] ? 'success' : 'secondary'; ?>">
                            <?php echo $o['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="offers.php?edit=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <a href="offers.php?toggle=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-warning"><i class="fas fa-power-off"></i></a>
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
