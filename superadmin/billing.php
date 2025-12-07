<?php
$pageTitle = 'Billing Configuration';
include 'include/header.php';

$success = '';
$error = '';

$billing = [
    'tax_rate' => 18,
    'currency' => 'INR',
    'currency_symbol' => '₹',
    'invoice_prefix' => 'INV',
    'gst_number' => '',
    'company_name' => 'EV Showroom',
    'company_address' => ''
];

$result = mysqli_query($con, "SELECT * FROM settings WHERE setting_key LIKE 'billing_%'");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $key = str_replace('billing_', '', $row['setting_key']);
        $billing[$key] = $row['setting_value'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['tax_rate', 'currency', 'currency_symbol', 'invoice_prefix', 'gst_number', 'company_name', 'company_address'];
    
    foreach ($fields as $field) {
        $value = sanitize($_POST[$field] ?? '');
        $key = 'billing_' . $field;
        
        $check = mysqli_query($con, "SELECT id FROM settings WHERE setting_key = '$key'");
        if (mysqli_num_rows($check) > 0) {
            mysqli_query($con, "UPDATE settings SET setting_value = '$value' WHERE setting_key = '$key'");
        } else {
            mysqli_query($con, "INSERT INTO settings (setting_key, setting_value) VALUES ('$key', '$value')");
        }
    }
    
    $success = 'Billing configuration saved successfully!';
    
    $result = mysqli_query($con, "SELECT * FROM settings WHERE setting_key LIKE 'billing_%'");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $key = str_replace('billing_', '', $row['setting_key']);
            $billing[$key] = $row['setting_value'];
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-file-invoice me-2" style="color: var(--primary);"></i>Billing Configuration</h2>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card p-4">
    <form method="POST">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Tax Rate (%)</label>
                <input type="number" step="0.01" name="tax_rate" class="form-control" value="<?php echo htmlspecialchars($billing['tax_rate']); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Currency Code</label>
                <input type="text" name="currency" class="form-control" value="<?php echo htmlspecialchars($billing['currency']); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Currency Symbol</label>
                <input type="text" name="currency_symbol" class="form-control" value="<?php echo htmlspecialchars($billing['currency_symbol']); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Invoice Prefix</label>
                <input type="text" name="invoice_prefix" class="form-control" value="<?php echo htmlspecialchars($billing['invoice_prefix']); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">GST Number</label>
                <input type="text" name="gst_number" class="form-control" value="<?php echo htmlspecialchars($billing['gst_number']); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($billing['company_name']); ?>">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Company Address</label>
                <textarea name="company_address" class="form-control" rows="3"><?php echo htmlspecialchars($billing['company_address']); ?></textarea>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Configuration</button>
    </form>
</div>

<?php include 'include/footer.php'; ?>
