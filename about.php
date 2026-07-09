<?php
session_start();

$title = "ChairHive - About";
$currentPage = "about";

$groupMembers = [
    [
        "name" => "Joschka Atabelo",
        "role" => "Project Manager",
        "description" => "Leads the team, coordinates project tasks, and ensures deadlines are met.",
        "image" => "images/joschka.jpg"
    ],
    [
        "name" => "Art Panulde",
        "role" => "Frontend Developer",
        "description" => "Develops the website interface and creates a responsive user experience.",
        "image" => "images/art.jpg"
    ],
    [
        "name" => "Jose Soriano",
        "role" => "Backend Developer",
        "description" => "Builds the PHP logic, database integration, and system functionality.",
        "image" => "images/jose.jpg"
    ],
    [
        "name" => "Carl Vitalista",
        "role" => "UI / UX Designer",
        "description" => "Designs the overall look of ChairHive and improves usability.",
        "image" => "images/carl.jpg"
    ]
];

require("include/header.php");
?>

<style>
    .team-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 30px;
        width: 100%;
        margin-top: 20px;
    }

    .team-card-wrapper {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        height: 100%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        box-sizing: border-box;
    }

    /* Fixed containment dimensions to prevent layout blow-outs */
    .team-avatar-box {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 20px;
        border: 4px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
    }

    .team-avatar-box img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Keeps image proportions regular instead of stretching */
    }

    .team-avatar-placeholder {
        font-size: 1.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
    }

    .member-desc {
        font-size: 0.875rem;
        color: #64748b;
        line-height: 1.5;
        margin: 0;
    }
</style>

<div class="cv-page-head">
    <div class="cv-page-head-inner">
        <h1>About ChairHive</h1>
        <p>Premium seating solutions designed for comfort, productivity, and style.</p>
    </div>
</div>

<section class="container py-5" style="max-width: 1200px; margin: 0 auto;">

    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <span class="cv-section-label" style="font-weight: 600; color: #0284c7; font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase;">About Us</span>
            <h2 class="fw-bold mb-4" style="color: #0f172a; margin-top: 8px;">
                We Believe Great Work Starts With A Great Chair.
            </h2>
            <p class="text-secondary mb-3" style="line-height: 1.6;">
                ChairHive specializes in ergonomic, executive, gaming,
                visitor, and drafting chairs carefully selected for
                comfort, quality, and long-term durability.
            </p>
            <p class="text-secondary" style="line-height: 1.6;">
                Our goal is to provide seating solutions that improve
                productivity, support proper posture, and enhance every
                workspace.
            </p>
        </div>

        <div class="col-lg-6">
            <div class="cv-card p-4" style="border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff;">
                <h4 class="mb-4" style="font-weight: 600; color: #0f172a;">Why Choose ChairHive?</h4>
                <div class="row text-center">
                    <div class="col-6 mb-4">
                        <h2 class="fw-bold" style="color: #0284c7;">5</h2>
                        <p class="text-muted small mb-0">Chair Categories</p>
                    </div>
                    <div class="col-6 mb-4">
                        <h2 class="fw-bold" style="color: #0284c7;">100%</h2>
                        <p class="text-muted small mb-0">Quality Checked</p>
                    </div>
                    <div class="col-6">
                        <h2 class="fw-bold" style="color: #0284c7;">4</h2>
                        <p class="text-muted small mb-0">Team Members</p>
                    </div>
                    <div class="col-6">
                        <h2 class="fw-bold" style="color: #0284c7;">24/7</h2>
                        <p class="text-muted small mb-0">Project Support</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<section class="container py-5" style="max-width: 1200px; margin: 0 auto; padding-bottom: 80px;">

    <div class="text-center mb-5">
        <span class="cv-section-label" style="font-weight: 600; color: #0284c7; font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase;">Our Team</span>
        <h2 class="fw-bold" style="color: #0f172a; margin-top: 8px;">Meet CyberVision</h2>
        <p class="text-secondary">
            The passionate developers behind the ChairHive project.
        </p>
    </div>

    <div class="team-grid-container">

        <?php foreach($groupMembers as $member):
            $parts = explode(" ", $member['name']);
            $initials = "";
            foreach($parts as $p){
                if(!empty($p)) $initials .= strtoupper(substr($p, 0, 1));
            }
        ?>

        <div>
            <div class="team-card-wrapper">
                <div class="team-avatar-box">
                    <?php if(!empty($member['image']) && file_exists($member['image'])): ?>
                        <img src="<?= htmlspecialchars($member['image']) ?>" alt="<?= htmlspecialchars($member['name']) ?>">
                    <?php else: ?>
                        <span class="team-avatar-placeholder"><?= htmlspecialchars($initials) ?></span>
                    <?php endif; ?>
                </div>

                <h5 class="fw-bold" style="color: #0f172a; margin-bottom: 6px; font-size: 1.15rem;">
                    <?= htmlspecialchars($member['name']) ?>
                </h5>

                <div class="fw-semibold mb-3" style="color: #0284c7; font-size: 0.9rem;">
                    <?= htmlspecialchars($member['role']) ?>
                </div>

                <p class="member-desc">
                    <?= htmlspecialchars($member['description']) ?>
                </p>
            </div>
        </div>

        <?php endforeach; ?>

    </div>
</section>

<?php require("include/footer.php"); ?>
