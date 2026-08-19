<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../includes/db.php";

// Check login status
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['patient_id'];

// --- 1. Fetch Patient Name ---
$pat_query = mysqli_query($conn, "SELECT name FROM patients WHERE id='$patient_id'");
$pat_data = mysqli_fetch_assoc($pat_query);
$patient_name = $pat_data['name'] ?? "Patient";

// --- 2. Fetch Statistics ---
$total = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) c FROM appointments WHERE patient_id='$patient_id'")
)['c'] ?? 0;

$pending = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) c FROM appointments WHERE patient_id='$patient_id' AND status='Pending'")
)['c'] ?? 0;

$approved = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) c FROM appointments WHERE patient_id='$patient_id' AND status='Approved'")
)['c'] ?? 0;

// --- 3. Get Next Upcoming Appointment ---
$next_query = "SELECT a.*, d.name as doc_name 
               FROM appointments a 
               JOIN doctors d ON a.doctor_id = d.id 
               WHERE a.patient_id='$patient_id' 
               AND a.appointment_date >= CURDATE() 
               AND a.status = 'Approved'
               ORDER BY a.appointment_date ASC, a.appointment_time ASC 
               LIMIT 1";
$next_result = mysqli_query($conn, $next_query);
$next_appt = mysqli_fetch_assoc($next_result);

// --- 4. Recent History List ---
$history_query = "SELECT a.*, d.name as doc_name, d.specialization 
                  FROM appointments a 
                  JOIN doctors d ON a.doctor_id = d.id 
                  WHERE a.patient_id='$patient_id' 
                  ORDER BY a.appointment_date DESC 
                  LIMIT 5";
$history_result = mysqli_query($conn, $history_query);

include "../includes/patient-header.php";
?>

<style>
    :root {
        /* Primary color matched exactly to your patient header: #2563eb */
        --patient-primary: #2563eb; 
        --patient-accent: #3b82f6;
        --bg-light: #f8fafc;
        --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
    }

    body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; }

    /* Hero Banner matches the Navbar Blue exactly */
    .patient-hero {
        background: linear-gradient(135deg, #1e40af 0%, var(--patient-primary) 100%);
        border-radius: 24px;
        padding: 40px;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.2);
        position: relative;
        overflow: hidden;
    }
    .patient-hero::after {
        content: ''; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px;
        background: rgba(255,255,255,0.1); border-radius: 50%;
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
        display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
    }

    /* Glass Panels */
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

    /* Upcoming Appointment Badge Style */
    .upcoming-box {
        background: #1e3a8a;
        color: white;
        border-radius: 20px;
        padding: 24px;
        position: relative;
        overflow: hidden;
    }
    .upcoming-box i.bg-icon {
        position: absolute; top: 10px; right: 10px; font-size: 4rem; opacity: 0.1;
    }

    /* Status Badges */
    .badge-soft-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-soft-warning { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
    .badge-soft-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .rounded-pill-custom { border-radius: 50px; padding: 6px 16px; font-weight: 600; font-size: 0.75rem; }

    /* Quick Action Buttons - FIXED & COMPACT */
    .btn-action {
        transition: 0.2s;
        border-radius: 12px;
        margin-bottom: 8px;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        color: inherit;
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        background: #ffffff;
    }
    .btn-action:hover {
        background: #f8fafc;
        border-color: var(--patient-primary);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }
    .btn-action:last-child {
        margin-bottom: 0;
    }
    
    /* NEW: Sticky Sidebar logic */
    .sidebar-sticky-wrapper {
        position: sticky;
        top: 2rem; /* Keeps it slightly pushed down from the navbar */
        z-index: 10;
    }
</style>

<div class="container-fluid px-4 py-4">
    
    <div class="patient-hero d-flex justify-content-between align-items-center">
        <div>
            <h6 class="text-uppercase fw-bold opacity-75 mb-1" style="letter-spacing: 2px;">
                <i class="bi bi-heart-pulse me-2"></i>My Health Dashboard
            </h6>
            <h1 class="display-5 fw-bold mb-0">Hello, <?php echo htmlspecialchars($patient_name); ?></h1>
            <p class="mt-2 opacity-75 mb-0">Stay on top of your health with your medical overview.</p>
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
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-journal-medical"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-bold">TOTAL BOOKINGS</p>
                        <h3 class="fw-bold mb-0"><?php echo $total; ?></h3>
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
                        <p class="text-muted mb-0 small fw-bold">PENDING APPROVAL</p>
                        <h3 class="fw-bold mb-0"><?php echo $pending; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-bold">CONFIRMED VISITS</p>
                        <h3 class="fw-bold mb-0"><?php echo $approved; ?></h3>
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
                        <h5 class="fw-bold mb-0">Recent Activity</h5>
                        <small class="text-muted">Your latest medical history</small>
                    </div>
                    <a href="my-appointments.php" class="btn btn-sm btn-outline-dark rounded-pill px-3">View All</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Schedule</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($history_result) > 0) {
                                while($row = mysqli_fetch_assoc($history_result)) { ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['doc_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['specialization']); ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></div>
                                        <small class="text-muted"><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></small>
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
                            <?php } } else { ?>
                                <tr><td colspan="3" class="text-center py-5 text-muted">No appointments found.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            
            <div class="sidebar-sticky-wrapper">
                
                <div class="upcoming-box mb-4 shadow-sm">
                    <i class="bi bi-calendar-event bg-icon"></i>
                    <h6 class="text-uppercase small fw-bold opacity-75 mb-3">Next Appointment</h6>
                    <?php if ($next_appt): ?>
                        <h3 class="fw-bold mb-1"><?php echo date('l, M d', strtotime($next_appt['appointment_date'])); ?></h3>
                        <p class="fs-5 mb-3"><?php echo date('h:i A', strtotime($next_appt['appointment_time'])); ?></p>
                        <div class="d-flex align-items-center bg-white bg-opacity-10 p-3 rounded-4">
                            <div class="bg-white bg-opacity-20 rounded-circle p-2 me-3">
                                <i class="bi bi-person-badge text-white"></i>
                            </div>
                            <div>
                                <small class="d-block opacity-75">Assigned Doctor</small>
                                <span class="fw-bold"><?php echo htmlspecialchars($next_appt['doc_name']); ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="py-3 text-center opacity-75">
                            <i class="bi bi-info-circle display-6 d-block mb-2"></i>
                            <p class="small mb-0">No upcoming visits confirmed yet.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="glass-panel p-4">
                    <h5 class="fw-bold mb-3">Quick Actions</h5>
                    <div class="d-flex flex-column">
                        <a href="book-appointment.php" class="btn-action">
                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">Book Appointment</div>
                                <div class="text-muted" style="font-size: 0.70rem;">Schedule a new visit</div>
                            </div>
                        </a>
                        <a href="my-appointments.php" class="btn-action">
                            <div class="bg-dark bg-opacity-10 text-dark p-2 rounded-3 me-3">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">My Appointments</div>
                                <div class="text-muted" style="font-size: 0.70rem;">View and track visits</div>
                            </div>
                        </a>
                        <a href="contact.php" class="btn-action">
                            <div class="bg-success bg-opacity-10 text-success p-2 rounded-3 me-3">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">Contact Doctor</div>
                                <div class="text-muted" style="font-size: 0.70rem;">Message medical staff</div>
                            </div>
                        </a>
                    </div>
                </div>
            
            </div> </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>