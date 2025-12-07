<?php
include '../include/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$action = $_POST['action'] ?? '';
$product_id = intval($_POST['product_id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($action === 'add' && $product_id > 0) {
    $check = mysqli_query($con, "SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['success' => false, 'message' => 'Already in wishlist']);
    } else {
        $result = mysqli_query($con, "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)");
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Added to wishlist']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add']);
        }
    }
} elseif ($action === 'remove' && $product_id > 0) {
    $result = mysqli_query($con, "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Removed from wishlist']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
