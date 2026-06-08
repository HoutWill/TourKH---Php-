<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();

$error = '';
$success = '';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute() ? $success = 'Booking deleted.' : $error = 'Delete failed.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    $people = max(1, (int) ($_POST['people'] ?? 1));
    $bookingDate = $_POST['booking_date'] ?? '';
    $totalPrice = (float) ($_POST['total_price'] ?? 0);

    if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
        $error = 'Invalid status.';
    } elseif ($bookingId > 0) {
        $stmt = $conn->prepare("UPDATE bookings SET booking_date=?, people=?, total_price=?, status=? WHERE booking_id=?");
        $stmt->bind_param('sidsi', $bookingDate, $people, $totalPrice, $status, $bookingId);
        $stmt->execute() ? $success = 'Booking updated.' : $error = 'Update failed.';
    }
}

$bookings = $conn->query("
    SELECT b.*, u.username, t.title AS tour_title
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN tours t ON b.tour_id = t.tour_id
    ORDER BY b.booking_id DESC
");

$editBooking = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE booking_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editBooking = $stmt->get_result()->fetch_assoc();
}

$pageTitle = 'Manage Bookings - TravelKH';
$extraCss = 'assets/css/auth.css';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
include __DIR__ . '/../includes/admin_sidebar.php';
?>

<h2 class="mb-4">Manage Bookings</h2>

<?php if ($error): ?><div class="alert alert-danger auth-alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success auth-alert"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

<?php if ($editBooking): ?>
<div class="crud-card mb-4">
  <h4>Edit Booking #<?php echo (int) $editBooking['booking_id']; ?></h4>
  <form method="post" class="auth-form row g-3">
    <input type="hidden" name="booking_id" value="<?php echo (int) $editBooking['booking_id']; ?>">
    <div class="col-md-4">
      <label class="form-label">Booking Date</label>
      <input type="date" name="booking_date" class="form-control" required
             value="<?php echo htmlspecialchars($editBooking['booking_date']); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">People</label>
      <input type="number" name="people" class="form-control" min="1" required
             value="<?php echo (int) $editBooking['people']; ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Total Price ($)</label>
      <input type="number" step="0.01" name="total_price" class="form-control" required
             value="<?php echo htmlspecialchars($editBooking['total_price']); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <?php foreach (['pending', 'approved', 'rejected'] as $s): ?>
        <option value="<?php echo $s; ?>" <?php echo $editBooking['status'] === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-green">Update Booking</button>
      <a href="<?php echo base_url('admin/bookings.php'); ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="crud-card">
  <div class="table-responsive">
    <table class="table crud-table align-middle mb-0">
      <thead>
        <tr><th>ID</th><th>User</th><th>Tour</th><th>Date</th><th>People</th><th>Total</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while ($b = $bookings->fetch_assoc()): ?>
        <tr>
          <td>#<?php echo (int) $b['booking_id']; ?></td>
          <td><?php echo htmlspecialchars($b['username']); ?></td>
          <td><?php echo htmlspecialchars($b['tour_title']); ?></td>
          <td><?php echo $b['booking_date'] ? date('M j, Y', strtotime($b['booking_date'])) : '—'; ?></td>
          <td><?php echo (int) $b['people']; ?></td>
          <td>$<?php echo number_format((float) $b['total_price'], 2); ?></td>
          <td><span class="badge bg-secondary"><?php echo htmlspecialchars($b['status']); ?></span></td>
          <td>
            <a href="?edit=<?php echo (int) $b['booking_id']; ?>" class="btn btn-sm btn-outline-primary btn-action">Edit</a>
            <a href="?delete=<?php echo (int) $b['booking_id']; ?>" class="btn btn-sm btn-outline-danger btn-action"
               onclick="return confirm('Delete this booking?');">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
include __DIR__ . '/../includes/admin_footer.php';
include __DIR__ . '/../includes/footer.php';
