<?php
session_start();
require_once('db.php');

$title       = "ChairHive - Premium Office Chairs";
$currentPage = "home";

$catIcons = array(
    'Ergonomic Chairs'         => 'bi-person-workspace',
    'Executive Chairs'         => 'bi-briefcase',
    'Gaming Chairs'            => 'bi-controller',
);

$sql    = "SELECT DISTINCT category FROM products WHERE is_active = 1 ORDER BY category";
$result = mysqli_query($conn, $sql);

$categories = array();
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row['category'];
}

$catImages = array();
foreach ($categories as $cat) {
    $imgSql  = "SELECT image FROM products WHERE category = ? AND is_active = 1 AND image IS NOT NULL AND image != '' LIMIT 1";
    $imgStmt = mysqli_prepare($conn, $imgSql);
    mysqli_stmt_bind_param($imgStmt, "s", $cat);
    mysqli_stmt_execute($imgStmt);
    $imgResult = mysqli_stmt_get_result($imgStmt);
    $imgRow    = mysqli_fetch_assoc($imgResult);
    $catImages[$cat] = $imgRow ? $imgRow['image'] : null;
    mysqli_stmt_close($imgStmt);
}

$heroSql    = "SELECT image FROM products WHERE is_active = 1 AND image IS NOT NULL AND image != '' ORDER BY price DESC LIMIT 1";
$heroResult = mysqli_query($conn, $heroSql);
$heroRow    = mysqli_fetch_assoc($heroResult);
$heroImage  = $heroRow ? $heroRow['image'] : null;

$featuredSql    = "SELECT * FROM products WHERE is_active = 1 ORDER BY price DESC LIMIT 4";
$featuredResult = mysqli_query($conn, $featuredSql);
$featured       = array();
while ($row = mysqli_fetch_assoc($featuredResult)) {
    $featured[] = $row;
}

mysqli_close($conn);

require('include/header.php');
?>

<!-- Hero Section -->
<section class="cv-hero">
    <div class="cv-hero-inner">
        <div class="cv-hero-left">
            <span class="cv-hero-eyebrow">Premium Seating Solutions</span>
            <h1>Sit Better.<br>Work Better.</h1>
            <p>ChairHive offers a curated selection of office chairs &mdash; from all-day ergonomic workhorses to executive leather seats and gaming rigs. Find the chair that fits the way you work.</p>
            <div class="cv-hero-btns">
                <a href="store.php" class="btn-hero-primary">
                    Shop Chairs <i class="bi bi-arrow-right"></i>
                </a>
                <a href="about.php" class="btn-hero-outline">
                    About Us
                </a>
            </div>
            <div class="cv-hero-trust">
                <i class="bi bi-star-fill"></i>
                    <span>Trusted by students and staff across the CyberVision project</span>
            </div>
        </div>
        <?php if ($heroImage): ?>
            <div class="cv-hero-image">
                <img src="<?= htmlspecialchars($heroImage) ?>" alt="Featured chair">
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Info Strip -->
<div class="cv-info-strip">
    <div class="cv-info-strip-inner">
        <div class="cv-info-strip-item">
            <h5><i class="bi bi-award"></i> &nbsp;Premium Quality</h5>
            <p>Every chair is selected for comfort, build quality, and durability</p>
        </div>
        <div class="cv-info-strip-item">
            <h5><i class="bi bi-truck"></i> &nbsp;Delivery Available</h5>
            <p>Nationwide delivery for all orders placed through the site</p>
        </div>
        <div class="cv-info-strip-item">
            <h5><i class="bi bi-headset"></i> &nbsp;School Project</h5>
            <p>Built by CyberVision for a Web Development final requirement</p>
        </div>
    </div>
</div>

<!-- Shop by Category -->
<section class="cv-section">
    <div class="cv-section-inner">
        <p class="cv-section-label">Browse</p>
        <h2 class="cv-section-title">Shop by Chair Type</h2>
        <p class="cv-section-sub">Find exactly what you need from our five chair categories.</p>

        <div class="cv-category-tiles">
            <?php foreach ($categories as $cat):
                $icon = isset($catIcons[$cat]) ? $catIcons[$cat] : 'bi-person-workspace';
                $img  = $catImages[$cat];
            ?>
                <a href="store.php#<?= urlencode($cat) ?>" class="cv-category-tile">
                    <div class="cv-category-tile-thumb">
                        <?php if ($img): ?>
                            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($cat) ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <i class="bi <?= $icon ?>" style="display:none;"></i>
                        <?php else: ?>
                            <i class="bi <?= $icon ?>"></i>
                        <?php endif; ?>
                    </div>
                    <div class="cv-category-tile-label">
                        <i class="bi <?= $icon ?>"></i>
                        <span><?= htmlspecialchars($cat) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<?php if (!empty($featured)): ?>
<section class="cv-section cv-section-light">
    <div class="cv-section-inner">
        <p class="cv-section-label">Handpicked</p>
        <h2 class="cv-section-title">Featured Chairs</h2>
        <p class="cv-section-sub">A few standout picks from across our lineup.</p>

        <div class="cv-product-grid">
            <?php foreach ($featured as $p):
                $icon       = isset($catIcons[$p['category']]) ? $catIcons[$p['category']] : 'bi-person-workspace';
                $outOfStock = (int)$p['stock_qty'] <= 0;
            ?>
                <div class="cv-product-card">
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
                        <div class="cv-product-footer">
                            <span class="cv-product-price">&#8369;<?= number_format($p['price'], 2) ?></span>
                            <form action="cart_action.php" method="post">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="redirect" value="index.php">
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

        <div class="cv-section-cta">
            <a href="store.php" class="btn-cv-outline">View Full Store <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- About Strip -->
<section class="cv-section">
    <div class="cv-section-inner">
        <div class="row g-4">
            <div class="col-md-6">
                <p class="cv-section-label">Our Focus</p>
                <h2 class="cv-section-title">One Product.<br>Done Right.</h2>
                <p class="cv-section-sub">We sell chairs and only chairs &mdash; because doing one thing well is better than doing everything halfway. Whether you're outfitting a home office or a 50-person workspace, ChairHive has the seating for it.</p>
                <a href="store.php" class="btn-cv mt-2">Browse the Store <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="col-md-6">
                <p class="cv-section-label">The Team</p>
                <h2 class="cv-section-title">Built by CyberVision</h2>
                <p class="cv-section-sub">This storefront was built by CyberVision as a final project for our Web Development class. Register an account, browse the store, add chairs to your cart, and go through checkout &mdash; the full flow is functional, just not for real purchases.</p>
                <a href="about.php" class="btn-cv-outline mt-2">Meet the Team</a>
            </div>
        </div>
    </div>
</section>

<?php require('include/footer.php'); ?>