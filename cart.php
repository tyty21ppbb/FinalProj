<?php
session_start();
require_once('db.php');

$title       = "ChairHive - Cart";
$currentPage = "cart";

$cart  = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$items = array();
$total = 0;

if (!empty($cart)) {
    $ids    = implode(',', array_map('intval', array_keys($cart)));
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
    $productsById = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $productsById[$row['id']] = $row;
    }
    foreach ($cart as $id => $qty) {
        if (!isset($productsById[$id])) continue;
        $p        = $productsById[$id];
        $subtotal = $p['price'] * $qty;
        $total   += $subtotal;
        $items[]  = array('product' => $p, 'qty' => $qty, 'subtotal' => $subtotal);
    }
}

mysqli_close($conn);
require('include/header.php');
?>

<div class="cv-page-head">
    <div class="cv-page-head-inner">
        <h1><i class="bi bi-cart3"></i> Your Cart</h1>
        <p>Review your items, update quantities, then proceed to checkout.</p>
    </div>
</div>

<div style="max-width:1200px;margin:0 auto;padding:40px 40px 80px;">

    <?php if (empty($items)): ?>

        <div class="text-center py-5">
            <i class="bi bi-cart-x text-muted" style="font-size:4rem;color:#CBD5E1!important;"></i>
            <h4 class="mt-4 fw-bold" style="color:#64748B;">Your cart is empty</h4>
            <p class="text-muted">Browse our store and add some chairs.</p>
            <a href="store.php" class="btn-cv mt-2">
                <i class="bi bi-shop"></i> Browse the Store
            </a>
        </div>

    <?php else: ?>

        <form action="cart_action.php" method="post">
            <input type="hidden" name="action" value="update_all">

            <div class="cv-table-wrap mb-4">
                <table class="cv-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row):
                            $p = $row['product'];
                        ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($p['name']) ?></td>
                                <td style="color:#64748B;"><?= htmlspecialchars($p['category']) ?></td>
                                <td>&#8369;<?= number_format($p['price'], 2) ?></td>
                                <td>
                                    <input type="number"
                                           name="qty[<?= (int)$p['id'] ?>]"
                                           value="<?= (int)$row['qty'] ?>"
                                           min="0" max="<?= (int)$p['stock_qty'] ?>"
                                           class="cv-qty-input">
                                </td>
                                <td class="fw-bold" style="color:#0E7490;">&#8369;<?= number_format($row['subtotal'], 2) ?></td>
                                <td>
                                    <a href="cart_action.php?action=remove&id=<?= (int)$p['id'] ?>&redirect=cart.php"
                                       class="btn-cv-danger">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <a href="cart_action.php?action=clear" style="color:#DC2626;font-size:0.85rem;">
                    <i class="bi bi-x-circle"></i> Clear entire cart
                </a>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="fw-bold fs-5" style="color:#0E7490;">
                        Total: &#8369;<?= number_format($total, 2) ?>
                    </span>
                    <button type="submit" name="submit" class="btn-cv-outline">
                        <i class="bi bi-arrow-clockwise"></i> Update Cart
                    </button>
                    <?php if (isset($_SESSION['islogged'])): ?>
                        <a href="checkout.php" class="btn-cv">
                            <i class="bi bi-credit-card"></i> Proceed to Checkout
                        </a>
                    <?php else: ?>
                        <a href="login.php?redirect=checkout.php" class="btn-cv">
                            <i class="bi bi-box-arrow-in-right"></i> Log In to Checkout
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

    <?php endif; ?>

</div>

<?php require('include/footer.php'); ?>
