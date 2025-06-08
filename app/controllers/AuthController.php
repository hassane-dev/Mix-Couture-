<?php
// app/controllers/AuthController.php

namespace App\Controllers;

use App\Models\User;
use App\Core\Session;

class AuthController {

    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Displays the login page.
     */
    public function login(): void {
        // If already logged in, redirect to dashboard
        if (Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/auth/dashboard');
            exit;
        }
        // Load the login view
        $this->loadView('auth/login');
    }

    /**
     * Handles the login form submission.
     */
    public function handleLogin(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Basic input sanitation
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? ''; // Get password directly, do not sanitize it as it breaks password_verify

            if (empty($email) || empty($password)) {
                Session::setFlash('error', 'Email and password are required.');
                $this->loadView('auth/login');
                return;
            }

            $user = $this->userModel->findByEmail($email);

            if ($user && $user['IsActive'] && $this->userModel->verifyPassword($password, $user['PasswordHash'])) {
                // Password is correct, start session
                Session::regenerateId(); // Regenerate session ID for security
                Session::set('user_id', $user['UserID']);
                Session::set('user_email', $user['Email']);
                Session::set('user_fullname', $user['FullName']);
                Session::set('user_role_id', $user['RoleID']);

                // Fetch RoleName (conceptual - User model could have a method to get user with role name)
                // For now, we'll assume RoleID is sufficient for basic role checks or RoleName is fetched separately
                // $userDetails = $this->userModel->findById($user['UserID']); // This would fetch RoleName
                // if ($userDetails) {
                //    Session::set('user_role_name', $userDetails['RoleName']);
                // }


                // Redirect to dashboard
                header('Location: ' . BASE_URL . '/auth/dashboard');
                exit;
            } else {
                // Invalid credentials or inactive user
                Session::setFlash('error', 'Invalid email or password, or account inactive.');
                $this->loadView('auth/login');
            }
        } else {
            // Not a POST request, redirect to login
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Displays the dashboard page (protected).
     */
    public function dashboard(): void {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Please log in to access the dashboard.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // User is logged in, load the dashboard view
        // You can pass user data to the view if needed
        $userData = [
            'fullName' => Session::get('user_fullname'),
            'email' => Session::get('user_email'),
            // 'roleName' => Session::get('user_role_name') // if set during login
        ];
        $this->loadView('dashboard/index', $userData);
    }

    /**
     * Handles user logout.
     */
    public function logout(): void {
        Session::destroy();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }

    /**
     * Simple view loader.
     * @param string $viewName The name of the view file (e.g., 'auth/login').
     * @param array $data Data to pass to the view.
     */
    protected function loadView(string $viewName, array $data = []): void {
        // Make data available to the view
        extract($data);

        $viewFile = VIEWS_PATH . $viewName . '.php';
        if (is_readable($viewFile)) {
            require_once $viewFile;
        } else {
            // In a real app, show a proper error page or log
            die("Error: View '{$viewName}' not found.");
        }
    }

    /**
     * Default action for the controller, e.g., if only /auth is accessed.
     */
    public function index(): void {
        $this->login();
    }
}
?>
