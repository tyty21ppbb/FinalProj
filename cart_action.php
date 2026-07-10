<?php
session_start();
require_once('db.php');

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if ($action == 'add') {

    $id = (int)$_POST['id'];
    $sql     = "SELECT id, name, stock_qty FROM products WHERE id = $id AND is_active = 1";
    $result  = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);

    $addedName = "";
    if ($product && (int)$product['stock_qty'] > 0) {
        $currentQty = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id] : 0;
        if ($currentQty < (int)$product['stock_qty']) {
            $_SESSION['cart'][$id] = $currentQty + 1;
        }
        $addedName = $product['name'];
    }

    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'store.php';
    if ($addedName != '') {
        if (strpos($redirect, '#') !== false) {
            $parts    = explode('#', $redirect, 2);
            $parts[0] .= (strpos($parts[0], '?') === false ? '?' : '&') . 'added=' . urlencode($addedName);
            $redirect  = $parts[0] . '#' . $parts[1];
        } else {
            $redirect .= (strpos($redirect, '?') === false ? '?' : '&') . 'added=' . urlencode($addedName);
        }
    }

} elseif ($action == 'remove') {

    $id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['id'];
    unset($_SESSION['cart'][$id]);
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'cart.php';

} elseif ($action == 'update_all') {

    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $id => $qty) {
            $id  = (int)$id;
            $qty = (int)$qty;
            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
                continue;
            }
            $sql     = "SELECT stock_qty FROM products WHERE id = $id AND is_active = 1";
            $result  = mysqli_query($conn, $sql);
            $product = mysqli_fetch_assoc($result);
            if ($product) {
                $_SESSION['cart'][$id] = min($qty, (int)$product['stock_qty']);
            }
        }
    }
    $redirect = 'cart.php';

} elseif ($action == 'clear') {

    $_SESSION['cart'] = array();
    $redirect = 'cart.php';

} else {
    $redirect = 'store.php';
}

mysqli_close($conn);
header('Location: ' . $redirect);
exit();
?>
