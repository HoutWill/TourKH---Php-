<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();

$stats = [
    'tours'    => $conn->query("SELECT COUNT(*) AS c FROM tours")->fetch_assoc()['c'],
    'users'    => $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'],
    'bookings' => $conn->query("SELECT COUNT(*) AS c FROM bookings")->fetch_assoc()['c'],
    'pending'  => $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE status='pending'")->fetch_assoc()['c'],
];

$recent = $conn->query("
    SELECT b.booking_id, b.status, b.created_at, u.username, t.title
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN tours t ON b.tour_id = t.tour_id
    ORDER BY b.created_at DESC LIMIT 5
");

$pageTitle = 'Admin Dashboard - TravelKH';
$extraCss = 'assets/css/auth.css';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
include __DIR__ . '/../includes/admin_sidebar.php';
?>

<h2 class="mb-4">Dashboard</h2>
<p class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>

<div class="stat-grid">
  <div class="stat-box"><div class="num"><?php echo (int) $stats['tours']; ?></div><div class="lbl">Tours</div></div>
  <div class="stat-box"><div class="num"><?php echo (int) $stats['users']; ?></div><div class="lbl">Users</div></div>
  <div class="stat-box"><div class="num"><?php echo (int) $stats['bookings']; ?></div><div class="lbl">Bookings</div></div>
  <div class="stat-box"><div class="num"><?php echo (int) $stats['pending']; ?></div><div class="lbl">Pending</div></div>
</div>

<div class="dashboard-card">
  <h4 class="mb-3">Recent Bookings</h4>
  <?php if ($recent->num_rows === 0): ?>
    <p class="text-muted mb-0">No bookings yet.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table crud-table mb-0">
        <thead><tr><th>ID</th><th>User</th><th>Tour</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
          <?php while ($r = $recent->fetch_assoc()): ?>
          <tr>
            <td>#<?php echo (int) $r['booking_id']; ?></td>
            <td><?php echo htmlspecialchars($r['username']); ?></td>
            <td><?php echo htmlspecialchars($r['title']); ?></td>
            <td><?php echo htmlspecialchars($r['status']); ?></td>
            <td><?php echo date('M j, Y', strtotime($r['created_at'])); ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php
include __DIR__ . '/../includes/admin_footer.php';
include __DIR__ . '/../includes/footer.php';
