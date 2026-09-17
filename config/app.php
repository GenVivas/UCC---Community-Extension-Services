<?php

/**
 * Application Configuration
 * UCC-CES Management System
 */

define('APP_NAME',    'UCC-CES Management System');
define('APP_VERSION', '1.0.0');
define('APP_ENV',     'development'); // 'development' | 'production'

// Base URL — adjust if deployed in a subdirectory
define('BASE_URL', 'http://localhost/UCC-CES');

// Absolute path to project root
define('ROOT_PATH',    dirname(__DIR__));
define('APP_PATH',     ROOT_PATH . '/app');
define('PUBLIC_PATH',  ROOT_PATH . '/public');
define('HELPERS_PATH', ROOT_PATH . '/helpers');
define('LOGS_PATH',    ROOT_PATH . '/logs');

// Upload directories
define('UPLOAD_PATH',        PUBLIC_PATH . '/uploads');
define('UPLOAD_PROPOSALS',   UPLOAD_PATH . '/proposals');
define('UPLOAD_EVALUATIONS', UPLOAD_PATH . '/evaluations');
define('UPLOAD_CERTS',       UPLOAD_PATH . '/certificates');

// Session config
define('SESSION_NAME',    'ucc_ces_session');
define('SESSION_TIMEOUT', 7200); // 2 hours in seconds

// Pagination
define('PER_PAGE', 15);

// Date/Time
define('APP_TIMEZONE', 'Asia/Manila');
date_default_timezone_set(APP_TIMEZONE);

// Error display based on environment
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
