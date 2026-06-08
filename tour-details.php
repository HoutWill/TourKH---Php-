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
                <div class="booking-card-modern">
                    <div class="card-badge">Top Choice</div>
                    
                    <div class="price-header">
                        <span class="price-label">Price per person</span>
                        <h2 class="tour-price-modern">$<?php echo number_format($t['price'], 2); ?></h2>
                    </div>
                    
                    <hr class="divider">
                    
                    <ul class="tour-features-list">
                        <li>
                            <span class="feature-icon">📍</span>
                            <div class="feature-text">
                                <span class="feature-title">Location</span>
                                <span class="feature-val"><?php echo htmlspecialchars($t['location']); ?></span>
                            </div>
                        </li>
                        <li>
                            <span class="feature-icon">⏱️</span>
                            <div class="feature-text">
                                <span class="feature-title">Duration</span>
                                <span class="feature-val"><?php echo htmlspecialchars($t['duration']); ?></span>
                            </div>
                        </li>
                    </ul>
                    
                    <hr class="divider">
                    
                    <div class="guarantees-box">
                        <div class="guarantee-item">
                            <span class="guarantee-check">✓</span> Instant Confirmation
                        </div>
                        <div class="guarantee-item">
                            <span class="guarantee-check">✓</span> Best Price Guarantee
                        </div>
                        <div class="guarantee-item">
                            <span class="guarantee-check">✓</span> Secure Booking
                        </div>
                    </div>
                    
                    <a href="<?php echo base_url('user/book.php?id=' . $t['tour_id']); ?>" class="btn-book-now-modern">
                        <span>✈️ Book Tour Now</span>
                        <span class="arrow-icon">→</span>
                    </a>
                    
                    <p class="booking-footer-text">No booking fees • Cancel anytime</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
