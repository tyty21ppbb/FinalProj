<?php
session_start();

if (!isset($_SESSION['islogged'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

require_once('db.php');

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$cart    = $_SESSION['cart'];
$ids     = implode(',', array_map('intval', array_keys($cart)));
$result  = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
$productsById = array();
while ($row = mysqli_fetch_assoc($result)) {
    $productsById[$row['id']] = $row;
}

$items = array();
$total = 0;
foreach ($cart as $id => $qty) {
    if (!isset($productsById[$id])) continue;
    $p        = $productsById[$id];
    $subtotal = $p['price'] * $qty;
    $total   += $subtotal;
    $items[]  = array('product' => $p, 'qty' => $qty, 'subtotal' => $subtotal);
}

if (empty($items)) {
    header('Location: cart.php');
    exit();
}

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
    $contact_number   = mysqli_real_escape_string($conn, $_POST['contact_number']);

    if (empty($shipping_address)) $errors[] = "Shipping address is required.";
    if (empty($contact_number))   $errors[] = "Contact number is required.";

    if (empty($errors)) {
        $_SESSION['checkout'] = array(
            'shipping_address' => $shipping_address,
            'contact_number'   => $contact_number,
        );
        mysqli_close($conn);
        header('Location: payment.php');
        exit();
    }
}

$uid      = (int)$_SESSION['user_id'];
$u_result = mysqli_query($conn, "SELECT address, contact_number FROM users WHERE id = $uid");
$u_row    = mysqli_fetch_assoc($u_result);

$title       = "ChairHive - Checkout";
$currentPage = "cart";

require('include/header.php');
?>

<div class="cv-page-head">
    <div class="cv-page-head-inner">
        <h1><i class="bi bi-truck"></i> Checkout</h1>
        <p>Confirm your shipping details before proceeding to payment.</p>
    </div>
</div>

<div style="max-width:1200px;margin:0 auto;padding:40px 40px 80px;">

    <?php if (!empty($errors)): ?>
        <div class="cv-alert cv-alert-danger mb-4">
            <?php foreach ($errors as $e): ?><p><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="cv-card">
                <p class="cv-card-title">Shipping Details</p>
                <form action="checkout.php" method="post" novalidate>
                    <div class="cv-mb">
                        <label class="cv-form-label">Complete Shipping Address</label>
                        <textarea name="shipping_address" class="cv-form-control" rows="3"><?= htmlspecialchars($_POST['shipping_address'] ?? $u_row['address'] ?? '') ?></textarea>
                    </div>
                    <div class="cv-mb">
                        <label class="cv-form-label">Contact Number</label>
                        <input type="text" name="contact_number" class="cv-form-control"
                               value="<?= htmlspecialchars($_POST['contact_number'] ?? $u_row['contact_number'] ?? '') ?>">
                    </div>
                    <button type="submit" name="submit" class="btn-cv w-100 justify-content-center" style="padding:12px;">
                        Continue to Payment <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-5">
            <div class="cv-order-summary">
                <p class="cv-card-title">Order Summary</p>
                <?php foreach ($items as $row): $p = $row['product']; ?>
                    <div class="cv-summary-row">
                        <span><?= htmlspecialchars($p['name']) ?> &times; <?= (int)$row['qty'] ?></span>
                        <span>&#8369;<?= number_format($row['subtotal'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="cv-summary-total">
                    <span>Total</span>
                    <span>&#8369;<?= number_format($total, 2) ?></span>
                </div>
            </div>
        </div>
    </div>

</div>

<?php mysqli_close($conn); require('include/footer.php'); ?>
