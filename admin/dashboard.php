<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../includes/db.php"; //

// Ensure Admin session exists
if (!isset($_SESSION['admin_id'])) { //
    header("Location: login.php");
    exit;
}

// --- 1. Fetch System Statistics ---
$doctors = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM doctors") //
)['total'] ?? 0;

$patients = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM patients") //
)['total'] ?? 0;

$appointments = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM appointments") //
)['total'] ?? 0;

$pending = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM appointments WHERE status='Pending'") //
)['total'] ?? 0;

// --- 2. Fetch Recent System Activity ---
$recent_query = "SELECT a.id, a.appointment_date, a.appointment_time, a.status, 
                        d.name AS doc_name, p.name AS pat_name 
                 FROM appointments a
                 JOIN doctors d ON a.doctor_id = d.id
                 JOIN patients p ON a.patient_id = p.id
                 ORDER BY a.id DESC LIMIT 6"; //
$recent_result = mysqli_query($conn, $recent_query);

include "../includes/admin-header.php"; //
?>

<style>
    :root {
        /* Primary color matched exactly to your admin header: #111827 */
        --admin-primary: #111827; 
        --admin-accent: #374151;
        --bg-light: #f8fafc;
        --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
    }

    body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; }

    /* Admin Hero Banner matches the Navbar exactly */
    .admin-hero {
        background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-accent) 100%);
        border-radius: 24px;
        padding: 40px;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 20px 25px -5px rgba(17, 24, 39, 0.2);
        position: relative;
        overflow: hidden;
    }
    .admin-hero::after {
        content: ''; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px;
        background: rgba(255,255,255,0.05); border-radius: 50%;
    }

    /* Stat Cards */
    .stat-card {
        border: none;
        border-radius: 20px;
        background: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .stat-card:hover { transform: translateY(-8px); box-shadow: var(--card-shadow); }
    
    .stat-icon {
        width: 54px; height: 54px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; font-size: 1.4rem;
    }

    /* Table Panels */
    .glass-panel {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: var(--card-shadow);
    }

    .table thead th {
        background-color: #f1f5f9;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        border: none;
        padding: 1.25rem 1.5rem;
    }
    .table tbody td { padding: 1.25rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }

    /* Status Badges */
    .badge-soft-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-soft-warning { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
    .badge-soft-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .rounded-pill-custom { border-radius: 50px; padding: 6px 16px; font-weight: 600; font-size: 0.75rem; }

    /* Quick Action Buttons */
    .btn-quick-action {
        transition: 0.2s;
        border-radius: 16px !important;
        margin-bottom: 12px;
        border: 1px solid #f1f5f9 !important;
        text-decoration: none;
        color: inherit;
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #ffffff;
    }
    .btn-quick-action:hover {
        background: #f8fafc !important;
        border-color: var(--admin-primary) !important;
        transform: scale(1.02);
    }
</style>

<div class="container-fluid px-4 py-4">
    
    <div class="admin-hero d-flex justify-content-between align-items-center">
        <div>
            <h6 class="text-uppercase fw-bold opacity-75 mb-1" style="letter-spacing: 2px;">
                <i class="bi bi-shield-lock me-2"></i>System Control Center
            </h6>
            <h1 class="display-5 fw-bold mb-0">Admin Console</h1>
            <p class="mt-2 opacity-75 mb-0">Platform Status: <span class="fw-bold">Operational</span> | Active Bookings: <?php echo $appointments; ?></p>
        </div>
        <div class="d-none d-md-block text-end">
            <div class="h4 mb-0 fw-bold"><?php echo date('h:i A'); ?></div>
            <div class="small opacity-75"><?php echo date('l, d M'); ?></div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-semibold small">DOCTORS</h6>
                        <h3 class="mb-0 fw-bold"><?php echo $doctors; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-semibold small">PATIENTS</h6>
                        <h3 class="mb-0 fw-bold"><?php echo $patients; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-dark bg-opacity-10 text-dark me-3">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-semibold small">TOTAL BOOKINGS</h6>
                        <h3 class="mb-0 fw-bold"><?php echo $appointments; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-semibold small">PENDING</h6>
                        <h3 class="mb-0 fw-bold"><?php echo $pending; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="glass-panel h-100">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">System Activity Log</h5>
                        <small class="text-muted">Latest platform interactions</small>
                    </div>
                    <a href="appointments.php" class="btn btn-outline-dark btn-sm rounded-pill px-3">View All Logs</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Provider</th>
                                <th>Patient</th>
                                <th>Schedule</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($recent_result) > 0) {
                                while($row = mysqli_fetch_assoc($recent_result)) { ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['doc_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['pat_name']); ?></td>
                                    <td>
                                        <div class="small fw-bold"><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></div>
                                        <div class="small text-muted"><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></div>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 'Approved') { ?>
                                            <span class="badge badge-soft-success rounded-pill-custom">Approved</span>
                                        <?php } elseif ($row['status'] == 'Pending') { ?>
                                            <span class="badge badge-soft-warning rounded-pill-custom">Pending</span>
                                        <?php } else { ?>
                                            <span class="badge badge-soft-danger rounded-pill-custom">Rejected</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass-panel p-4 h-100">
                <h5 class="fw-bold mb-4">Management Hub</h5>
                <div class="d-grid gap-2">
                    <a href="doctors.php" class="btn-quick-action">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold small">Manage Doctors</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Database & Verification</small>
                        </div>
                    </a>
                    <a href="appointments.php" class="btn-quick-action">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-calendar-week"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold small">Booking Logs</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Monitor system entries</small>
                        </div>
                    </a>
                    <a href="register.php" class="btn-quick-action">
                        <div class="stat-icon bg-dark bg-opacity-10 text-dark me-3">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold small">System Access</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Admin credentials</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?> //