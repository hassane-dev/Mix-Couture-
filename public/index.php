<?php
// public/index.php

// Load configuration
require_once '../config/config.php';

// Autoloader for classes
spl_autoload_register(function ($className) {
    // Convert namespace to path
    // Example: App\Controllers\AuthController becomes app/controllers/AuthController.php
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

    $paths = [
        APP_ROOT . '/', // For classes like App\Core\Router, App\Models\User
        CORE_PATH,
        CONTROLLERS_PATH,
        MODELS_PATH
    ];

    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (is_readable($file)) {
            require_once $file;
            return;
        }
    }

    // Fallback for simple class names without full namespace (adjust as needed for your structure)
    // This might be needed if your classes are not strictly namespaced under App\
    if (file_exists(APP_ROOT . '/' . $className . '.php')) {
        require_once APP_ROOT . '/' . $className . '.php';
    } elseif (file_exists(CORE_PATH . $className . '.php')) {
        require_once CORE_PATH . $className . '.php';
    } elseif (file_exists(CONTROLLERS_PATH . $className . '.php')) {
        require_once CONTROLLERS_PATH . $className . '.php';
    } elseif (file_exists(MODELS_PATH . $className . '.php')) {
        require_once MODELS_PATH . $className . '.php';
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
