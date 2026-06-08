<?php
$current_page = basename($_SERVER['PHP_SELF']);
$in_admin = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false;
?>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand-modern" href="<?php echo base_url('index.php'); ?>">
      <span>Travel</span>KH
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
      <?php if (!$in_admin): ?>
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center gap-1">
        <li class="nav-item">
          <a class="nav-link-modern <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="<?php echo base_url('index.php'); ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link-modern <?php echo $current_page === 'tours.php' ? 'active' : ''; ?>" href="<?php echo base_url('tours.php'); ?>">Tours</a>
        </li>
        <li class="nav-item">
          <a class="nav-link-modern <?php echo $current_page === 'about.php' ? 'active' : ''; ?>" href="<?php echo base_url('about.php'); ?>">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link-modern <?php echo $current_page === 'packages.php' ? 'active' : ''; ?>" href="<?php echo base_url('packages.php'); ?>">Packages</a>
        </li>
        <li class="nav-item">
          <a class="nav-link-modern <?php echo $current_page === 'contact.php' ? 'active' : ''; ?>" href="<?php echo base_url('contact.php'); ?>">Contact</a>
        </li>
      </ul>
      <?php endif; ?>

      <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0 <?php echo $in_admin ? 'ms-auto' : ''; ?>">
        <?php if (is_logged_in()): ?>
          <?php if (is_admin()): ?>
            <a href="<?php echo base_url('admin/index.php'); ?>" class="btn-nav-secondary">Admin Panel</a>
            <a href="<?php echo base_url('admin/profile.php'); ?>" class="btn-nav-secondary">Profile</a>
          <?php else: ?>
            <a href="<?php echo base_url('user/profile.php'); ?>" class="btn-nav-secondary">My Profile</a>
          <?php endif; ?>
          <a href="<?php echo base_url('auth/logout.php'); ?>" class="btn-nav-primary">Logout</a>
        <?php else: ?>
          <a href="<?php echo base_url('auth/register.php'); ?>" class="btn-nav-secondary">Register</a>
          <a href="<?php echo base_url('auth/login.php'); ?>" class="btn-nav-primary">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
