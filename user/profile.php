<?php
require_once __DIR__ . '/../includes/config.php';
require_user();

$userId = current_user_id();
$user = get_user_by_id($conn, $userId);
$error = '';
$success = '';

if (isset($_GET['booking_success']) && $_GET['booking_success'] == 1) {
    $success = 'Tour booked and paid successfully via Stripe!';
}


// Helper to resolve avatar image
function getProfileImage($imgName) {
    if (empty($imgName) || $imgName === 'default_avatar.jpg') {
        return 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80';
    }
    if (strpos($imgName, 'http') === 0) {
        return $imgName;
    }
    return base_url('assets/images/users/' . $imgName);
}

// Handle Profile Updates
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
    } elseif ($action === 'update_profile') {
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $nationality = trim($_POST['nationality'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $profile_image = $user['profile_image']; // Default to existing image

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_image']['tmp_name'];
            $fileName = $_FILES['profile_image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $newFileName = 'user_' . $userId . '_' . time() . '.' . $fileExtension;
            $uploadFileDir = APP_ROOT . '/assets/images/users/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $dest_path = $uploadFileDir . $newFileName;
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExtension, $allowedExtensions, true)) {
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $profile_image = $newFileName;
                } else {
                    $error = 'There was an error moving the uploaded profile image.';
                }
            } else {
                $error = 'Upload failed. Allowed formats: JPG, JPEG, PNG, GIF, WEBP.';
            }
        }

        // Basic validation
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (empty($error)) {
            $upd = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, profile_image = ?, nationality = ?, bio = ? WHERE user_id = ?");
            $upd->bind_param('ssssssi', $full_name, $email, $phone, $profile_image, $nationality, $bio, $userId);
            if ($upd->execute()) {
                $success = 'Profile updated successfully.';
                // Refresh user info
                $user = get_user_by_id($conn, $userId);
            } else {
                $error = 'Failed to update profile: ' . $conn->error;
            }
        }
    }
}

// Fetch booking history count and records
$bookingCountQuery = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE user_id = $userId");
$bookingCount = $bookingCountQuery ? $bookingCountQuery->fetch_assoc()['c'] : 0;

