<?php
$pageTitle = 'Themes';
include 'include/header.php';

$success = '';

$themes = [
    ['id' => 'default', 'name' => 'Default Green', 'primary' => '#00ff88', 'secondary' => '#1a1a2e'],
    ['id' => 'blue', 'name' => 'Ocean Blue', 'primary' => '#00a8ff', 'secondary' => '#1a1a2e'],
    ['id' => 'purple', 'name' => 'Royal Purple', 'primary' => '#9c27b0', 'secondary' => '#1a1a2e'],
    ['id' => 'orange', 'name' => 'Sunset Orange', 'primary' => '#ff5722', 'secondary' => '#1a1a2e'],
    ['id' => 'red', 'name' => 'Crimson Red', 'primary' => '#f44336', 'secondary' => '#1a1a2e'],
    ['id' => 'gold', 'name' => 'Golden', 'primary' => '#ffc107', 'secondary' => '#1a1a2e']
];

$current_theme = 'default';
$result = mysqli_query($con, "SELECT setting_value FROM settings WHERE setting_key = 'site_theme'");
if ($result && $row = mysqli_fetch_assoc($result)) {
    $current_theme = $row['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = sanitize($_POST['theme'] ?? 'default');
    
    $check = mysqli_query($con, "SELECT id FROM settings WHERE setting_key = 'site_theme'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($con, "UPDATE settings SET setting_value = '$theme' WHERE setting_key = 'site_theme'");
    } else {
        mysqli_query($con, "INSERT INTO settings (setting_key, setting_value) VALUES ('site_theme', '$theme')");
    }
    
    $current_theme = $theme;
    $success = 'Theme updated successfully!';
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-palette me-2" style="color: var(--primary);"></i>Theme Management</h2>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card p-4">
    <h5 class="mb-4">Select Theme</h5>
    <form method="POST">
        <div class="row g-4">
            <?php foreach ($themes as $theme): ?>
            <div class="col-md-4">
                <div class="card p-3 h-100 <?php echo $current_theme == $theme['id'] ? 'border-success' : ''; ?>" style="cursor: pointer;" onclick="document.getElementById('theme_<?php echo $theme['id']; ?>').click();">
                    <div class="d-flex align-items-center mb-3">
                        <input type="radio" name="theme" id="theme_<?php echo $theme['id']; ?>" value="<?php echo $theme['id']; ?>" 
                               <?php echo $current_theme == $theme['id'] ? 'checked' : ''; ?> class="form-check-input me-2">
                        <label class="form-check-label" for="theme_<?php echo $theme['id']; ?>"><strong><?php echo $theme['name']; ?></strong></label>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background: <?php echo $theme['primary']; ?>;"></div>
                        <div style="width: 40px; height: 40px; border-radius: 8px; background: <?php echo $theme['secondary']; ?>;"></div>
                    </div>
                    <?php if ($current_theme == $theme['id']): ?>
                    <span class="badge bg-success mt-2">Current Theme</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <button type="submit" class="btn btn-primary mt-4"><i class="fas fa-save me-2"></i>Apply Theme</button>
    </form>
</div>

<div class="card p-4 mt-4">
    <h5 class="mb-3"><i class="fas fa-info-circle me-2" style="color: var(--primary);"></i>Custom Theme</h5>
    <p class="text-muted">For custom themes, you can modify the CSS variables in the theme configuration file. Contact your developer for advanced customization options.</p>
</div>

<?php include 'include/footer.php'; ?>
