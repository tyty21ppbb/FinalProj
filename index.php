<?php
session_start();
require_once('db.php');

$title       = "ChairHive - Premium Office Chairs";
$currentPage = "home";

$sql    = "SELECT DISTINCT category FROM products WHERE is_active = 1 ORDER BY category";
$result = mysqli_query($conn, $sql);

$categories = array();
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row['category'];
}
mysqli_close($conn);

// Category icons
$catIcons = array(
    'Ergonomic Chairs'       => 'bi-person-workspace',
    'Executive Chairs'       => 'bi-briefcase',
    'Gaming Chairs'          => 'bi-controller',
    'Visitor & Guest Chairs' => 'bi-people',
    'Stools & Drafting Chairs' => 'bi-easel',
);

require('include/header.php');
?>

<!-- Hero Section -->
<section class="cv-hero">
    <div class="cv-hero-inner">
        <div class="cv-hero-left">
            <span class="cv-hero-eyebrow">Premium Seating Solutions</span>
            <h1>Sit Better.<br><span>Work Better.</span></h1>
        </div>
        <div class="cv-hero-right">
            <p>ChairHive offers a curated selection of office chairs &mdash; from all-day ergonomic workhorses to executive leather seats and gaming rigs. Find the chair that fits the way you work.</p>
            <div class="cv-hero-btns">
                <a href="store.php" class="btn-hero-primary">
                    Shop Chairs <i class="bi bi-arrow-right"></i>
                </a>
                <a href="about.php" class="btn-hero-outline">
                    About Us
                </a>
            </div>
        </div>
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

        <div class="cv-category-grid">
            <?php foreach ($categories as $cat):
                $icon = isset($catIcons[$cat]) ? $catIcons[$cat] : 'bi-person-workspace';
            ?>
                <a href="store.php#<?= urlencode($cat) ?>" class="cv-category-card">
                    <div class="cv-category-icon">
                        <i class="bi <?= $icon ?>"></i>
                    </div>
                    <span><?= htmlspecialchars($cat) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- About Strip -->
<section class="cv-section cv-section-light">
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
