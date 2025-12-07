<?php
session_start();
include '../include/config.php';
include 'include/header.php';

$pageTitle = 'Invoices';
$admin_id = $_SESSION['admin_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_invoice'])) {
    $order_id = intval($_POST['order_id']);
    $user_id = intval($_POST['user_id']);
    $subtotal = floatval($_POST['subtotal']);
    $cgst = floatval($_POST['cgst'] ?? 0);
    $sgst = floatval($_POST['sgst'] ?? 0);
    $discount = floatval($_POST['discount'] ?? 0);
    $service_charges = floatval($_POST['service_charges'] ?? 0);
    $total = $subtotal + $cgst + $sgst + $service_charges - $discount;
    $invoice_number = generateInvoiceNumber();
    
    $query = "INSERT INTO invoices (invoice_number, order_id, admin_id, user_id, subtotal, cgst, sgst, discount, service_charges, total_amount, invoice_date, status)
              VALUES ('$invoice_number', $order_id, $admin_id, $user_id, $subtotal, $cgst, $sgst, $discount, $service_charges, $total, CURDATE(), 'draft')";
    
    if (mysqli_query($con, $query)) {
        $success = "Invoice $invoice_number created successfully!";
    } else {
        $error = 'Failed to create invoice.';
    }
}

if (isset($_GET['send']) && is_numeric($_GET['send'])) {
    $id = intval($_GET['send']);
    mysqli_query($con, "UPDATE invoices SET status = 'sent' WHERE id = $id AND admin_id = $admin_id");
    $success = 'Invoice sent to customer!';
}

if (isset($_GET['paid']) && is_numeric($_GET['paid'])) {
    $id = intval($_GET['paid']);
    mysqli_query($con, "UPDATE invoices SET status = 'paid' WHERE id = $id AND admin_id = $admin_id");
    $success = 'Invoice marked as paid!';
}

$invoices = [];
$result = mysqli_query($con, "
    SELECT i.*, u.full_name as customer_name, u.email, o.order_number
    FROM invoices i 
    LEFT JOIN users u ON i.user_id = u.id
    LEFT JOIN orders o ON i.order_id = o.id
    WHERE i.admin_id = $admin_id 
    ORDER BY i.created_at DESC
");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $invoices[] = $row;
    }
}

$orders_for_invoice = [];
$result = mysqli_query($con, "
    SELECT o.*, u.full_name, u.id as user_id
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.admin_id = $admin_id AND o.id NOT IN (SELECT order_id FROM invoices WHERE order_id IS NOT NULL)
    ORDER BY o.created_at DESC
");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders_for_invoice[] = $row;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-file-invoice me-2" style="color: var(--primary);"></i>Invoices</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
        <i class="fas fa-plus me-2"></i>Create Invoice
    </button>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($invoices)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No invoices yet.</td></tr>
                <?php else: ?>
                <?php foreach ($invoices as $inv): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($inv['invoice_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($inv['order_number'] ?? 'N/A'); ?></td>
                    <td>
                        <?php echo htmlspecialchars($inv['customer_name']); ?>
                        <br><small class="text-muted"><?php echo htmlspecialchars($inv['email']); ?></small>
                    </td>
                    <td class="text-success fw-bold"><?php echo formatPrice($inv['total_amount']); ?></td>
                    <td>
                        <span class="badge bg-<?php 
                            echo $inv['status'] == 'paid' ? 'success' : 
                                ($inv['status'] == 'sent' ? 'info' : 
                                ($inv['status'] == 'overdue' ? 'danger' : 'warning')); 
                        ?>">
                            <?php echo ucfirst($inv['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($inv['invoice_date'])); ?></td>
                    <td>
                        <?php if ($inv['status'] == 'draft'): ?>
                        <a href="invoices.php?send=<?php echo $inv['id']; ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-paper-plane"></i></a>
                        <?php endif; ?>
                        <?php if ($inv['status'] != 'paid'): ?>
                        <a href="invoices.php?paid=<?php echo $inv['id']; ?>" class="btn btn-sm btn-outline-success"><i class="fas fa-check"></i></a>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-outline-primary" onclick="window.print()"><i class="fas fa-print"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: var(--accent); color: #fff;">
            <div class="modal-header border-0">
                <h5 class="modal-title">Create Invoice</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="create_invoice" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label">Select Order</label>
                        <select name="order_id" class="form-select" required onchange="updateUserAndAmount(this)">
                            <option value="">Choose an order</option>
                            <?php foreach ($orders_for_invoice as $order): ?>
                            <option value="<?php echo $order['id']; ?>" data-user="<?php echo $order['user_id']; ?>" data-amount="<?php echo $order['total_amount']; ?>">
                                <?php echo $order['order_number']; ?> - <?php echo $order['full_name']; ?> (<?php echo formatPrice($order['total_amount']); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <input type="hidden" name="user_id" id="user_id">
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Subtotal</label>
                            <input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Discount</label>
                            <input type="number" step="0.01" name="discount" class="form-control" value="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">CGST (9%)</label>
                            <input type="number" step="0.01" name="cgst" class="form-control" value="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">SGST (9%)</label>
                            <input type="number" step="0.01" name="sgst" class="form-control" value="0">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Service Charges</label>
                            <input type="number" step="0.01" name="service_charges" class="form-control" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateUserAndAmount(select) {
    var option = select.options[select.selectedIndex];
    document.getElementById('user_id').value = option.dataset.user || '';
    document.getElementById('subtotal').value = option.dataset.amount || '';
}
</script>

<?php include 'include/footer.php'; ?>
