<?php

const ROLE_ADMIN = 'admin';
const ROLE_USER  = 'user';
const VALID_ROLES = [ROLE_ADMIN, ROLE_USER];

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function current_user_role() {
    return $_SESSION['role'] ?? null;
}

function is_admin() {
    return current_user_role() === ROLE_ADMIN;
}

function require_login($redirect = 'auth/login.php') {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        redirect($redirect);
    }
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        redirect('index.php');
    }
}

function require_user() {
    require_login();
    if (is_admin()) {
        redirect('admin/index.php');
    }
}

function get_user_by_id($conn, $userId) {
    $stmt = $conn->prepare("SELECT user_id, username, role, status, created_at, full_name, email, phone, profile_image, nationality, bio FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function verify_password_and_upgrade($conn, $plainPassword, $row) {
    if (password_verify($plainPassword, $row['password'])) {
        return true;
    }
    if (hash_equals($row['password'], $plainPassword)) {
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param('si', $hash, $row['user_id']);
        $stmt->execute();
        return true;
    }
    return false;
}

function login_user($row) {
    $_SESSION['user_id'] = (int) $row['user_id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['role'] = $row['role'];
    $_SESSION['user'] = (int) $row['user_id'];
}

function logout_user() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function validate_username($username) {
    $username = trim($username);
    if (strlen($username) < 3 || strlen($username) > 50) {
        return 'Username must be 3–50 characters.';
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return 'Username may only contain letters, numbers, and underscores.';
    }
    return null;
}

function validate_password($password) {
    if (strlen($password) < 6) {
        return 'Password must be at least 6 characters.';
    }
    return null;
}

function validate_role($role) {
    return in_array($role, VALID_ROLES, true) ? null : 'Invalid role. Must be admin or user.';
}

function validate_status($status) {
    return in_array($status, ['active', 'inactive'], true) ? null : 'Invalid status.';
}

function post_login_redirect() {
    $target = $_SESSION['redirect_after_login'] ?? '';
    unset($_SESSION['redirect_after_login']);
    if (!empty($target)) {
        header('Location: ' . $target);
        exit;
    }
    if (is_admin()) {
        redirect('admin/index.php');
    }
    redirect('index.php');
}
