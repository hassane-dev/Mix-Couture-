<?php
// public/index.php

// Load configuration
require_once '../config/config.php';

// Autoloader for classes
spl_autoload_register(function ($className) {
    // Ensure backslashes are directory separators for internal consistency
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

    $file = null;

    // Check for App\Core namespace
    if (strpos($className, 'App' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR) === 0) {
        // Remove 'App/Core/' prefix
        $relativeClassName = substr($className, strlen('App' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR));
        // CORE_PATH is defined in config.php as APP_ROOT . '/core/'
        $file = CORE_PATH . $relativeClassName . '.php'; // e.g. app_root/core/Session.php
    }
    // Check for App\Controllers namespace
    elseif (strpos($className, 'App' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR) === 0) {
        // Remove 'App/Controllers/' prefix
        $relativeClassName = substr($className, strlen('App' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR));
        // CONTROLLERS_PATH is defined in config.php as APP_ROOT . '/app/controllers/'
        $file = CONTROLLERS_PATH . $relativeClassName . '.php'; // e.g. app_root/app/controllers/AuthController.php
    }
    // Check for App\Models namespace
    elseif (strpos($className, 'App' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR) === 0) {
        // Remove 'App/Models/' prefix
        $relativeClassName = substr($className, strlen('App' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR));
        // MODELS_PATH is defined in config.php as APP_ROOT . '/app/models/'
        $file = MODELS_PATH . $relativeClassName . '.php'; // e.g. app_root/app/models/User.php
    }

    if ($file && is_readable($file)) {
        require_once $file;
    } else {
        // Optional: Log if a class in the App namespace was not found by this autoloader
        // if (strpos($className, 'App' . DIRECTORY_SEPARATOR) === 0) {
        //     error_log("Autoloader: Class " . $className . " not found. Tried path: " . ($file ?? 'N/A'));
        // }
    }
});


// Start the session using the Session helper
App\Core\Session::start();

// Basic URL parsing
// Example: /auth/login will call AuthController's login method
// Example: /public/index.php?url=auth/login
$url = $_GET['url'] ?? 'auth/login'; // Default to login page
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

// Controller and Method
$controllerName = !empty($urlParts[0]) ? ucfirst($urlParts[0]) . 'Controller' : 'AuthController';
$methodName = $urlParts[1] ?? 'index'; // Default method if not specified (e.g. for /dashboard)
$params = array_slice($urlParts, 2);

// Prepend namespace
$controllerClass = 'App\\Controllers\\' . $controllerName;


// Instantiate router and dispatch
$router = new App\Core\Router();
$router->dispatch($controllerClass, $methodName, $params);

?>
