<?php
// core/Router.php

namespace App\Core;

class Router {
    public function dispatch(string $controllerClass, string $methodName, array $params = []): void {
        // Check if controller class exists
        if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass();

            // Check if method exists
            if (method_exists($controllerInstance, $methodName)) {
                // Call the method, passing parameters
                call_user_func_array([$controllerInstance, $methodName], $params);
            } else {
                // Method not found
                $this->notFound("Method '{$methodName}' not found in controller '{$controllerClass}'.");
            }
        } else {
            // Controller not found
            $this->notFound("Controller class '{$controllerClass}' not found.");
        }
    }

    /**
     * Basic 404 error handling.
     * In a real application, you'd likely render a proper 404 view.
     */
    protected function notFound(string $message = "Page not found."): void {
        header("HTTP/1.0 404 Not Found");
        // You could include a view here: include_once(VIEWS_PATH . 'errors/404.php');
        echo "<h1>404 Not Found</h1>";
        echo "<p>{$message}</p>";
        // Log the error message as well
        error_log("Router Error: " . $message);
        exit;
    }
}
?>
