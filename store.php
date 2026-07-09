<?php
session_start();
require_once('db.php');

$title       = "ChairHive - Store";
$currentPage = "store";

$sql    = "SELECT * FROM products WHERE is_active = 1 ORDER BY category, name";
$result = mysqli_query($conn, $sql);

$grouped = array();
while ($row = mysqli_fetch_assoc($result)) {
    $grouped[$row['category']][] = $row;
}

$catIcons = array(
    'Ergonomic Chairs'         => 'bi-person-workspace',
    'Executive Chairs'         => 'bi-briefcase',
    'Gaming Chairs'            => 'bi-controller',
    'Visitor & Guest Chairs'   => 'bi-people',
    'Stools & Drafting Chairs' => 'bi-easel',
);

$justAdded = isset($_GET['added']) ? $_GET['added'] : null;

mysqli_close($conn);

require('include/header.php');
?>

<div class="cv-page-head">
    <div class="cv-page-head-inner">
        <h1><i class="bi bi-shop"></i> Store</h1>
        <p>All chair types, organized by category. Add any chair to your cart.</p>
    </div>
</div>

<div style="max-width:1200px;margin:0 auto;padding:40px 40px 60px;">

    <?php if ($justAdded): ?>
        <div class="cv-alert cv-alert-success mb-4">
            <i class="bi bi-cart-check"></i>
            <strong>"<?= htmlspecialchars($justAdded) ?>"</strong> was added to your cart.
            <a href="cart.php" style="color:#166534;font-weight:600;">View Cart &rarr;</a>
        </div>
    <?php endif; ?>

    <div class="cv-toolbar">
        <div class="cv-cat-tabs">
            <button type="button" class="cv-cat-tab active" data-target="all">All</button>
            <?php foreach ($grouped as $categoryName => $items): ?>
                <button type="button" class="cv-cat-tab" data-target="<?= htmlspecialchars(urlencode($categoryName)) ?>">
                    <?= htmlspecialchars($categoryName) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="cv-sort-bar">
            <label for="cv-sort-select">Sort by</label>
            <select id="cv-sort-select">
                <option value="default">Default</option>
                <option value="price-asc">Price: Low to High</option>
                <option value="price-desc">Price: High to Low</option>
                <option value="name-asc">Name: A to Z</option>
                <option value="name-desc">Name: Z to A</option>
            </select>
        </div>
    </div>

    <?php foreach ($grouped as $categoryName => $items): ?>
        <div class="mb-5 cv-cat-section" id="<?= urlencode($categoryName) ?>" data-category="<?= htmlspecialchars(urlencode($categoryName)) ?>">
            <div class="cv-cat-heading">
                <?php $icon = isset($catIcons[$categoryName]) ? $catIcons[$categoryName] : 'bi-person-workspace'; ?>
                <div class="cv-category-icon" style="width:36px;height:36px;font-size:1rem;">
                    <i class="bi <?= $icon ?>"></i>
                </div>
                <h4><?= htmlspecialchars($categoryName) ?></h4>
            </div>

            <div class="cv-product-grid">
                <?php foreach ($items as $p):
                    $outOfStock = (int)$p['stock_qty'] <= 0;
                ?>
                    <div class="cv-product-card" data-price="<?= (float)$p['price'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>">
                        <?php $icon = isset($catIcons[$categoryName]) ? $catIcons[$categoryName] : 'bi-person-workspace'; ?>
                        <a href="product.php?id=<?= (int)$p['id'] ?>" class="cv-product-link">
                            <div class="cv-product-thumb">
                                <?php if (!empty($p['image'])): ?>
                                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <i class="bi <?= $icon ?>" style="display:none;"></i>
                                <?php else: ?>
                                    <i class="bi <?= $icon ?>"></i>
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="cv-product-body">
                            <div class="cv-product-cat"><?= htmlspecialchars($p['category']) ?></div>
                            <a href="product.php?id=<?= (int)$p['id'] ?>" class="cv-product-link">
                                <div class="cv-product-name"><?= htmlspecialchars($p['name']) ?></div>
                            </a>
                            <div class="cv-product-desc"><?= htmlspecialchars($p['description']) ?></div>

                            <?php if ($outOfStock): ?>
                                <span class="cv-badge-out">Out of Stock</span>
                            <?php elseif ((int)$p['stock_qty'] <= 5): ?>
                                <span class="cv-badge-low">Only <?= (int)$p['stock_qty'] ?> left</span>
                            <?php endif; ?>

                            <div class="cv-product-footer">
                                <span class="cv-product-price">&#8369;<?= number_format($p['price'], 2) ?></span>
                                <form action="cart_action.php" method="post">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                    <input type="hidden" name="redirect" value="store.php#<?= urlencode($categoryName) ?>">
                                    <button type="submit" name="submit" class="btn-cv" <?= $outOfStock ? 'disabled' : '' ?>>
                                        <?php if ($outOfStock): ?>
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
    <?php endforeach; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var tabs = document.querySelectorAll('.cv-cat-tab');
    var sections = document.querySelectorAll('.cv-cat-section');
    var sortSelect = document.getElementById('cv-sort-select');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');

            var target = tab.getAttribute('data-target');
            sections.forEach(function (section) {
                if (target === 'all' || section.getAttribute('data-category') === target) {
                    section.style.display = '';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    });

    sortSelect.addEventListener('change', function () {
        var value = sortSelect.value;

        sections.forEach(function (section) {
            var grid = section.querySelector('.cv-product-grid');
            var cards = Array.prototype.slice.call(grid.querySelectorAll('.cv-product-card'));

            cards.sort(function (a, b) {
                if (value === 'price-asc') {
                    return parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
                }
                if (value === 'price-desc') {
                    return parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price'));
                }
                if (value === 'name-asc') {
                    return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                }
                if (value === 'name-desc') {
                    return b.getAttribute('data-name').localeCompare(a.getAttribute('data-name'));
                }
                return 0;
            });

            if (value === 'default') {
                cards.sort(function (a, b) {
                    return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                });
            }

            cards.forEach(function (card) {
                grid.appendChild(card);
            });
        });
    });
});
</script>

<?php require('include/footer.php'); ?>