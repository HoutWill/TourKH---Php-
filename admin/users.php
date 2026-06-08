<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();

$error = '';
$success = '';
$editUser = null;
$selfId = current_user_id();

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id === $selfId) {
        $error = 'You cannot delete your own account.';
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute() ? $success = 'User deleted.' : $error = 'Cannot delete user (may have bookings).';
    }
}

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT user_id, username, role, status FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editUser = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) ($_POST['user_id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? ROLE_USER;
    $status = $_POST['status'] ?? 'active';

    $userErr = validate_username($username);
    $roleErr = validate_role($role);
    $statusErr = validate_status($status);

    if ($userErr) {
        $error = $userErr;
    } elseif ($roleErr) {
        $error = $roleErr;
    } elseif ($statusErr) {
        $error = $statusErr;
    } elseif ($userId > 0) {
        if ($userId === $selfId && $role !== ROLE_ADMIN) {
            $error = 'You cannot remove your own admin role.';
        } else {
            if ($password !== '') {
                if ($passErr = validate_password($password)) {
                    $error = $passErr;
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET username=?, password=?, role=?, status=? WHERE user_id=?");
                    $stmt->bind_param('ssssi', $username, $hash, $role, $status, $userId);
                }
            } else {
                $stmt = $conn->prepare("UPDATE users SET username=?, role=?, status=? WHERE user_id=?");
                $stmt->bind_param('sssi', $username, $role, $status, $userId);
            }
            if (empty($error) && $stmt->execute()) {
                $success = 'User updated.';
                $editUser = null;
            } elseif (empty($error)) {
                $error = 'Update failed (username may be taken).';
            }
        }
    } else {
        if ($password === '') {
            $error = 'Password is required for new users.';
        } elseif ($passErr = validate_password($password)) {
            $error = $passErr;
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $username, $hash, $role, $status);
            $stmt->execute() ? $success = 'User created.' : $error = 'Create failed (username may be taken).';
        }
    }
}

$users = $conn->query("SELECT user_id, username, role, status, created_at FROM users ORDER BY user_id ASC");

$pageTitle = 'Manage Users - TravelKH';
$extraCss = 'assets/css/auth.css';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
include __DIR__ . '/../includes/admin_sidebar.php';
?>

<h2 class="mb-4">Manage Users</h2>
<p class="text-muted small">Role field: <code>enum('admin','user')</code> — only admins can assign roles.</p>

<?php if ($error): ?><div class="alert alert-danger auth-alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success auth-alert"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

<div class="crud-card mb-4">
  <h4><?php echo $editUser ? 'Edit User' : 'Add New User'; ?></h4>
  <form method="post" class="auth-form row g-3">
    <input type="hidden" name="user_id" value="<?php echo (int) ($editUser['user_id'] ?? 0); ?>">
    <div class="col-md-4">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required pattern="[a-zA-Z0-9_]{3,50}"
             value="<?php echo htmlspecialchars($editUser['username'] ?? ''); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Password <?php echo $editUser ? '(leave blank to keep)' : ''; ?></label>
      <input type="password" name="password" class="form-control" <?php echo $editUser ? '' : 'required'; ?> minlength="6">
    </div>
    <div class="col-md-2">
      <label class="form-label">Role</label>
      <select name="role" class="form-select">
        <option value="user" <?php echo ($editUser['role'] ?? 'user') === 'user' ? 'selected' : ''; ?>>user</option>
        <option value="admin" <?php echo ($editUser['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>admin</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="active" <?php echo ($editUser['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>active</option>
        <option value="inactive" <?php echo ($editUser['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>inactive</option>
      </select>
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-green"><?php echo $editUser ? 'Update User' : 'Create User'; ?></button>
      <?php if ($editUser): ?>
        <a href="<?php echo base_url('admin/users.php'); ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<div class="crud-card">
  <div class="table-responsive">
    <table class="table crud-table align-middle mb-0">
      <thead>
        <tr><th>ID</th><th>Username</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
          <td><?php echo (int) $u['user_id']; ?></td>
          <td><?php echo htmlspecialchars($u['username']); ?></td>
          <td><span class="badge <?php echo $u['role'] === 'admin' ? 'badge-role-admin' : 'badge-role-user'; ?>"><?php echo htmlspecialchars($u['role']); ?></span></td>
          <td><?php echo htmlspecialchars($u['status']); ?></td>
          <td><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
          <td>
            <a href="?edit=<?php echo (int) $u['user_id']; ?>" class="btn btn-sm btn-outline-primary btn-action">Edit</a>
            <?php if ((int) $u['user_id'] !== $selfId): ?>
            <a href="?delete=<?php echo (int) $u['user_id']; ?>" class="btn btn-sm btn-outline-danger btn-action"
               onclick="return confirm('Delete this user?');">Delete</a>
            <?php endif; ?>
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
