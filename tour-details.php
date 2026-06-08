<?php
require_once __DIR__ . '/includes/config.php';

$tourId = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM tours WHERE tour_id = ? AND status = 'active'");
$stmt->bind_param('i', $tourId);
$stmt->execute();
$t = $stmt->get_result()->fetch_assoc();

if (!$t) {
    redirect('tours.php');
}

$pageTitle = htmlspecialchars($t['title']) . ' - TravelKH';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container mt-5">
    <div data-aos="fade-up">
        <div class="text-center mb-4">
            <h1 class="tour-title">🌄 <?php echo htmlspecialchars($t['title']); ?></h1>
            <p class="text-muted fs-5">📍 <?php echo htmlspecialchars($t['location']); ?></p>
        </div>

        <img src="<?php echo getTourImage($t['image']); ?>"
             class="w-100 mb-4 shadow-sm"
             style="border-radius:20px; max-height:600px; object-fit:cover;"
             alt="<?php echo htmlspecialchars($t['title']); ?>">

        <div class="row">
            <div class="col-lg-8">
                <div class="tour-description">
                    <h3 class="section-title">🌍 Tour Description</h3>
                    <?php echo nl2br(htmlspecialchars($t['full_description'])); ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="tour-card booking-card">
                    <h2 class="tour-price">💰 $<?php echo number_format($t['price'], 2); ?></h2>
                    <hr>
                    <p>📍 <?php echo htmlspecialchars($t['location']); ?></p>
                    <p>⏱️ <?php echo htmlspecialchars($t['duration']); ?></p>
                    <a href="<?php echo base_url('user/book.php?id=' . $t['tour_id']); ?>" class="btn btn-green w-100">
                        ✈️ Book Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
