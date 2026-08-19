<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | Hospital Management</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/react-theme.css">
    
    <style>
        body { background-color: #f8f9fa; }
        
        /* Force text color to white for visibility */
        .navbar-nav .nav-link { 
            color: #ffffff !important; 
            font-weight: 500; 
            font-size: 1rem;
            padding: 10px 15px;
            transition: 0.2s;
            opacity: 0.85; 
        }
        .navbar-nav .nav-link:hover { 
            background: rgba(255, 255, 255, 0.15); 
            border-radius: 5px;
            opacity: 1;
        }
        .navbar-brand {
            color: #ffffff !important;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3" style="background-color: #111827 !important;">
        <div class="container-fluid px-4">
            
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
                <span class="bg-primary text-white rounded p-1 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-shield-lock-fill small"></i>
                </span>
                Admin Console
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctors.php"><i class="bi bi-people-fill me-1"></i> Doctors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="appointments.php"><i class="bi bi-calendar-check me-1"></i> Appointments</a>
                    </li>
                    
                    <li class="nav-item ms-lg-3">
                        <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold text-white border-white">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </nav>

    <main class="py-4">