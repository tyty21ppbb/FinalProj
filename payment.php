<?php
session_start();

if (!isset($_SESSION['islogged'])) { header('Location: login.php?redirect=checkout.php'); exit(); }
if (!isset($_SESSION['checkout'])) { header('Location: checkout.php'); exit(); }

require_once('db.php');

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
if (empty($cart)) { header('Location: cart.php'); exit(); }

$ids          = implode(',', array_map('intval', array_keys($cart)));
$result       = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
$productsById = array();
while ($row = mysqli_fetch_assoc($result)) {
    $productsById[$row['id']] = $row;
}

$items = array();
$total = 0;
foreach ($cart as $id => $qty) {
    if (!isset($productsById[$id])) continue;
    $p   = $productsById[$id];
    $qty = min((int)$qty, (int)$p['stock_qty']);
    if ($qty <= 0) continue;
    $subtotal = $p['price'] * $qty;
    $total   += $subtotal;
    $items[]  = array('product' => $p, 'qty' => $qty, 'subtotal' => $subtotal);
}

if (empty($items)) { header('Location: cart.php'); exit(); }

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $method    = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
    $reference = "";

    if (!in_array($method, array('Credit/Debit Card', 'GCash', 'Cash on Delivery'))) {
        $errors[] = "Please select a payment method.";
    }

    if ($method == 'Credit/Debit Card') {
        $card_name   = trim($_POST['card_name']   ?? '');
        $card_number = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
        $card_expiry = trim($_POST['card_expiry'] ?? '');
        $card_cvv    = trim($_POST['card_cvv']    ?? '');
        if (empty($card_name))         $errors[] = "Cardholder name is required.";
        if (strlen($card_number) < 12) $errors[] = "Please enter a valid card number.";
        if (empty($card_expiry))       $errors[] = "Card expiry is required.";
        if (!preg_match('/^\d{3,4}$/', $card_cvv)) $errors[] = "Please enter a valid CVV.";
        if (empty($errors)) $reference = "Card ending in " . substr($card_number, -4);
    } elseif ($method == 'GCash') {
        $gcash_number = trim($_POST['gcash_number'] ?? '');
        if (!preg_match('/^[0-9]{10,13}$/', $gcash_number)) {
            $errors[] = "Please enter a valid GCash mobile number.";
        } else {
            $reference = "GCash " . substr($gcash_number, 0, 4) . "****" . substr($gcash_number, -2);
        }
    } elseif ($method == 'Cash on Delivery') {
        $reference = "Pay upon delivery";
    }

    if (empty($errors)) {
        $uid           = (int)$_SESSION['user_id'];
        $checkout      = $_SESSION['checkout'];
        $address_esc   = mysqli_real_escape_string($conn, $checkout['shipping_address']);
        $contact_esc   = mysqli_real_escape_string($conn, $checkout['contact_number']);
        $method_esc    = mysqli_real_escape_string($conn, $method);
        $reference_esc = mysqli_real_escape_string($conn, $reference);

        $order_sql = "INSERT INTO orders (user_id, shipping_address, contact_number, payment_method, payment_reference, total_amount, status)
                      VALUES ($uid, '$address_esc', '$contact_esc', '$method_esc', '$reference_esc', $total, 'Paid (Simulated)')";

        if (mysqli_query($conn, $order_sql)) {
            $order_id = mysqli_insert_id($conn);

            foreach ($items as $row) {
                $p         = $row['product'];
                $pid       = (int)$p['id'];
                $pname_esc = mysqli_real_escape_string($conn, $p['name']);
                $uprice    = (float)$p['price'];
                $qty       = (int)$row['qty'];
                mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity)
                                     VALUES ($order_id, $pid, '$pname_esc', $uprice, $qty)");
                mysqli_query($conn, "UPDATE products SET stock_qty = stock_qty - $qty WHERE id = $pid AND stock_qty >= $qty");
            }

            $name_esc = mysqli_real_escape_string($conn, $_SESSION['fullname']);
            $desc_esc = mysqli_real_escape_string($conn, "$name_esc placed order #$order_id for ₱" . number_format($total, 2) . " ($method).");
            mysqli_query($conn, "INSERT INTO audit_log (user_id, actor_name, actor_role, action, description)
                                 VALUES ($uid, '$name_esc', 'buyer', 'PLACE_ORDER', '$desc_esc')");

            unset($_SESSION['cart'], $_SESSION['checkout']);
            mysqli_close($conn);
            header('Location: order_confirmation.php?order=' . $order_id);
            exit();
        } else {
            $errors[] = "Something went wrong placing your order: " . mysqli_error($conn);
        }
    }
}

