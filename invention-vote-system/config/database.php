<?php
// Database configuration settings. Update the environment variables or change
// defaults below to match your local setup.

$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'invention_vote_system';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASSWORD') ?: '';

?>
