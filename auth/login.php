<?php
require_once __DIR__ . '/../includes/config.php';

if (is_logged_in()) {
    post_login_redirect();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        $stmt = $conn->prepare("SELECT user_id, username, password, role, status FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row) {
            $error = 'Invalid username or password.';
        } elseif ($row['status'] !== 'active') {
            $error = 'Your account is inactive. Contact support.';
        } elseif (!verify_password_and_upgrade($conn, $password, $row)) {
            $error = 'Invalid username or password.';
        } else {
            login_user($row);
            post_login_redirect();
        }
    }
}

$pageTitle = 'Login - TravelKH';
$extraCss = 'assets/css/auth.css';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<section class="auth-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="auth-card">
          <h1>Welcome Back</h1>
          <p class="text-muted mb-4">Sign in to manage your bookings and profile.</p>

          <?php if ($error): ?>
            <div class="alert alert-danger auth-alert"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>

          <form method="post" class="auth-form">
            <div class="mb-3">
              <label class="form-label" for="username">Username</label>
              <input type="text" class="form-control" id="username" name="username" required
                     value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            <div class="mb-4">
              <label class="form-label" for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-green w-100 py-2">Login</button>
          </form>

          <p class="text-center mt-4 mb-0 text-muted">
            Don't have an account?
            <a href="<?php echo base_url('auth/register.php'); ?>" class="text-success fw-semibold">Register</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
