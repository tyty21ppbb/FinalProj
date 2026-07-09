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

<!-- Internal CSS to override layout compressing/stretching bugs -->
<style>
    .cv-store-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px 60px;
        width: 100%;
        box-sizing: border-box;
    }

    /* Fixed Layout Toolbar alignment */
    .cv-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 30px;
        background: #f8fafc;
        padding: 15px;
        border-radius: 8px;
    }

    .cv-cat-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .cv-cat-tab {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s;
    }

    .cv-cat-tab.active, .cv-cat-tab:hover {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }

    /* Responsive Grid Engine */
    .cv-product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 25px;
        width: 100%;
        margin-top: 15px;
    }

    /* Uniform Height Cards */
    .cv-product-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .cv-product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    /* Uniform Aspect Box for product pictures */
    .cv-product-thumb {
        width: 100%;
        height: 250px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
        box-sizing: border-box;
    }

    .cv-product-thumb img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain; /* Prevents stretching */
    }

    .cv-product-body {
        padding: 15px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .cv-product-cat {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .cv-product-name {
        font-size: 1.05rem;
        font-weight: 600;
        color: #1e293b;
        text-decoration: none;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .cv-product-desc {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 15px;
        line-height: 1.5;
        flex-grow: 1; /* Pushes content down nicely */
    }

    /* Keep footer row item alignment level */
    .cv-product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }

    .cv-product-price {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
    }

    .btn-cv {
        background: #0f172a;
        color: #ffffff;
        border: none;
        padding: 8px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-cv:disabled {
        background: #cbd5e1;
        color: #64748b;
        cursor: not-allowed;
    }
</style>

<div class="cv-page-head">
    <div class="cv-page-head-inner">
        <h1><i class="bi bi-shop"></i> Store</h1>
        <p>All chair types, organized by category. Add any chair to your cart.</p>
    </div>
</div>

<div class="cv-store-container">

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
            <label for="cv-sort-select">Sort by </label>
            <select id="cv-sort-select" style="padding:6px; border-radius:4px; border:1px solid #cbd5e1;">
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
            <div class="cv-cat-heading" style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                <?php $icon = isset($catIcons[$categoryName]) ? $catIcons[$catIcons[$categoryName]] : 'bi-person-workspace'; ?>
                <div class="cv-category-icon" style="width:36px; height:36px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.1rem; color:#475569;">
                    <i class="bi <?= isset($catIcons[$categoryName]) ? $catIcons[$categoryName] : 'bi-person-workspace' ?>"></i>
                </div>
                <h4 style="margin:0; font-weight:600; color:#1e293b;"><?= htmlspecialchars($categoryName) ?></h4>
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
                                    <i class="bi <?= $icon ?>" style="display:none; font-size:3rem; color:#cbd5e1;"></i>
                                <?php else: ?>
                                    <i class="bi <?= $icon ?>" style="font-size:3rem; color:#cbd5e1;"></i>
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="cv-product-body">
                            <div class="cv-product-cat"><?= htmlspecialchars($p['category']) ?></div>
                            <a href="product.php?id=<?= (int)$p['id'] ?>" style="text-decoration:none;">
                                <div class="cv-product-name"><?= htmlspecialchars($p['name']) ?></div>
                            </a>
                            <div class="cv-product-desc"><?= htmlspecialchars($p['description']) ?></div>

                            <div style="margin-bottom:12px;">
                                <?php if ($outOfStock): ?>
                                    <span style="background:#fee2e2; color:#991b1b; padding:3px 8px; border-radius:4px; font-size:0.75rem; font-weight:600;">Out of Stock</span>
                                <?php elseif ((int)$p['stock_qty'] <= 5): ?>
                                    <span style="background:#fef3c7; color:#92400e; padding:3px 8px; border-radius:4px; font-size:0.75rem; font-weight:600;">Only <?= (int)$p['stock_qty'] ?> left</span>
                                <?php endif; ?>
                            </div>

                            <div class="cv-product-footer">
                                <span class="cv-product-price">&#8369;<?= number_format($p['price'], 2) ?></span>
                                <form action="cart_action.php" method="post" style="margin:0;">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                    <input type="hidden" name="redirect" value="store.php#<?= urlencode($categoryName) ?>">
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
                    return parseFloat(b.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
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
