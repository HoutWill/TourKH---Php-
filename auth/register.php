<?php
require_once __DIR__ . '/../includes/config.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $userErr = validate_username($username);
    $passErr = validate_password($password);

    if ($userErr) {
        $error = $userErr;
    } elseif ($passErr) {
        $error = $passErr;
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $check->bind_param('s', $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'Username already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = ROLE_USER;
            $status = 'active';
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $username, $hash, $role, $status);
            if ($stmt->execute()) {
                $success = 'Account created! You can now log in.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

$pageTitle = 'Register - TravelKH';
$extraCss = 'assets/css/auth.css';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<section class="auth-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="auth-card">
          <h1>Create Account</h1>
          <p class="text-muted mb-4">Join TravelKH to book tours across Cambodia.</p>

          <?php if ($error): ?>
            <div class="alert alert-danger auth-alert"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
          <?php if ($success): ?>
            <div class="alert alert-success auth-alert"><?php echo htmlspecialchars($success); ?></div>
          <?php endif; ?>

          <form method="post" class="auth-form">
            <div class="mb-3">
              <label class="form-label" for="username">Username</label>
              <input type="text" class="form-control" id="username" name="username" required
                     pattern="[a-zA-Z0-9_]{3,50}"
                     value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
              <div class="form-text">3–50 characters: letters, numbers, underscore only.</div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" required minlength="6">
            </div>
            <div class="mb-4">
              <label class="form-label" for="confirm_password">Confirm Password</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
            </div>
            <button type="submit" class="btn btn-green w-100 py-2">Register</button>
          </form>

          <p class="text-center mt-4 mb-0 text-muted">
            Already have an account?
            <a href="<?php echo base_url('auth/login.php'); ?>" class="text-success fw-semibold">Login</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
