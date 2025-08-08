<?php
// General application settings

// Name of the system used in page titles and headers
$app_name = 'Invention Vote System';

// Display PHP errors. Should be set to false in production environments
$display_errors = getenv('APP_DEBUG') === 'true';
if ($display_errors) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}

?>
