<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!function_exists('base_url')) {
    require_once __DIR__ . '/config.php';
}
$pageTitle = $pageTitle ?? 'TravelKH';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
<?php if (!empty($extraCss)): ?>
<link href="<?php echo base_url($extraCss); ?>" rel="stylesheet">
<?php endif; ?>
</head>
<body>
