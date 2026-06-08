<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Packages - TravelKH';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1>Packages</h1>
        <p class="lead text-muted">Choose from our most popular tour packages for culture, adventure, and relaxation.</p>
    </div>

    <div class="tour-grid">
        <div class="tour-item">
            <div class="tour-card p-3">
                <img src="<?php echo getTourImage('phnompenh.jpg'); ?>" alt="Phnom Penh city tour">
                <h4>City Explorer</h4>
                <p>Discover Phnom Penh's top sights, historical landmarks, and local flavors with a guided city tour.</p>
                <div class="price">From $49 / person</div>
            </div>
        </div>
        <div class="tour-item">
            <div class="tour-card p-3">
                <img src="<?php echo getTourImage('cardamom.jpg'); ?>" alt="Cardamom Mountains nature tour">
                <h4>Nature Escape</h4>
                <p>Relax in countryside settings, waterfalls, and green landscapes with a peaceful nature retreat.</p>
                <div class="price">From $79 / person</div>
            </div>
        </div>
        <div class="tour-item">
            <div class="tour-card p-3">
                <img src="<?php echo getTourImage('angkor.jpg'); ?>" alt="Angkor temple cultural tour">
                <h4>Cultural Highlights</h4>
                <p>Visit Angkor temples, local markets, and cultural sites with an experienced Khmer guide.</p>
                <div class="price">From $89 / person</div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
