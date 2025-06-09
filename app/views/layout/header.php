<?php use App\Core\Session; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Fashion Platform'; ?> - Fashion Platform</title>
    <!-- Local CSS -->
    <link href="<?php echo BASE_URL; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/assets/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 56px; /* Adjust padding-top if navbar is fixed */
        }
        .navbar {
            /* margin-bottom: 20px; Replaced by fixed-top and body padding */
        }
        /* Additional global styles can go here */
        .flash-message {
            position: fixed;
            top: 70px; /* Below navbar */
            right: 20px;
            z-index: 1050; /* Ensure it's above most other content */
            min-width: 250px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/auth/dashboard">Fashion Platform</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbarNav" aria-controls="mainNavbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($activeNav) && $activeNav == 'dashboard') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/auth/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($activeNav) && $activeNav == 'clients') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/clients">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($activeNav) && $activeNav == 'models') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/models">Models</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($activeNav) && $activeNav == 'orders') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/orders">Orders</a>
                    </li>
                    <!-- Example for Expenses - assuming it exists -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($activeNav) && $activeNav == 'expenses') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/expenses">Expenses</a>
                    </li>
                    <?php if (Session::get('user_role_id') == 1): /* Assuming 1 is Admin RoleID */ ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($activeNav) && $activeNav == 'users') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/users">Users</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (Session::isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars(Session::get('user_fullname') ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <!-- <li><a class="dropdown-item" href="#">Profile</a></li> -->
                                <!-- <li><hr class="dropdown-divider"></li> -->
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/auth/logout">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/auth/login">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- This container would typically wrap the main content of each view -->
    <!-- Flash messages container -->
    <div id="flashMessageContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1055; top: 56px !important;">
        <!-- Flash messages will be injected here by JS or directly in PHP -->
        <?php
        $successMessage = Session::getFlash('success');
        if ($successMessage) {
            echo '<div class="alert alert-success alert-dismissible fade show flash-message" role="alert">';
            echo htmlspecialchars($successMessage);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
        $errorMessage = Session::getFlash('error');
        if ($errorMessage) {
            echo '<div class="alert alert-danger alert-dismissible fade show flash-message" role="alert">';
            echo htmlspecialchars($errorMessage);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
        ?>
    </div>

    <main class="container mt-4 pt-4"> <!-- Added pt-4 for spacing due to fixed navbar -->
        <!-- View content will be injected here -->
