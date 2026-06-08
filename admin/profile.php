<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();

$userId = current_user_id();
$user = get_user_by_id($conn, $userId);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!verify_password_and_upgrade($conn, $current, ['user_id' => $userId, 'password' => $row['password']])) {
            $error = 'Current password is incorrect.';
        } elseif ($err = validate_password($newPass)) {
            $error = $err;
        } elseif ($newPass !== $confirm) {
            $error = 'New passwords do not match.';
        } else {
            $hash = password_hash($newPass, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $upd->bind_param('si', $hash, $userId);
            $upd->execute();
            $success = 'Password updated successfully.';
        }
    }
}

$pageTitle = 'Admin Profile - TravelKH';
$extraCss = 'assets/css/auth.css';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
include __DIR__ . '/../includes/admin_sidebar.php';
?>

<h2 class="mb-4">Admin Profile</h2>

<?php if ($error): ?><div class="alert alert-danger auth-alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success auth-alert"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="profile-card">
      <h4 class="mb-3">Account Details</h4>
      <dl class="profile-meta">
        <dt>Username</dt>
        <dd><?php echo htmlspecialchars($user['username']); ?></dd>
        <dt>Role</dt>
        <dd><span class="badge badge-role-admin rounded-pill px-3 py-2"><?php echo htmlspecialchars($user['role']); ?></span></dd>
        <dt>Status</dt>
        <dd><span class="badge badge-status-<?php echo $user['status']; ?> rounded-pill px-3 py-2"><?php echo htmlspecialchars($user['status']); ?></span></dd>
        <dt>Member Since</dt>
        <dd><?php echo date('M j, Y', strtotime($user['created_at'])); ?></dd>
      </dl>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="profile-card">
      <h4 class="mb-3">Change Password</h4>
      <form method="post" class="auth-form">
        <input type="hidden" name="action" value="change_password">
        <div class="mb-3">
          <label class="form-label">Current Password</label>
          <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">New Password</label>
          <input type="password" name="new_password" class="form-control" required minlength="6">
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control" required minlength="6">
        </div>
        <button type="submit" class="btn btn-green">Update Password</button>
      </form>
    </div>
  </div>
</div>

<?php
include __DIR__ . '/../includes/admin_footer.php';
include __DIR__ . '/../includes/footer.php';
