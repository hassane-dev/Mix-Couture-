<?php
// core/Session.php

namespace App\Core;

class Session {
    /**
     * Starts or resumes a session.
     */
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Set session cookie parameters for better security
            session_set_cookie_params([
                'lifetime' => 0, // Expires when browser closes
                'path' => '/',
                'domain' => '', // Set your domain in production
                'secure' => isset($_SERVER['HTTPS']), // Send only over HTTPS
                'httponly' => true, // Prevent JavaScript access
                'samesite' => 'Lax' // Mitigate CSRF
            ]);
            session_name(SESSION_NAME); // Use a custom session name from config
            session_start();
        }
    }

    /**
     * Sets a session variable.
     * @param string $key The key of the session variable.
     * @param mixed $value The value to store.
     */
    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Gets a session variable.
     * @param string $key The key of the session variable.
     * @return mixed|null The value if set, null otherwise.
     */
    public static function get(string $key): mixed {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Checks if a session variable is set.
     * @param string $key The key of the session variable.
     * @return bool True if set, false otherwise.
     */
    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    /**
     * Removes a session variable.
     * @param string $key The key of the session variable to remove.
     */
    public static function remove(string $key): void {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroys the current session.
     */
    public static function destroy(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Unset all session variables
            $_SESSION = [];

            // Delete the session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            // Finally, destroy the session
            session_destroy();
        }
    }

    /**
     * Regenerates the session ID to prevent session fixation.
     * Call this after login or any privilege level change.
     */
    public static function regenerateId(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Checks if a user is logged in.
     * Assumes 'user_id' is stored in session upon successful login.
     * @return bool True if logged in, false otherwise.
     */
    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    /**
     * Sets a flash message that is removed after being displayed once.
     * @param string $key The key for the flash message.
     * @param string $message The message content.
     */
    public static function setFlash(string $key, string $message): void {
        self::set('flash_' . $key, $message);
    }

    /**
     * Gets and clears a flash message.
     * @param string $key The key for the flash message.
     * @return string|null The message if set, null otherwise.
     */
    public static function getFlash(string $key): ?string {
        $message = self::get('flash_' . $key);
        if ($message) {
            self::remove('flash_' . $key);
        }
        return $message;
    }
}
?>
