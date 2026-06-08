<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();

$error = '';
$success = '';
$editTour = null;

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tours WHERE tour_id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $success = 'Tour deleted.';
    } else {
        $error = 'Cannot delete tour (may have bookings).';
    }
}

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM tours WHERE tour_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editTour = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tourId = (int) ($_POST['tour_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $duration = trim($_POST['duration'] ?? '');
    $shortDesc = trim($_POST['short_description'] ?? '');
    $fullDesc = trim($_POST['full_description'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $image = '';
    if ($tourId > 0) {
        $stmt = $conn->prepare("SELECT image FROM tours WHERE tour_id = ?");
        $stmt->bind_param('i', $tourId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $image = $row['image'] ?? '';
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $newFileName = 'tour_' . time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
        $uploadFileDir = APP_ROOT . '/assets/images/';
        
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        $dest_path = $uploadFileDir . $newFileName;
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExtension, $allowedExtensions, true)) {
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image = $newFileName;
            } else {
                $error = 'There was an error moving the uploaded tour image.';
            }
        } else {
            $error = 'Upload failed. Allowed formats: JPG, JPEG, PNG, GIF, WEBP.';
        }
    }

    if ($title === '' || $price <= 0) {
        $error = 'Title and valid price are required.';
    } elseif (!in_array($status, ['active', 'inactive'], true)) {
        $error = 'Invalid status.';
    } elseif (empty($error)) {
        if ($tourId > 0) {
            $stmt = $conn->prepare("UPDATE tours SET title=?, location=?, price=?, duration=?, image=?, short_description=?, full_description=?, status=? WHERE tour_id=?");
            $stmt->bind_param('ssdsssssi', $title, $location, $price, $duration, $image, $shortDesc, $fullDesc, $status, $tourId);
            $stmt->execute() ? $success = 'Tour updated.' : $error = 'Update failed.';
            $editTour = null;
        } else {
            $stmt = $conn->prepare("INSERT INTO tours (title, location, price, duration, image, short_description, full_description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssdsssss', $title, $location, $price, $duration, $image, $shortDesc, $fullDesc, $status);
            $stmt->execute() ? $success = 'Tour created.' : $error = 'Create failed.';
        }
    }
}

$tours = $conn->query("SELECT * FROM tours ORDER BY tour_id DESC");

$pageTitle = 'Manage Tours - TravelKH';
$extraCss = 'assets/css/auth.css';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
include __DIR__ . '/../includes/admin_sidebar.php';
?>

<h2 class="mb-4">Manage Tours</h2>

<?php if ($error): ?><div class="alert alert-danger auth-alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success auth-alert"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

<div class="crud-card mb-4">
  <h4><?php echo $editTour ? 'Edit Tour' : 'Add New Tour'; ?></h4>
  <form method="post" class="auth-form row g-3" enctype="multipart/form-data">
    <input type="hidden" name="tour_id" value="<?php echo (int) ($editTour['tour_id'] ?? 0); ?>">
    <div class="col-md-6">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($editTour['title'] ?? ''); ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Location</label>
      <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($editTour['location'] ?? 'Cambodia'); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Price ($)</label>
      <input type="number" step="0.01" min="0" name="price" class="form-control" required value="<?php echo htmlspecialchars($editTour['price'] ?? ''); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Duration</label>
      <input type="text" name="duration" class="form-control" placeholder="e.g. 2 Days 1 Night" value="<?php echo htmlspecialchars($editTour['duration'] ?? ''); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="active" <?php echo ($editTour['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>active</option>
        <option value="inactive" <?php echo ($editTour['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>inactive</option>
      </select>
    </div>
    <div class="col-12">
      <label class="form-label">Tour Image</label>
      <input type="file" name="image" class="form-control" accept="image/*">
      <?php if (!empty($editTour['image'])): ?>
        <small class="text-muted mt-1 d-block">Current Image: <code><?php echo htmlspecialchars($editTour['image']); ?></code></small>
      <?php endif; ?>
    </div>
    <div class="col-12">
      <label class="form-label">Short Description</label>
      <textarea name="short_description" class="form-control" rows="2"><?php echo htmlspecialchars($editTour['short_description'] ?? ''); ?></textarea>
    </div>
    <div class="col-12">
      <label class="form-label">Full Description</label>
      <textarea name="full_description" class="form-control" rows="4"><?php echo htmlspecialchars($editTour['full_description'] ?? ''); ?></textarea>
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-green"><?php echo $editTour ? 'Update Tour' : 'Create Tour'; ?></button>
      <?php if ($editTour): ?>
        <a href="<?php echo base_url('admin/tours.php'); ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<div class="crud-card">
  <div class="table-responsive">
    <table class="table crud-table align-middle mb-0">
      <thead>
        <tr><th>ID</th><th>Title</th><th>Location</th><th>Price</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while ($t = $tours->fetch_assoc()): ?>
        <tr>
          <td><?php echo (int) $t['tour_id']; ?></td>
          <td><?php echo htmlspecialchars($t['title']); ?></td>
          <td><?php echo htmlspecialchars($t['location']); ?></td>
          <td>$<?php echo number_format($t['price'], 2); ?></td>
          <td><?php echo htmlspecialchars($t['status']); ?></td>
          <td>
            <a href="?edit=<?php echo (int) $t['tour_id']; ?>" class="btn btn-sm btn-outline-primary btn-action">Edit</a>
            <a href="?delete=<?php echo (int) $t['tour_id']; ?>" class="btn btn-sm btn-outline-danger btn-action"
               onclick="return confirm('Delete this tour?');">Delete</a>
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
