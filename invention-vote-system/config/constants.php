<?php
// Global constants used across the system

// Base URL of the application (without trailing slash). Change if deploying
// under a sub directory or different domain.
define('BASE_URL', getenv('APP_BASE_URL') ?: '/invention-vote-system');

// User role identifiers for permission checking
define('ROLE_SUPER_ADMIN', 'SUPER_ADMIN');
define('ROLE_ADMIN', 'ADMIN');
define('ROLE_CHAIRMAN', 'CHAIRMAN');
define('ROLE_JUDGE', 'JUDGE');

// Session key name that stores currently logged in user information
define('SESSION_USER_KEY', 'iv_user');

// Default timezone for all date functions
date_default_timezone_set('Asia/Bangkok');

?>
