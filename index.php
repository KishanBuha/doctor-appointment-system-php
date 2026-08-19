<?php include "includes/db.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Hospital | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/react-theme.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #fbbf24;
        }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f8f9fa;
        }

        /* --- 1. COMPACT HERO SECTION --- */
        .hero-section {
            min-height: 75vh; /* REDUCED from 100vh to eliminate empty space */
            background: radial-gradient(circle at top right, var(--primary-color), var(--secondary-color));
            position: relative;
            display: flex;
            align-items: center; 
            justify-content: center; 
            text-align: center;
            color: white;
            padding-top: 60px; /* Reduced top padding */
            padding-bottom: 140px; /* Space for deeper card overlap */
            clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
        }

        /* Background Pattern Overlay */
        .hero-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 900px;
            padding: 20px;
            margin-top: -40px; /* Shift text slightly up to balance */
        }

        /* --- 2. FLOATING CARDS --- */
        .cards-container {
            margin-top: -120px; /* Increased overlap to close the gap */
            position: relative;
            z-index: 5;
            padding-bottom: 40px; /* Reduced bottom padding */
        }

        .portal-card {
            background: white;
            border-radius: 20px;
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08); /* Softer shadow */
        }
        .portal-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
        }
        
        .portal-card .icon-circle {
            width: 70px; height: 70px;
            border-radius: 50%;
            background: #f0f9ff;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            color: var(--primary-color);
            font-size: 2rem;
        }

        footer { background: #fff; margin-top: auto; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="transition: 0.3s;">
        <div class="container mt-2">
            <a class="navbar-brand fw-bold fs-3 d-flex align-items-center" href="#">
                <div class="bg-white text-primary rounded-circle p-1 me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-hospital-fill"></i>
                </div>
                Global Hospital
            </a>
            <div class="ms-auto">
                <a href="patient/login.php" class="btn btn-outline-light rounded-pill px-4 fw-bold small">Login</a>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <span class="badge bg-white text-primary bg-opacity-100 px-3 py-2 rounded-pill mb-3 shadow-sm fw-bold">
                <i class="bi bi-star-fill text-warning me-1"></i> #1 Multi-Specialty Hospital
            </span>
            <h1 class="display-3 fw-bolder mb-3">Your Health, Our Priority</h1>
            <p class="lead opacity-75 mb-0 px-md-5 fs-4">
                Connect with top-tier specialists instantly. Secure, fast, and caring.
            </p>
        </div>
    </section>

    <section class="cards-container container">
        <div class="row g-4 justify-content-center">
            
            <div class="col-lg-4 col-md-6">
                <div class="card portal-card h-100 p-4 text-center">
                    <div class="icon-circle">
                        <i class="bi bi-person-heart"></i>
                    </div>
                    <h4 class="fw-bold">Patient Portal</h4>
                    <p class="text-muted small mb-4">Book appointments, check history, and manage your health records.</p>
                    <a href="patient/login.php" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">Patient Login</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card portal-card h-100 p-4 text-center">
                    <div class="icon-circle text-success" style="background: #f0fdf4;">
                        <i class="bi bi-bandaid"></i>
                    </div>
                    <h4 class="fw-bold">Doctor Console</h4>
                    <p class="text-muted small mb-4">View daily schedules, manage appointments, and update availability.</p>
                    <a href="doctor/login.php" class="btn btn-success w-100 rounded-pill py-2 fw-bold">Doctor Login</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card portal-card h-100 p-4 text-center">
                    <div class="icon-circle text-dark" style="background: #f3f4f6;">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h4 class="fw-bold">Admin Access</h4>
                    <p class="text-muted small mb-4">Hospital administration, staff management, and system reports.</p>
                    <a href="admin/login.php" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">Admin Login</a>
                </div>
            </div>

        </div>
    </section>

    <section class="container py-4 mb-4">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="p-3">
                    <h1 class="text-primary fw-bold display-4 mb-0">24/7</h1>
                    <p class="lead fw-bold mb-1">Emergency Support</p>
                    <p class="text-muted small">Always available for critical care.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <h1 class="text-primary fw-bold display-4 mb-0">50+</h1>
                    <p class="lead fw-bold mb-1">Specialist Doctors</p>
                    <p class="text-muted small">Experienced professionals.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <h1 class="text-primary fw-bold display-4 mb-0">10k+</h1>
                    <p class="lead fw-bold mb-1">Happy Patients</p>
                    <p class="text-muted small">Trusted by the community.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center py-4 bg-white border-top">
        <div class="container">
            <p class="mb-0 text-muted">&copy; <?php echo date('Y'); ?> Global Hospital Management System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar Scrolled Effect
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                nav.style.background = 'rgba(37, 99, 235, 0.95)';
                nav.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
            } else {
                nav.style.background = 'transparent';
                nav.style.boxShadow = 'none';
            }
        });
    </script>
</body>
</html>