$title       = "ChairHive - Payment";
$currentPage = "cart";
require('include/header.php');
?>

<div class="cv-page-head">
    <div class="cv-page-head-inner">
        <h1><i class="bi bi-credit-card"></i> Payment</h1>
        <p>Simulated payment step &mdash; no real payment processor is connected.</p>
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
                <p class="cv-card-title">Payment Method</p>
                <form action="payment.php" method="post" novalidate id="paymentForm">

                    <label class="cv-pay-option">
                        <input type="radio" name="payment_method" value="Credit/Debit Card" onclick="togglePayment('card')"
                               <?= (!isset($_POST['payment_method']) || $_POST['payment_method'] == 'Credit/Debit Card') ? 'checked' : '' ?>>
                        <i class="bi bi-credit-card-2-front" style="color:#0E7490;font-size:1.2rem;"></i>
                        Credit / Debit Card
                    </label>
                    <label class="cv-pay-option">
                        <input type="radio" name="payment_method" value="GCash" onclick="togglePayment('gcash')"
                               <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'GCash') ? 'checked' : '' ?>>
                        <i class="bi bi-phone" style="color:#0E7490;font-size:1.2rem;"></i>
                        GCash
                    </label>
                    <label class="cv-pay-option">
                        <input type="radio" name="payment_method" value="Cash on Delivery" onclick="togglePayment('cod')"
                               <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Cash on Delivery') ? 'checked' : '' ?>>
                        <i class="bi bi-cash-coin" style="color:#0E7490;font-size:1.2rem;"></i>
                        Cash on Delivery
                    </label>

                    <div id="fields-card" style="margin-top:16px;">
                        <div class="cv-mb">
                            <label class="cv-form-label">Cardholder Name</label>
                            <input type="text" name="card_name" class="cv-form-control" value="<?= htmlspecialchars($_POST['card_name'] ?? '') ?>">
                        </div>
                        <div class="cv-form-row cv-mb">
                            <div>
                                <label class="cv-form-label">Card Number</label>
                                <input type="text" name="card_number" class="cv-form-control" placeholder="0000 0000 0000 0000" maxlength="19" value="<?= htmlspecialchars($_POST['card_number'] ?? '') ?>">
                            </div>
                            <div>
                                <label class="cv-form-label">Expiry (MM/YY)</label>
                                <input type="text" name="card_expiry" class="cv-form-control" placeholder="MM/YY" maxlength="5" value="<?= htmlspecialchars($_POST['card_expiry'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="cv-mb" style="max-width:140px;">
                            <label class="cv-form-label">CVV</label>
                            <input type="text" name="card_cvv" class="cv-form-control" maxlength="4" value="<?= htmlspecialchars($_POST['card_cvv'] ?? '') ?>">
                        </div>
                        <div class="cv-alert cv-alert-warning">
                            <i class="bi bi-info-circle"></i> <strong>Demo only:</strong> No real card data is processed or stored.
                        </div>
                    </div>

                    <div id="fields-gcash" style="display:none;margin-top:16px;">
                        <div class="cv-mb">
                            <label class="cv-form-label">GCash Mobile Number</label>
                            <input type="text" name="gcash_number" class="cv-form-control" placeholder="09171234567" value="<?= htmlspecialchars($_POST['gcash_number'] ?? '') ?>">
                        </div>
                    </div>

                    <div id="fields-cod" style="display:none;margin-top:16px;">
                        <div class="cv-alert cv-alert-warning">
                            <i class="bi bi-truck"></i> You will pay the courier in cash when your order arrives.
                        </div>
                    </div>

                    <button type="submit" name="submit" class="btn-cv w-100 justify-content-center mt-2" style="padding:13px;font-size:1rem;">
                        <i class="bi bi-bag-check"></i> Place Order &mdash; &#8369;<?= number_format($total, 2) ?>
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
                <p style="font-size:0.82rem;color:#64748B;margin-top:14px;">
                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($_SESSION['checkout']['shipping_address']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function togglePayment(method) {
    document.getElementById('fields-card').style.display  = (method === 'card')  ? 'block' : 'none';
    document.getElementById('fields-gcash').style.display = (method === 'gcash') ? 'block' : 'none';
    document.getElementById('fields-cod').style.display   = (method === 'cod')   ? 'block' : 'none';
}
window.onload = function() {
    var selected = document.querySelector('input[name="payment_method"]:checked');
    if (selected) {
        if (selected.value === 'Credit/Debit Card') togglePayment('card');
        else if (selected.value === 'GCash')        togglePayment('gcash');
        else                                         togglePayment('cod');
    }
};
</script>

<?php mysqli_close($conn); require('include/footer.php'); ?>
