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

$categoryNames = array_keys($grouped);
$firstCategory = isset($categoryNames[0]) ? $categoryNames[0] : null;

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

<div class="cv-store-wrap">

    <?php if ($justAdded): ?>
        <div class="cv-alert cv-alert-success mb-4">
            <i class="bi bi-cart-check"></i>
            <strong>"<?= htmlspecialchars($justAdded) ?>"</strong> was added to your cart.
            <a href="cart.php" style="color:#166534;font-weight:600;">View Cart &rarr;</a>
        </div>
    <?php endif; ?>

    <div class="cv-store-layout">

        <!-- Sidebar category nav -->
        <aside class="cv-store-sidebar">
            <div class="cv-sidebar-label">Categories</div>
            <nav class="cv-sidebar-nav">
                <?php foreach ($grouped as $categoryName => $items):
                    $icon    = isset($catIcons[$categoryName]) ? $catIcons[$categoryName] : 'bi-person-workspace';
                    $isFirst = ($categoryName === $firstCategory);
                ?>
                    <button type="button"
                            class="cv-sidebar-item<?= $isFirst ? ' active' : '' ?>"
                            data-target="<?= htmlspecialchars(urlencode($categoryName)) ?>">
                        <span class="cv-sidebar-icon"><i class="bi <?= $icon ?>"></i></span>
                        <span class="cv-sidebar-text"><?= htmlspecialchars($categoryName) ?></span>
                        <span class="cv-sidebar-count"><?= count($items) ?></span>
                    </button>
                <?php endforeach; ?>
            </nav>
        </aside>

        <!-- Main content: one category at a time -->
        <div class="cv-store-main">

            <div class="cv-content-toolbar">
                <div class="cv-content-heading">
                    <?php $icon = isset($catIcons[$firstCategory]) ? $catIcons[$firstCategory] : 'bi-person-workspace'; ?>
                    <div class="cv-category-icon">
                        <i class="bi <?= $icon ?>" id="cv-active-cat-icon"></i>
                    </div>
                    <div>
                        <h4 id="cv-active-cat-name"><?= htmlspecialchars($firstCategory) ?></h4>
                        <span class="cv-content-count" id="cv-active-cat-count">
                            <?= count($grouped[$firstCategory]) ?> items
                        </span>
                    </div>
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

            <?php foreach ($grouped as $categoryName => $items):
                $isFirst = ($categoryName === $firstCategory);
            ?>
                <div class="cv-cat-section<?= $isFirst ? ' active' : '' ?>"
                     data-category="<?= htmlspecialchars(urlencode($categoryName)) ?>"
                     data-cat-name="<?= htmlspecialchars($categoryName) ?>"
                     data-cat-icon="<?= isset($catIcons[$categoryName]) ? $catIcons[$categoryName] : 'bi-person-workspace' ?>">

                    <div class="cv-product-grid">
                        <?php foreach ($items as $p):
                            $outOfStock = (int)$p['stock_qty'] <= 0;
                            $icon = isset($catIcons[$categoryName]) ? $catIcons[$categoryName] : 'bi-person-workspace';
                        ?>
                            <div class="cv-product-card" data-price="<?= (float)$p['price'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>">
                                <a href="product.php?id=<?= (int)$p['id'] ?>" class="cv-product-link">
                                    <div class="cv-product-thumb">
                                        <?php if (!empty($p['image'])): ?>
                                            <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                            <i class="bi <?= $icon ?>" style="display:none;"></i>
                                        <?php else: ?>
                                            <i class="bi <?= $icon ?>"></i>
                                        <?php endif; ?>

                                        <?php if ($outOfStock): ?>
                                            <span class="cv-badge-out cv-badge-floating">Out of Stock</span>
                                        <?php elseif ((int)$p['stock_qty'] <= 5): ?>
                                            <span class="cv-badge-low cv-badge-floating">Only <?= (int)$p['stock_qty'] ?> left</span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <div class="cv-product-body">
                                    <a href="product.php?id=<?= (int)$p['id'] ?>" class="cv-product-link">
                                        <div class="cv-product-name"><?= htmlspecialchars($p['name']) ?></div>
                                    </a>
                                    <div class="cv-product-desc"><?= htmlspecialchars($p['description']) ?></div>

                                    <div class="cv-product-footer">
                                        <span class="cv-product-price">&#8369;<?= number_format($p['price'], 2) ?></span>
                                        <form action="cart_action.php" method="post">
                                            <input type="hidden" name="action" value="add">
                                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                            <input type="hidden" name="redirect" value="store.php">
                                            <button type="submit" name="submit" class="btn-cv" <?= $outOfStock ? 'disabled' : '' ?>>
                                                <?php if ($outOfStock): ?>
                                                    Unavailable
                                                <?php else: ?>
                                                    <i class="bi bi-cart-plus"></i> Add
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
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var sidebarItems = document.querySelectorAll('.cv-sidebar-item');
    var sections     = document.querySelectorAll('.cv-cat-section');
    var sortSelect   = document.getElementById('cv-sort-select');
    var activeIcon   = document.getElementById('cv-active-cat-icon');
    var activeName   = document.getElementById('cv-active-cat-name');
    var activeCount  = document.getElementById('cv-active-cat-count');

    function countVisible(section) {
        return section.querySelectorAll('.cv-product-card').length;
    }

    sidebarItems.forEach(function (item) {
        item.addEventListener('click', function () {
            sidebarItems.forEach(function (i) { i.classList.remove('active'); });
            item.classList.add('active');

            var target = item.getAttribute('data-target');
            var iconClass = item.querySelector('.cv-sidebar-icon i').className;
            var text = item.querySelector('.cv-sidebar-text').textContent;

            sections.forEach(function (section) {
                if (section.getAttribute('data-category') === target) {
                    section.classList.add('active');
                    activeIcon.className = iconClass;
                    activeName.textContent = text;
                    activeCount.textContent = countVisible(section) + ' items';
                } else {
                    section.classList.remove('active');
                }
            });

            document.querySelector('.cv-store-main').scrollTo({ top: 0, behavior: 'smooth' });
            window.scrollTo({ top: document.querySelector('.cv-store-layout').offsetTop - 20, behavior: 'smooth' });
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