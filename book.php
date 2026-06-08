<?php
require_once __DIR__ . '/includes/config.php';
$tourId = (int)($_GET['id'] ?? 0);
redirect("user/book.php?id=$tourId");
?>
