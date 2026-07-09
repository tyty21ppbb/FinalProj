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
        "name" => "J*** S******",
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

<div class="cv-page-head">
    <div class="cv-page-head-inner">
        <h1>About ChairHive</h1>
        <p>Premium seating solutions designed for comfort, productivity, and style.</p>
    </div>
</div>

<section class="container py-5">

    <!-- About -->
    <div class="row align-items-center g-5">

        <div class="col-lg-6">

            <span class="cv-section-label">ABOUT US</span>

            <h2 class="fw-bold mb-4">
                We Believe Great Work Starts With A Great Chair.
            </h2>

            <p class="text-secondary mb-3">
                ChairHive specializes in ergonomic, executive, gaming,
                visitor, and drafting chairs carefully selected for
                comfort, quality, and long-term durability.
            </p>

            <p class="text-secondary">
                Our goal is to provide seating solutions that improve
                productivity, support proper posture, and enhance every
                workspace.
            </p>

        </div>

        <div class="col-lg-6">

            <div class="cv-card p-4">

                <h4 class="mb-4">Why Choose ChairHive?</h4>

                <div class="row text-center">

                    <div class="col-6 mb-4">
                        <h2 class="text-primary fw-bold">5</h2>
                        <p>Chair Categories</p>
                    </div>

                    <div class="col-6 mb-4">
                        <h2 class="text-primary fw-bold">100%</h2>
                        <p>Quality Checked</p>
                    </div>

                    <div class="col-6">
                        <h2 class="text-primary fw-bold">4</h2>
                        <p>Team Members</p>
                    </div>

                    <div class="col-6">
                        <h2 class="text-primary fw-bold">24/7</h2>
                        <p>Project Support</p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<section class="container py-5">

    <div class="text-center mb-5">

        <span class="cv-section-label">OUR TEAM</span>

        <h2 class="fw-bold">Meet CyberVision</h2>

        <p class="text-secondary">
            The passionate developers behind the ChairHive project.
        </p>

    </div>

    <div class="row g-4 justify-content-center">

        <?php foreach($groupMembers as $member):

            $parts = explode(" ", $member['name']);

            $initials = "";

            foreach($parts as $p){
                $initials .= strtoupper(substr($p,0,1));
            }

        ?>

        <div class="col-lg-3 col-md-6">

            <div class="cv-card text-center team-card p-4 h-100">

                <div class="team-avatar">

                    <img src="<?= htmlspecialchars($member['image']) ?>" alt="<?= htmlspecialchars($member['name']) ?>">

                </div>

                <h5 class="fw-bold mt-4">

                    <?= htmlspecialchars($member['name']) ?>

                </h5>

                <div class="text-primary fw-semibold mb-3">

                    <?= htmlspecialchars($member['role']) ?>

                </div>

                <p class="text-secondary small">

                    <?= htmlspecialchars($member['description']) ?>

                </p>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</section>

<?php require("include/footer.php"); ?>