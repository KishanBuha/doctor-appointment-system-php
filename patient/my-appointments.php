<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../includes/db.php";

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['patient_id'];
$message = "";

// --- ACTION: Cancel Appointment ---
if (isset($_GET['cancel'])) {
    $appt_id = intval($_GET['cancel']);
    
    // Ensure the appointment actually belongs to this patient before cancelling
    $check_query = "SELECT id FROM appointments WHERE id='$appt_id' AND patient_id='$patient_id' AND status='Pending'";
    if (mysqli_num_rows(mysqli_query($conn, $check_query)) > 0) {
        mysqli_query($conn, "UPDATE appointments SET status='Cancelled' WHERE id='$appt_id'");
        $message = "<div class='alert alert-warning alert-dismissible fade show'>
                        <i class='bi bi-check-circle me-2'></i> Appointment cancelled successfully.
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    }
}

// Fetch Appointments
$query = "SELECT a.*, d.name as doctor_name, d.specialization 
          FROM appointments a 
          JOIN doctors d ON a.doctor_id = d.id 
          WHERE a.patient_id = '$patient_id' 
          ORDER BY a.appointment_date DESC, a.appointment_time ASC";
$result = mysqli_query($conn, $query);
$total_appts = mysqli_num_rows($result);
?>

<?php include "../includes/patient-header.php"; ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Appointment History</h3>
            <p class="text-muted mb-0">Track your past and upcoming visits</p>
        </div>
        <a href="book-appointment.php" class="btn btn-primary rounded-pill shadow-sm px-4">
            <i class="bi bi-plus-lg me-1"></i> Book New
        </a>
    </div>

    <?php echo $message; ?>

    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4" id="pills-upcoming-tab" data-bs-toggle="pill" data-bs-target="#pills-upcoming" type="button">
                Upcoming & Pending
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4" id="pills-history-tab" data-bs-toggle="pill" data-bs-target="#pills-history" type="button">
                Past & Cancelled
            </button>
        </li>
    </ul>

    <div class="card shadow border-0">
        <div class="card-body p-0">
            <?php if ($total_appts > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Doctor</th>
                            <th>Schedule</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $imgIndex = 1;
                        $has_upcoming = false; // Flag to check if list is empty
                        
                        while ($row = mysqli_fetch_assoc($result)): 
                            $img = "../assets/images/doctors/doc" . (($imgIndex % 5) + 1) . ".png";
                            $imgIndex++;
                            
                            // Classify row for tabs
                            $is_history = ($row['status'] == 'Rejected' || $row['status'] == 'Cancelled' || strtotime($row['appointment_date']) < time());
                            $row_class = $is_history ? "history-row d-none" : "upcoming-row"; 
                        ?>
                        <tr class="appt-row <?php echo $row_class; ?>" data-type="<?php echo $is_history ? 'history' : 'upcoming'; ?>">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $img; ?>" class="rounded-circle me-3 border" width="45" height="45" style="object-fit:cover;">
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo $row['doctor_name']; ?></div>
                                        <small class="text-muted"><?php echo $row['specialization']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></div>
                                <div class="small text-primary fw-bold">
                                    <i class="bi bi-clock me-1"></i><?php echo date('h:i A', strtotime($row['appointment_time'])); ?>
                                </div>
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate text-muted" style="max-width: 150px;">
                                    <?php echo $row['reason']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'Approved') { ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                        <i class="bi bi-check-circle-fill me-1"></i> Approved
                                    </span>
                                <?php } elseif ($row['status'] == 'Pending') { ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">
                                        <i class="bi bi-hourglass-split me-1"></i> Pending
                                    </span>
                                <?php } elseif ($row['status'] == 'Cancelled') { ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">
                                        <i class="bi bi-x-circle-fill me-1"></i> Cancelled
                                    </span>
                                <?php } else { ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">
                                        <i class="bi bi-slash-circle-fill me-1"></i> Rejected
                                    </span>
                                <?php } ?>
                            </td>
                            <td class="text-end pe-4">
                                <?php if ($row['status'] == 'Pending'): ?>
                                    <a href="?cancel=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                       onclick="return confirm('Are you sure you want to cancel this appointment request?');">
                                        Cancel Request
                                    </a>
                                <?php elseif ($row['status'] == 'Approved' && strtotime($row['appointment_date']) >= time()): ?>
                                    <button class="btn btn-sm btn-light border rounded-pill px-3 text-muted" disabled>
                                        Contact Clinic
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">Archived</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div id="empty-state" class="text-center py-5 d-none">
                <img src="../assets/images/icons/list_icon.svg" width="60" class="mb-3 opacity-25">
                <h5 class="text-muted">No appointments found in this category.</h5>
            </div>

            <?php else: ?>
                <div class="text-center py-5">
                    <img src="../assets/images/icons/calendar_icon.svg" width="80" class="mb-4 opacity-50">
                    <h4 class="text-muted">No appointments yet</h4>
                    <p class="text-muted mb-4">Book your first visit with our specialists today.</p>
                    <a href="book-appointment.php" class="btn btn-primary rounded-pill px-4 shadow-sm">Book Appointment</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const upcomingTab = document.getElementById("pills-upcoming-tab");
        const historyTab = document.getElementById("pills-history-tab");
        const rows = document.querySelectorAll(".appt-row");
        const emptyState = document.getElementById("empty-state");

        function filterRows(type) {
            let visibleCount = 0;
            rows.forEach(row => {
                if (type === 'upcoming') {
                    if (row.dataset.type === 'upcoming') {
                        row.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        row.classList.add('d-none');
                    }
                } else {
                    if (row.dataset.type === 'history') {
                        row.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        row.classList.add('d-none');
                    }
                }
            });

            if (visibleCount === 0) {
                emptyState.classList.remove('d-none');
            } else {
                emptyState.classList.add('d-none');
            }
        }

        // Initialize (Default: Upcoming)
        filterRows('upcoming');

        upcomingTab.addEventListener("click", () => filterRows('upcoming'));
        historyTab.addEventListener("click", () => filterRows('history'));
    });
</script>

<?php include "../includes/footer.php"; ?>