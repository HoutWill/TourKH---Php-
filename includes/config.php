<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_ROOT', dirname(__DIR__));

// Default database credentials (override in config.local.php)
$host = "localhost";
$user = "root";
$pass = "123456";
$db   = "travel_tour_db";

// Default Stripe credentials (override in config.local.php)
$stripe_secret = "your_stripe_secret_key_here";
$stripe_publishable = "your_stripe_publishable_key_here";

// Allow overriding credentials locally (ignored in git)
if (file_exists(__DIR__ . '/config.local.php')) {
    include __DIR__ . '/config.local.php';
}

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

function base_url($path = '') {
    static $base = null;
    if ($base === null) {
        $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
        $appRoot = str_replace('\\', '/', realpath(APP_ROOT));
        $base = rtrim(str_replace($docRoot, '', $appRoot), '/') . '/';
    }
    return $base . ltrim($path, '/');
}

function redirect($path) {
    header('Location: ' . base_url($path));
    exit;
}

function getTourImage($imgName) {
    if (empty($imgName)) {
        return 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=800&q=80';
    }
    if (strpos($imgName, 'http') === 0) {
        return $imgName;
    }
    $localFile = APP_ROOT . '/assets/images/' . $imgName;
    if (file_exists($localFile)) {
        return base_url('assets/images/' . $imgName);
    }
    $fallbacks = [
        'angkor.jpg' => 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=800&q=80',
        'kohrong.jpg' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        'knongpsa.jpg' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
        'phnompenh.jpg' => 'https://images.unsplash.com/photo-1569154941061-e231b4725ef1?auto=format&fit=crop&w=800&q=80',
        'cardamom.jpg' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=800&q=80',
        'kampot_river.jpg' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=800&q=80'
    ];
    return isset($fallbacks[$imgName]) ? $fallbacks[$imgName] : 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=800&q=80';
}

require_once __DIR__ . '/auth.php';
