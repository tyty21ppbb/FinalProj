<?php
session_start();
if (!isset($_SESSION['islogged'])) { header('Location: login.php'); exit(); }

require_once('db.php');

$order_id = (int)(isset($_GET['order']) ? $_GET['order'] : 0);
$uid      = (int)$_SESSION['user_id'];

$result = mysqli_query($conn, "SELECT * FROM orders WHERE id = $order_id AND user_id = $uid");
$order  = mysqli_fetch_assoc($result);
if (!$order) { header('Location: index.php'); exit(); }

$items_result = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = $order_id");

$title       = "ChairHive - Order Confirmed";
$currentPage = "";
require('include/header.php');
?>

<div class="cv-page-head">
    <div class="cv-page-head-inner">
        <h1><i class="bi bi-check-circle"></i> Order Confirmed</h1>
        <p>Your order has been placed successfully.</p>
    </div>
</div>

<div style="max-width:760px;margin:0 auto;padding:48px 40px 80px;">

    <div class="text-center mb-4">
        <i class="bi bi-check-circle-fill" style="font-size:3.5rem;color:#16A34A;"></i>
        <h2 class="fw-bold mt-3">Thank you, <?= htmlspecialchars(explode(' ', $_SESSION['fullname'])[0]) ?>!</h2>
    </div>

    <div class="cv-alert cv-alert-success mb-4">
        <strong>Order #<?= (int)$order['id'] ?></strong> &mdash;
        Payment: <?= htmlspecialchars($order['payment_method']) ?>
        <?php if ($order['payment_reference']): ?>
            (<?= htmlspecialchars($order['payment_reference']) ?>)
        <?php endif; ?>
    </div>

    <div class="cv-table-wrap mb-4">
        <table class="cv-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= (int)$item['quantity'] ?></td>
                        <td>&#8369;<?= number_format($item['unit_price'], 2) ?></td>
                        <td>&#8369;<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0 small"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($order['shipping_address']) ?></p>
        <p class="fw-bold mb-0" style="color:#0E7490;font-size:1.1rem;">
            Total Paid: &#8369;<?= number_format($order['total_amount'], 2) ?>
        </p>
    </div>

    <div class="text-center">
        <a href="store.php" class="btn-cv" style="padding:12px 28px;font-size:1rem;">
            <i class="bi bi-shop"></i> Continue Shopping
        </a>
    </div>

</div>

<?php mysqli_close($conn); require('include/footer.php'); ?>
