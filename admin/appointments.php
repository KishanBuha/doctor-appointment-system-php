<?php
session_start();
include "../includes/db.php";

// Protect admin page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle Deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM appointments WHERE id='$id'");
    header("Location: appointments.php");
    exit;
}

// Fetch Appointments with Patient and Doctor details
$query = "SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.reason,
                 p.name AS patient_name, p.phone AS patient_phone,
                 d.name AS doctor_name, d.specialization
          FROM appointments a
          JOIN patients p ON a.patient_id = p.id
          JOIN doctors d ON a.doctor_id = d.id
          ORDER BY a.appointment_date DESC, a.appointment_time ASC";
$result = mysqli_query($conn, $query);
$total = mysqli_num_rows($result);
?>

<?php include "../includes/admin-header.php"; ?>

<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Appointment Records</h3>
            <p class="text-muted mb-0">View and manage all hospital bookings</p>
        </div>
        <span class="badge bg-dark rounded-pill px-3 py-2 shadow-sm">
            Total Bookings: <?php echo $total; ?>
        </span>
    </div>

    <div class="card shadow border-0">
        <div class="card-header bg-white p-3 border-bottom">
            <h5 class="mb-0 text-primary fw-bold">Master Schedule</h5>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Date & Time</th>
                        <th>Patient Info</th>
                        <th>Assigned Doctor</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                
                <?php if ($total > 0) { 
                    while ($row = mysqli_fetch_assoc($result)) { 
                ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark"><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></div>
                            <small class="text-muted"><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></small>
                        </td>

                        <td>
                            <div class="fw-bold text-dark"><?php echo $row['patient_name']; ?></div>
                            <small class="text-muted"><i class="bi bi-telephone me-1"></i><?php echo $row['patient_phone']; ?></small>
                        </td>

                        <td>
                            <div class="fw-bold text-primary"><?php echo $row['doctor_name']; ?></div>
                            <span class="badge bg-light text-secondary border"><?php echo $row['specialization']; ?></span>
                        </td>

                        <td>
                            <?php if ($row['status'] == 'Approved') { ?>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                    <span class="dot bg-success d-inline-block rounded-circle me-1" style="width:6px; height:6px;"></span> Approved
                                </span>
                            <?php } elseif ($row['status'] == 'Rejected') { ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">
                                    <span class="dot bg-danger d-inline-block rounded-circle me-1" style="width:6px; height:6px;"></span> Rejected
                                </span>
                            <?php } else { ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">
                                    <span class="dot bg-warning d-inline-block rounded-circle me-1" style="width:6px; height:6px;"></span> Pending
                                </span>
                            <?php } ?>
                        </td>

                        <td class="text-end pe-4">
                            <a href="?delete=<?php echo $row['id']; ?>" 
                               class="btn btn-sm btn-outline-danger rounded-pill px-3"
                               onclick="return confirm('Are you sure you want to permanently delete this appointment record?');">
                                <i class="bi bi-trash-fill me-1"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php } 
                } else { ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x display-4 d-block mb-3 opacity-25"></i>
                            No appointment records found in the system.
                        </td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>