<?php
session_start();
require_once('db.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql  = "SELECT * FROM products WHERE id = ? AND is_active = 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$product) {
    mysqli_close($conn);
    header("Location: store.php");
    exit;
}

$title       = "ChairHive - " . $product['name'];
$currentPage = "store";

$catIcons = array(
    'Ergonomic Chairs'         => 'bi-person-workspace',
    'Executive Chairs'         => 'bi-briefcase',
    'Gaming Chairs'            => 'bi-controller',
    'Visitor & Guest Chairs'   => 'bi-people',
    'Stools & Drafting Chairs' => 'bi-easel',
);
$icon = isset($catIcons[$product['category']]) ? $catIcons[$product['category']] : 'bi-person-workspace';

$relatedSql  = "SELECT * FROM products WHERE category = ? AND id != ? AND is_active = 1 LIMIT 4";
$relatedStmt = mysqli_prepare($conn, $relatedSql);
mysqli_stmt_bind_param($relatedStmt, "si", $product['category'], $id);
mysqli_stmt_execute($relatedStmt);
$relatedResult = mysqli_stmt_get_result($relatedStmt);
$related = array();
while ($row = mysqli_fetch_assoc($relatedResult)) {
    $related[] = $row;
}
mysqli_stmt_close($relatedStmt);

mysqli_close($conn);

$outOfStock = (int)$product['stock_qty'] <= 0;

require('include/header.php');
?>

<div style="max-width:1100px;margin:0 auto;padding:40px 40px 60px;">

    <a href="store.php" class="cv-back-link"><i class="bi bi-arrow-left"></i> Back to Store</a>

    <div class="cv-product-detail">
        <div class="cv-detail-image">
            <?php if (!empty($product['image'])): ?>
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <i class="bi <?= $icon ?>" style="display:none;"></i>
            <?php else: ?>
                <i class="bi <?= $icon ?>"></i>
            <?php endif; ?>
        </div>

        <div class="cv-detail-info">
            <div class="cv-product-cat"><?= htmlspecialchars($product['category']) ?></div>
            <h1 class="cv-detail-name"><?= htmlspecialchars($product['name']) ?></h1>

            <?php if ($outOfStock): ?>
                <span class="cv-badge-out">Out of Stock</span>
            <?php elseif ((int)$product['stock_qty'] <= 5): ?>
                <span class="cv-badge-low">Only <?= (int)$product['stock_qty'] ?> left</span>
            <?php endif; ?>

            <div class="cv-detail-price">&#8369;<?= number_format($product['price'], 2) ?></div>

            <p class="cv-detail-desc"><?= htmlspecialchars($product['description']) ?></p>

            <form action="cart_action.php" method="post">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                <input type="hidden" name="redirect" value="product.php?id=<?= (int)$product['id'] ?>">
                <button type="submit" name="submit" class="btn-cv cv-detail-btn" <?= $outOfStock ? 'disabled' : '' ?>>
                    <?php if ($outOfStock): ?>
                        Unavailable
                    <?php else: ?>
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    <?php endif; ?>
                </button>
            </form>
        </div>
    </div>

    <?php if (!empty($related)): ?>
        <div class="cv-related">
            <h4>More in <?= htmlspecialchars($product['category']) ?></h4>
            <div class="cv-product-grid">
                <?php foreach ($related as $r): $rOutOfStock = (int)$r['stock_qty'] <= 0; ?>
                    <div class="cv-product-card">
                        <a href="product.php?id=<?= (int)$r['id'] ?>" class="cv-product-link">
                            <div class="cv-product-thumb">
                                <?php if (!empty($r['image'])): ?>
                                    <img src="<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['name']) ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <i class="bi <?= $icon ?>" style="display:none;"></i>
                                <?php else: ?>
                                    <i class="bi <?= $icon ?>"></i>
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="cv-product-body">
                            <div class="cv-product-cat"><?= htmlspecialchars($r['category']) ?></div>
                            <a href="product.php?id=<?= (int)$r['id'] ?>" class="cv-product-link">
                                <div class="cv-product-name"><?= htmlspecialchars($r['name']) ?></div>
                            </a>
                            <div class="cv-product-desc"><?= htmlspecialchars($r['description']) ?></div>
                            <div class="cv-product-footer">
                                <span class="cv-product-price">&#8369;<?= number_format($r['price'], 2) ?></span>
                                <form action="cart_action.php" method="post">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                    <input type="hidden" name="redirect" value="product.php?id=<?= (int)$product['id'] ?>">
                                    <button type="submit" name="submit" class="btn-cv" <?= $rOutOfStock ? 'disabled' : '' ?>>
                                        <?php if ($rOutOfStock): ?>
                                            Unavailable
                                        <?php else: ?>
                                            <i class="bi bi-cart-plus"></i> Add to Cart
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php require('include/footer.php'); ?>