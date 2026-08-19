<?php
session_start();
include "../includes/db.php";

// Protect page
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

// Handle approve / reject actions
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'approve') {
        mysqli_query($conn, "UPDATE appointments SET status='Approved' WHERE id='$id' AND doctor_id='$doctor_id'");
    } elseif ($action === 'reject') {
        mysqli_query($conn, "UPDATE appointments SET status='Rejected' WHERE id='$id' AND doctor_id='$doctor_id'");
    }

    header("Location: appointments.php");
    exit;
}

// Fetch appointments (Added 'appointment_end_time' to query)
$query = "
    SELECT 
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.appointment_end_time, 
        a.status,
        a.reason,
        p.name AS patient_name,
        p.phone AS patient_phone,
        p.email AS patient_email
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = '$doctor_id'
    ORDER BY a.appointment_date DESC
";
$result = mysqli_query($conn, $query);
?>

<?php include "../includes/doctor-header.php"; ?>

<div class="row">
    <div class="col-md-12">

        <div class="card shadow border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-primary">Appointment Requests</h3>
                <span class="badge bg-primary rounded-pill">
                    Total: <?php echo mysqli_num_rows($result); ?>
                </span>
            </div>

            <?php if (mysqli_num_rows($result) > 0) { ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Patient Details</th>
                                <th>Schedule (Duration)</th> <th>Reason</th>
                                <th>Status</th>
                                <th width="200">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php while ($row = mysqli_fetch_assoc($result)) { 
                            // Format Start and End times
                            $startTime = date('h:i A', strtotime($row['appointment_time']));
                            
                            // If end time exists in DB use it, otherwise assume 30 mins
                            if (!empty($row['appointment_end_time']) && $row['appointment_end_time'] != '00:00:00') {
                                $endTime = date('h:i A', strtotime($row['appointment_end_time']));
                            } else {
                                $endTime = date('h:i A', strtotime($row['appointment_time'] . ' +30 minutes'));
                            }
                            
                            // Calculate duration in minutes for display
                            $start_ts = strtotime($row['appointment_time']);
                            $end_ts   = strtotime($endTime); // Using the formatted string works, but raw DB value is safer if available
                            if(!empty($row['appointment_end_time'])) $end_ts = strtotime($row['appointment_end_time']);
                            
                            $duration_mins = round(abs($end_ts - $start_ts) / 60, 0);
                        ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?php echo $row['patient_name']; ?></div>
                                    <small class="text-muted"><?php echo $row['patient_phone']; ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-1">
                                        <?php echo date('M d, Y', strtotime($row['appointment_date'])); ?>
                                    </div>
                                    <div class="small text-primary bg-light border rounded px-2 py-1 d-inline-block">
                                        <i class="bi bi-clock me-1"></i>
                                        <?php echo $startTime . " - " . $endTime; ?>
                                    </div>
                                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                        (<?php echo $duration_mins; ?> Mins)
                                    </div>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 150px;" title="<?php echo $row['reason']; ?>">
                                        <?php echo $row['reason']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill 
                                        <?php
                                            if ($row['status'] == 'Approved') echo 'bg-success';
                                            elseif ($row['status'] == 'Rejected') echo 'bg-danger';
                                            else echo 'bg-warning text-dark';
                                        ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'Pending') { ?>
                                        <div class="d-flex gap-2">
                                            <a href="?action=approve&id=<?php echo $row['id']; ?>" 
                                               class="btn btn-outline-success btn-sm rounded-pill px-3"
                                               title="Approve Appointment">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                            <a href="?action=reject&id=<?php echo $row['id']; ?>" 
                                               class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                               title="Reject Appointment"
                                               onclick="return confirm('Are you sure you want to REJECT this appointment?');">
                                                <i class="bi bi-x-lg"></i>
                                            </a>
                                        </div>
                                    <?php } else { ?>
                                        <span class="text-muted small fst-italic">
                                            <i class="bi bi-lock-fill"></i> Closed
                                        </span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>
                </div>

            <?php } else { ?>
                <div class="text-center py-5">
                    <img src="../assets/images/icons/list_icon.svg" width="60" class="mb-3 opacity-50">
                    <h5 class="text-muted">No appointments found</h5>
                    <p class="text-muted small">New booking requests will appear here.</p>
                </div>
            <?php } ?>

        </div>
        
    </div>
</div>

<?php include "../includes/footer.php"; ?>