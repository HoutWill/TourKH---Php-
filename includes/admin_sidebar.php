<?php
$admin_page = basename($_SERVER['PHP_SELF']);
?>
<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-sidebar-brand">Admin Panel</div>
    <nav class="admin-sidebar-nav">
      <a href="<?php echo base_url('admin/index.php'); ?>" class="<?php echo $admin_page === 'index.php' ? 'active' : ''; ?>">Dashboard</a>
      <a href="<?php echo base_url('admin/tours.php'); ?>" class="<?php echo $admin_page === 'tours.php' ? 'active' : ''; ?>">Tours</a>
      <a href="<?php echo base_url('admin/bookings.php'); ?>" class="<?php echo $admin_page === 'bookings.php' ? 'active' : ''; ?>">Bookings</a>
      <a href="<?php echo base_url('admin/users.php'); ?>" class="<?php echo $admin_page === 'users.php' ? 'active' : ''; ?>">Users</a>
      <a href="<?php echo base_url('admin/profile.php'); ?>" class="<?php echo $admin_page === 'profile.php' ? 'active' : ''; ?>">My Profile</a>
      <a href="<?php echo base_url('index.php'); ?>">View Site</a>
      <a href="<?php echo base_url('auth/logout.php'); ?>">Logout</a>
    </nav>
  </aside>
  <main class="admin-content">