$pageTitle = 'My Profile - TravelKH';
// Link new profile stylesheet
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<!-- Page Specific CSS -->
<link href="<?php echo base_url('assets/css/profile.css'); ?>" rel="stylesheet">

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<section class="profile-layout-section">
    <div class="container">
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius:12px;">
                <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius:12px;">
                <strong>Success!</strong> <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            
            <!-- Left Profile Card -->
            <div class="col-lg-4" data-aos="fade-right">
                <div class="profile-card">
                    <div class="profile-cover"></div>
                    <div class="profile-avatar-container">
                        <img src="<?php echo getProfileImage($user['profile_image']); ?>" alt="Profile Avatar" class="profile-avatar">
                    </div>
                    <div class="profile-info">
                        <h3><?php echo !empty($user['full_name']) ? htmlspecialchars($user['full_name']) : 'New Explorer'; ?></h3>
                        <span class="username-badge">@<?php echo htmlspecialchars($user['username']); ?></span>
                        <p class="bio-text">
                            <?php echo !empty($user['bio']) ? htmlspecialchars($user['bio']) : 'No bio added yet. Tell us about your next adventure!'; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Right Content Panels -->
            <div class="col-lg-8" data-aos="fade-left">
                
                <!-- Nav Tabs -->
                <ul class="profile-nav-tabs">
                    <li>
                        <button class="nav-link active" onclick="switchTab(event, 'tab-overview')">Overview</button>
                    </li>
                    <li>
                        <button class="nav-link" onclick="switchTab(event, 'tab-bookings')">My Bookings</button>
                    </li>
                    <li>
                        <button class="nav-link" onclick="switchTab(event, 'tab-settings')">Edit Profile</button>
                    </li>
                    <li>
                        <button class="nav-link" onclick="switchTab(event, 'tab-security')">Security</button>
                    </li>
                </ul>
                
                <!-- Overview Panel -->
                <div id="tab-overview" class="tab-content-panel profile-details-card">
                    <h4 class="mb-4" style="font-weight: 700; color: var(--charcoal);">Account Details</h4>
                    <div class="row">
                        <div class="col-md-6 detail-row">
                            <div class="detail-label">Full Name</div>
                            <div class="detail-value"><?php echo !empty($user['full_name']) ? htmlspecialchars($user['full_name']) : 'Not set'; ?></div>
                        </div>
                        <div class="col-md-6 detail-row">
                            <div class="detail-label">Email Address</div>
                            <div class="detail-value"><?php echo !empty($user['email']) ? htmlspecialchars($user['email']) : 'Not set'; ?></div>
                        </div>
                        <div class="col-md-6 detail-row">
                            <div class="detail-label">Phone Number</div>
                            <div class="detail-value"><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not set'; ?></div>
                        </div>
                        <div class="col-md-6 detail-row">
                            <div class="detail-label">Nationality</div>
                            <div class="detail-value"><?php echo !empty($user['nationality']) ? htmlspecialchars($user['nationality']) : 'Not set'; ?></div>
                        </div>
                        <div class="col-md-6 detail-row">
                            <div class="detail-label">Member Since</div>
                            <div class="detail-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></div>
                        </div>
                        <div class="col-md-6 detail-row">
                            <div class="detail-label">Total Bookings</div>
                            <div class="detail-value"><?php echo (int) $bookingCount; ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Bookings Panel -->
                <div id="tab-bookings" class="tab-content-panel profile-details-card d-none">
                    <h4 class="mb-4" style="font-weight: 700; color: var(--charcoal);">Booking History</h4>
                    
                    <?php
                    // Fetch booking history with joined tour details
                    $bookings_stmt = $conn->query("SELECT b.*, t.title, t.price, t.duration, t.image 
                                                   FROM bookings b 
                                                   JOIN tours t ON b.tour_id = t.tour_id 
                                                   WHERE b.user_id = $userId 
                                                   ORDER BY b.created_at DESC");
                    
                    if ($bookings_stmt && $bookings_stmt->num_rows > 0):
                        while ($b = $bookings_stmt->fetch_assoc()):
                            $status_class = 'badge-pending';
                            if ($b['status'] === 'approved') {
                                $status_class = 'badge-approved';
                            } elseif ($b['status'] === 'rejected') {
                                $status_class = 'badge-rejected';
                            }
                    ?>
                    <div class="booking-item-card">
                        <img src="<?php echo getTourImage($b['image']); ?>" alt="Tour Thumbnail" class="booking-tour-img">
                        <div class="booking-details-box">
                            <a href="<?php echo base_url('tour-details.php?id=' . $b['tour_id']); ?>" class="booking-tour-title">
                                <?php echo htmlspecialchars($b['title']); ?>
                            </a>
                            <div class="booking-meta-row mt-1">
                                <span>⏱️ <?php echo htmlspecialchars($b['duration']); ?></span> &nbsp;|&nbsp;
                                <span>📅 Booked on: <?php echo date("M d, Y", strtotime($b['booking_date'])); ?></span>
                            </div>
                            <div class="booking-price-tag mt-2">
                                $<?php echo number_format($b['total_price'] > 0 ? $b['total_price'] : $b['price'], 2); ?> 
                                <span class="text-muted" style="font-size:0.8rem; font-weight:normal;">(<?php echo (int)$b['people']; ?> traveler<?php echo $b['people'] > 1 ? 's' : ''; ?>)</span>
                            </div>
                        </div>
                        <div>
                            <span class="badge-status <?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($b['status']); ?>
                            </span>
                        </div>
                    </div>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <div class="text-center py-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor" class="text-muted mb-3 bi bi-calendar-event" viewBox="0 0 16 16">
                            <path d="M11 6.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2z"/>
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                        </svg>
                        <h5>No Active Bookings</h5>
                        <p class="text-muted">You haven't booked any tours yet. Explore Cambodia today!</p>
                        <a href="<?php echo base_url('tours.php'); ?>" class="btn btn-green mt-2 px-4 py-2" style="border-radius:12px;">Browse Tours</a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Settings Panel -->
                <div id="tab-settings" class="tab-content-panel profile-details-card d-none">
                    <h4 class="mb-4" style="font-weight: 700; color: var(--charcoal);">Edit Profile Details</h4>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_profile">
                        <div class="row">
                            <div class="col-md-6 profile-form-group">
                                <label for="form-name">Full Name</label>
                                <input type="text" id="form-name" name="full_name" class="profile-form-control" placeholder="Your Name" value="<?php echo htmlspecialchars($user['full_name']); ?>">
                            </div>
                            <div class="col-md-6 profile-form-group">
                                <label for="form-email">Email Address</label>
                                <input type="email" id="form-email" name="email" class="profile-form-control" placeholder="yourname@domain.com" value="<?php echo htmlspecialchars($user['email']); ?>">
                            </div>
                            <div class="col-md-6 profile-form-group">
                                <label for="form-phone">Phone Number</label>
                                <input type="text" id="form-phone" name="phone" class="profile-form-control" placeholder="+855 ..." value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>
                            <div class="col-md-6 profile-form-group">
                                <label for="form-nationality">Nationality</label>
                                <input type="text" id="form-nationality" name="nationality" class="profile-form-control" placeholder="e.g. Cambodian" value="<?php echo htmlspecialchars($user['nationality']); ?>">
                            </div>
                             <div class="col-md-12 profile-form-group">
                                 <label for="form-avatar">Profile Image</label>
                                 <input type="file" id="form-avatar" name="profile_image" class="profile-form-control" accept="image/*">
                                 <small class="text-muted mt-1 d-block">Choose an image file from your device (JPG, JPEG, PNG, GIF, or WEBP).</small>
                             </div>
                            <div class="col-md-12 profile-form-group">
                                <label for="form-bio">Bio Blurb</label>
                                <textarea id="form-bio" name="bio" class="profile-form-control" rows="4" placeholder="Tell travelers and guides a bit about yourself..."><?php echo htmlspecialchars($user['bio']); ?></textarea>
                            </div>
                            
                            <div class="col-md-12 text-end mt-3">
                                <button type="submit" class="btn-profile-save">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Security Panel -->
                <div id="tab-security" class="tab-content-panel profile-details-card d-none">
                    <h4 class="mb-4" style="font-weight: 700; color: var(--charcoal);">Change Password</h4>
                    <form method="POST">
                        <input type="hidden" name="action" value="change_password">
                        <div class="profile-form-group">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="profile-form-control" required>
                        </div>
                        <div class="profile-form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="profile-form-control" required minlength="6">
                        </div>
                        <div class="profile-form-group">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="profile-form-control" required minlength="6">
                        </div>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn-profile-save">Update Password</button>
                        </div>
                    </form>
                </div>
                
            </div>
            
        </div>
        
    </div>
</section>

<!-- Vanilla JS tabs switcher -->
<script>
function switchTab(event, tabId) {
    event.preventDefault();
    
    // Hide all panels
    document.querySelectorAll('.tab-content-panel').forEach(panel => {
        panel.classList.add('d-none');
    });
    
    // Deactivate all links
    document.querySelectorAll('.profile-nav-tabs .nav-link').forEach(link => {
        link.classList.remove('active');
    });
    
    // Show active panel & active link
    document.getElementById(tabId).classList.remove('d-none');
    event.currentTarget.classList.add('active');
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
