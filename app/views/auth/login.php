<?php
// app/views/auth/login.php
// Ensure session is started (usually done in index.php or a core file)
if (session_status() === PHP_SESSION_NONE) {
    // This is a fallback, ideally App\Core\Session::start() is called much earlier
    // For safety, ensuring it's active before trying to access $_SESSION for flash messages.
    // However, direct session_start() here might conflict if headers already sent or custom session handler is used.
    // It's better if Session::start() is guaranteed by the entry point (public/index.php).
    // For now, we assume Session class is available and start has been called.
    // App\Core\Session::start(); // Redundant if called in index.php
}
use App\Core\Session; // To use Session::getFlash()

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fashion Platform</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center mb-4">Admin Login</h2>

        <?php $errorMessage = Session::getFlash('error'); ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <?php $successMessage = Session::getFlash('success'); ?>
        <?php if ($successMessage): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/auth/handleLogin" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <!-- <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe">
                <label class="form-check-label" for="rememberMe">Remember me</label>
            </div> -->
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <!-- Optional: Add links like "Forgot Password?" here -->
        <!-- <div class="text-center mt-3">
            <a href="#">Forgot Password?</a>
        </div> -->
    </div>

    <!-- Bootstrap 5 JS Bundle (Popper.js included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
