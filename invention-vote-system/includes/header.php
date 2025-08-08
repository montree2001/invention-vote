<?php
require_once __DIR__ . '/../config/settings.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($app_name) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css" />
</head>
<body>
<header>
    <h1><?= htmlspecialchars($app_name) ?></h1>
</header>
