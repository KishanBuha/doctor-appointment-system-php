<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../includes/db.php";

// Ensure doctor session exists
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit;
}

$doctor_id = $_SESSION['doctor_id'];
$raw_name = $_SESSION['doctor_name'] ?? "Doctor";
$today = date("Y-m-d");

// Smart Name Display
$display_name = (stripos(trim($raw_name), 'Dr.') === 0) ? $raw_name : 'Dr. ' . $raw_name;

// Calculate Greeting
$hour = date('H');
$greeting = ($hour < 12) ? "Good Morning" : (($hour < 17) ? "Good Afternoon" : "Good Evening");

// --- 1. Fetch Statistics ---
$totalAppts = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM appointments WHERE doctor_id='$doctor_id'"))['total'];
$todayCount = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM appointments WHERE doctor_id='$doctor_id' AND appointment_date='$today'"))['total'];
$pending = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM appointments WHERE doctor_id='$doctor_id' AND status='Pending'"))['total'];

// --- 2. Fetch Today's Itinerary ---
$today_query = "SELECT a.appointment_time, a.status, p.name AS patient_name, p.phone 
                FROM appointments a 
                JOIN patients p ON a.patient_id = p.id 
                WHERE a.doctor_id='$doctor_id' AND a.appointment_date='$today' 
                ORDER BY a.appointment_time ASC";
$today_result = mysqli_query($conn, $today_query);

include "../includes/doctor-header.php";
?>

<style>
    :root {
        /* Primary color matched exactly to your header: #065f46 */
        --primary-med: #065f46; 
        --accent-med: #0d9488;
        --bg-light: #f8fafc;
        --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
    }

    body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; }

    /* Hero Banner matches the Navbar exactly */
    .hero-banner {
        background: linear-gradient(135deg, var(--primary-med) 0%, var(--accent-med) 100%);
        border-radius: 24px;
        padding: 40px;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 20px 25px -5px rgba(6, 95, 70, 0.2);
        position: relative;
        overflow: hidden;
    }
    .hero-banner::after {
        content: ''; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px;
        background: rgba(255,255,255,0.08); border-radius: 50%;
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
        width: 56px; height: 56px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
    }

    .glass-panel {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: var(--card-shadow);
    }

    /* Table Design */
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

    /* Avatar Icons */
    .avatar-init {
        width: 42px; height: 42px; border-radius: 10px;
        background: #ecfdf5; color: var(--primary-med);
        display: flex; align-items: center; justify-content: center; font-weight: 700;
    }

    /* Status Chips */
    .badge-soft-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-soft-warning { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
    .badge-soft-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .rounded-pill-custom { border-radius: 50px; padding: 6px 16px; font-weight: 600; font-size: 0.75rem; }

    /* Action Tiles */
    .action-tile {
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
    .action-tile:hover {
        background: #f8fafc !important;
        border-color: var(--primary-med) !important;
        transform: scale(1.02);
    }
</style>

<div class="container-fluid px-4 py-4">
    
    <div class="hero-banner d-flex justify-content-between align-items-center">
        <div>
            <h6 class="text-uppercase fw-bold opacity-75 mb-1" style="letter-spacing: 2px;">
                <i class="bi bi-activity me-2"></i><?php echo $greeting; ?>
            </h6>
            <h1 class="display-5 fw-bold mb-0"><?php echo htmlspecialchars($display_name); ?></h1>
            <p class="mt-2 opacity-75 mb-0">You have <span class="fw-bold"><?php echo $todayCount; ?> sessions</span> confirmed for today.</p>
        </div>
        <div class="d-none d-md-block text-end">
            <div class="h4 mb-0 fw-bold"><?php echo date('h:i A'); ?></div>
            <div class="small opacity-75"><?php echo date('l, F d'); ?></div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-bold">TODAY'S VISITS</p>
                        <h3 class="fw-bold mb-0"><?php echo $todayCount; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-bold">PENDING REQUESTS</p>
                        <h3 class="fw-bold mb-0"><?php echo $pending; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-calendar-heart"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-bold">TOTAL BOOKINGS</p>
                        <h3 class="fw-bold mb-0"><?php echo $totalAppts; ?></h3>
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
                        <h5 class="fw-bold mb-0">Daily Itinerary</h5>
                        <small class="text-muted">Clinical Schedule</small>
                    </div>
                    <a href="appointments.php" class="btn btn-sm btn-outline-dark rounded-pill px-3">View All</a>
                </div>
                
                <div class="table-responsive">
                    <?php if (mysqli_num_rows($today_result) > 0) { ?>
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($today_result)) {
                                    $names = explode(" ", trim($row['patient_name']));
                                    $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                                ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-init me-3"><?php echo $initials; ?></div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['patient_name']); ?></div>
                                                    <small class="text-muted">Clinical Visit</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><i class="bi bi-telephone text-muted me-2 small"></i><?php echo htmlspecialchars($row['phone']); ?></td>
                                        <td>
                                            <?php if ($row['status'] == 'Approved') { ?>
                                                <span class="badge badge-soft-success rounded-pill-custom">Confirmed</span>
                                            <?php } elseif ($row['status'] == 'Pending') { ?>
                                                <span class="badge badge-soft-warning rounded-pill-custom">Pending</span>
                                            <?php } else { ?>
                                                <span class="badge badge-soft-danger rounded-pill-custom">Cancelled</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted display-4"></i>
                            <h6 class="mt-3 text-muted">No appointments scheduled for today.</h6>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass-panel p-4 h-100">
                <h5 class="fw-bold mb-4">Practice Hub</h5>
                <div class="d-grid gap-2">
                    <a href="schedule.php" class="action-tile">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-calendar-plus"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold small">Schedule</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Availability</small>
                        </div>
                    </a>
                    <a href="fees.php" class="action-tile">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold small">Manage Fees</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Pricing</small>
                        </div>
                    </a>
                    <a href="profile.php" class="action-tile">
                        <div class="stat-icon bg-dark bg-opacity-10 text-dark me-3">
                            <i class="bi bi-person-gear"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold small">Edit Profile</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Details</small>
                        </div>
                    </a>
                    <a href="inquiries.php" class="action-tile">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold small">Inquiries</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Messages</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>