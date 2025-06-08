<?php
// config/config.php

// Database Configuration
define('DB_HOST', 'localhost'); // Replace with your database host
define('DB_NAME', 'fashion_platform_db'); // Replace with your database name
define('DB_USER', 'root'); // Replace with your database username
define('DB_PASS', ''); // Replace with your database password
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('BASE_URL', 'http://localhost/fashion_platform'); // Replace with your site's base URL (e.g., http://localhost/yourproject)
define('APP_ROOT', dirname(dirname(__FILE__))); // Points to the 'app' directory's parent

// Session Configuration
define('SESSION_NAME', 'FashionPlatformSession');

// Paths
define('CORE_PATH', APP_ROOT . '/core/');
define('CONTROLLERS_PATH', APP_ROOT . '/app/controllers/');
define('MODELS_PATH', APP_ROOT . '/app/models/');
define('VIEWS_PATH', APP_ROOT . '/app/views/');
?>
