<?php
require_once __DIR__ . '/../includes/config.php';
require_user();

$userId = current_user_id();

$stmt = $conn->prepare("
    SELECT b.booking_id, b.booking_date, b.people, b.total_price, b.status, b.created_at,
           t.title, t.location, t.duration
    FROM bookings b
    JOIN tours t ON b.tour_id = t.tour_id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->bind_param('i', $userId);
$stmt->execute();
$bookings = $stmt->get_result();

$pageTitle = 'My Bookings - TravelKH';
$extraCss = 'assets/css/auth.css';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">My Bookings</h2>
    <a href="<?php echo base_url('tours.php'); ?>" class="btn btn-green">Browse Tours</a>
  </div>

  <div class="profile-card">
    <?php if ($bookings->num_rows === 0): ?>
      <p class="text-muted mb-0">You have no bookings yet.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table crud-table align-middle mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tour</th>
              <th>Date</th>
              <th>People</th>
              <th>Total</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($b = $bookings->fetch_assoc()): ?>
            <tr>
              <td>#<?php echo (int) $b['booking_id']; ?></td>
              <td>
                <strong><?php echo htmlspecialchars($b['title']); ?></strong><br>
                <small class="text-muted"><?php echo htmlspecialchars($b['location']); ?> · <?php echo htmlspecialchars($b['duration']); ?></small>
              </td>
              <td><?php echo $b['booking_date'] ? date('M j, Y', strtotime($b['booking_date'])) : '—'; ?></td>
              <td><?php echo (int) $b['people']; ?></td>
              <td>$<?php echo number_format((float) $b['total_price'], 2); ?></td>
              <td><span class="badge bg-secondary"><?php echo htmlspecialchars($b['status']); ?></span></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
