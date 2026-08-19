<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../includes/db.php";

// Protect doctor page
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit;
}

$doctor_id = $_SESSION['doctor_id'];
$doctor_name = $_SESSION['doctor_name'] ?? "Doctor";
$message = "";

// Standard predefined slots (Updated with Early Evening)
$time_slots = [
    'Morning' => ['09:00 AM', '09:30 AM', '10:00 AM', '10:30 AM', '11:00 AM', '11:30 AM'],
    'Afternoon' => ['12:00 PM', '12:30 PM', '02:00 PM', '02:30 PM', '03:00 PM', '03:30 PM', '04:00 PM', '04:30 PM'],
    'Early Evening' => ['05:00 PM', '05:30 PM', '06:00 PM', '06:30 PM', '07:00 PM', '07:30 PM']
];

// --- 1. HANDLE BULK BLOCK / UNBLOCK ---
if (isset($_POST['bulk_action'])) {
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date   = mysqli_real_escape_string($conn, $_POST['end_date']);
    $action     = mysqli_real_escape_string($conn, $_POST['action']); 
    
    $selected_slots = isset($_POST['time_slots']) ? $_POST['time_slots'] : [];

    if (empty($selected_slots)) {
        $message = "<div class='alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-4'>
                        <i class='bi bi-exclamation-triangle-fill me-2'></i> Please select at least one time slot to apply changes.
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    } else {
        $current_date = strtotime($start_date);
        $end_date_time = strtotime($end_date);

        while ($current_date <= $end_date_time) {
            $date_str = date('Y-m-d', $current_date);
            foreach ($selected_slots as $st) {
                $st_clean = mysqli_real_escape_string($conn, $st);

                if ($action === 'block') {
                    $chk = mysqli_query($conn, "SELECT id FROM blocked_slots WHERE doctor_id='$doctor_id' AND block_date='$date_str' AND block_time='$st_clean'");
                    if (mysqli_num_rows($chk) == 0) {
                        mysqli_query($conn, "INSERT INTO blocked_slots (doctor_id, block_date, block_time) VALUES ('$doctor_id', '$date_str', '$st_clean')");
                    }
                } elseif ($action === 'unblock') {
                    mysqli_query($conn, "DELETE FROM blocked_slots WHERE doctor_id='$doctor_id' AND block_date='$date_str' AND block_time='$st_clean'");
                }
            }
            $current_date = strtotime("+1 day", $current_date); 
        }
        
        $action_text = $action === 'block' ? 'blocked' : 'unblocked';
        $message = "<div class='alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4'>
                        <i class='bi bi-check-circle-fill me-2'></i> Schedule successfully $action_text from $start_date to $end_date!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    }
}

// --- 2. HANDLE SINGLE SLOT BLOCK / UNBLOCK ---
if (isset($_POST['single_action'])) {
    $action_date = mysqli_real_escape_string($conn, $_POST['selected_date']);
    $action_time = mysqli_real_escape_string($conn, $_POST['slot_time']); 
    
    if (isset($_POST['block_slot'])) {
        $query = "INSERT INTO blocked_slots (doctor_id, block_date, block_time) VALUES ('$doctor_id', '$action_date', '$action_time')";
        mysqli_query($conn, $query);
    } elseif (isset($_POST['unblock_slot'])) {
        $query = "DELETE FROM blocked_slots WHERE doctor_id='$doctor_id' AND block_date='$action_date' AND block_time='$action_time'";
        mysqli_query($conn, $query);
    }
    header("Location: schedule.php?date=" . $action_date);
    exit;
}

// Get selected date for the view
$selected_date = isset($_GET['date']) ? mysqli_real_escape_string($conn, $_GET['date']) : date('Y-m-d');

// Fetch appointments
$query = "SELECT a.*, p.name as patient_name, p.phone as patient_phone 
          FROM appointments a 
          JOIN patients p ON a.patient_id = p.id 
          WHERE a.doctor_id = '$doctor_id' 
          AND a.appointment_date = '$selected_date' 
          AND a.status IN ('Approved', 'Pending')";
$result = mysqli_query($conn, $query);

$booked_slots = [];
$total_booked = 0;
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $time_key = date('H:i', strtotime($row['appointment_time']));
        $booked_slots[$time_key] = $row;
        $total_booked++;
    }
}

// Fetch blocked slots
$blocked_query = "SELECT block_time FROM blocked_slots WHERE doctor_id = '$doctor_id' AND block_date = '$selected_date'";
$blocked_result = mysqli_query($conn, $blocked_query);

$blocked_slots_array = [];
$total_blocked = 0;
if ($blocked_result && mysqli_num_rows($blocked_result) > 0) {
    while ($row = mysqli_fetch_assoc($blocked_result)) {
        $time_key = date('H:i', strtotime($row['block_time']));
        $blocked_slots_array[] = $time_key;
        $total_blocked++;
    }
}

$total_slots = 20; // Updated for 6 morning + 8 afternoon + 6 evening
$available_slots = $total_slots - $total_booked - $total_blocked;

include "../includes/doctor-header.php"; 
?>

<style>
    /* Grid Styles */
    .slot-card { transition: all 0.2s ease; border-left: 4px solid transparent; }
    .slot-available { background-color: #f8fafc; border-left-color: #10b981; }
    .slot-booked { background-color: #eff6ff; border-left-color: #2563eb; }
    .slot-pending { background-color: #fffbeb; border-left-color: #f59e0b; }
    .slot-blocked { background-color: #fef2f2; border-left-color: #ef4444; opacity: 0.8;}
    .time-badge { width: 90px; text-align: center; }

    /* Modal Action Toggle Styles */
    .btn-block-toggle, .btn-unblock-toggle { color: #64748b; border: none; transition: all 0.3s ease; background: transparent; }
    .btn-block-toggle:hover, .btn-unblock-toggle:hover { color: #1e293b; }
    .btn-check:checked + .btn-block-toggle { background-color: #ef4444; color: white; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }
    .btn-check:checked + .btn-unblock-toggle { background-color: #10b981; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
    
    /* Modal Time Slot Checkbox Pill Styles */
    .slot-pill-label { font-size: 0.8rem; font-weight: 600; transition: all 0.2s ease; background: #f8fafc; border-color: #e2e8f0; color: #475569; cursor: pointer; }
    .slot-pill-label:hover { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; }
    .btn-check:checked + .slot-pill-label { background-color: #1e293b !important; border-color: #1e293b !important; color: white !important; }
</style>

<div class="container-fluid pb-5">
    
    <?php echo $message; ?>

    <div class="row mb-4 align-items-end">
        <div class="col-md-5">
            <h3 class="fw-bold text-dark mb-1">Daily Schedule</h3>
            <p class="text-muted mb-0">Manage your availability and patient bookings</p>
        </div>
        <div class="col-md-7 text-md-end mt-3 mt-md-0 d-flex flex-column flex-md-row justify-content-md-end gap-2">
            <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#bulkManageModal">
                <i class="bi bi-calendar2-range me-2"></i> Manage Availability 
            </button>
            <form method="GET" class="d-inline-flex align-items-center bg-white p-2 rounded-pill shadow-sm border">
                <label for="datePicker" class="fw-bold text-muted px-3 mb-0"><i class="bi bi-calendar-week me-2"></i>Date:</label>
                <input type="date" id="datePicker" name="date" class="form-control border-0 bg-light rounded-pill" 
                       value="<?php echo $selected_date; ?>" onchange="this.form.submit()" style="cursor: pointer;">
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row gap-4 bg-white flex-wrap">
                <div><span class="text-muted small fw-bold text-uppercase">Total</span><h5 class="fw-bold mb-0"><?php echo $total_slots; ?></h5></div>
                <div class="border-start ps-4"><span class="text-muted small fw-bold text-uppercase">Available</span><h5 class="fw-bold text-success mb-0"><?php echo $available_slots; ?></h5></div>
                <div class="border-start ps-4"><span class="text-muted small fw-bold text-uppercase">Booked</span><h5 class="fw-bold text-primary mb-0"><?php echo $total_booked; ?></h5></div>
                <div class="border-start ps-4"><span class="text-muted small fw-bold text-uppercase">Blocked</span><h5 class="fw-bold text-danger mb-0"><?php echo $total_blocked; ?></h5></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($time_slots as $session_name => $slots) { 
            // Determine icon for session
            if ($session_name === 'Morning') $icon = '<i class="bi bi-sunrise text-warning me-2"></i>';
            elseif ($session_name === 'Afternoon') $icon = '<i class="bi bi-sun text-danger me-2"></i>';
            else $icon = '<i class="bi bi-moon-stars text-primary me-2"></i>';
        ?>
        <div class="col-xl-4 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                    <h5 class="fw-bold text-dark mb-0"><?php echo $icon . $session_name; ?> Session</h5>
                </div>
                <div class="card-body px-4 pt-2 pb-4">
                    <div class="d-flex flex-column gap-3">
                        
                        <?php 
                        foreach ($slots as $time) { 
                            $time_24hr = date('H:i', strtotime($time));

                            if (array_key_exists($time_24hr, $booked_slots)) {
                                $appointment = $booked_slots[$time_24hr];
                                $is_pending = ($appointment['status'] == 'Pending');
                                $card_class = $is_pending ? 'slot-pending' : 'slot-booked';
                        ?>
                                <div class="card slot-card <?php echo $card_class; ?> border-top-0 border-bottom-0 border-end-0 shadow-sm p-3 rounded-3">
                                    <div class="d-flex align-items-center">
                                        <div class="time-badge badge bg-dark text-white p-2 rounded-3 me-3 fs-6"><?php echo $time; ?></div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="fw-bold mb-0 text-dark"><?php echo $appointment['patient_name']; ?></h6>
                                                <?php if($is_pending) { echo '<span class="badge bg-warning text-dark small rounded-pill">Pending</span>'; } else { echo '<span class="badge bg-primary bg-opacity-10 text-primary small rounded-pill">Confirmed</span>'; } ?>
                                            </div>
                                            <p class="text-muted small mb-0 fst-italic">"<?php echo htmlspecialchars($appointment['reason']); ?>"</p>
                                        </div>
                                    </div>
                                </div>
                        <?php } elseif (in_array($time_24hr, $blocked_slots_array)) { ?>
                                <div class="card slot-card slot-blocked border-top-0 border-bottom-0 border-end-0 p-3 rounded-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="time-badge badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 p-2 rounded-3 me-3 fs-6"><?php echo $time; ?></div>
                                            <div><h6 class="fw-bold text-danger mb-0">Blocked</h6><small class="text-muted">Unavailable</small></div>
                                        </div>
                                        <form method="POST" class="m-0">
                                            <input type="hidden" name="single_action" value="1">
                                            <input type="hidden" name="selected_date" value="<?php echo $selected_date; ?>">
                                            <input type="hidden" name="slot_time" value="<?php echo date('H:i:s', strtotime($time)); ?>">
                                            <button type="submit" name="unblock_slot" class="btn btn-sm btn-outline-danger rounded-pill px-3">Unblock</button>
                                        </form>
                                    </div>
                                </div>
                        <?php } else { ?>
                                <div class="card slot-card slot-available border-top-0 border-bottom-0 border-end-0 p-3 rounded-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="time-badge badge bg-light text-success border border-success border-opacity-25 p-2 rounded-3 me-3 fs-6"><?php echo $time; ?></div>
                                            <div><h6 class="fw-bold text-success mb-0">Available</h6><small class="text-muted">Open for booking</small></div>
                                        </div>
                                        <form method="POST" class="m-0">
                                            <input type="hidden" name="single_action" value="1">
                                            <input type="hidden" name="selected_date" value="<?php echo $selected_date; ?>">
                                            <input type="hidden" name="slot_time" value="<?php echo date('H:i:s', strtotime($time)); ?>">
                                            <button type="submit" name="block_slot" class="btn btn-sm btn-light border rounded-pill px-3 text-muted">Block</button>
                                        </form>
                                    </div>
                                </div>
                        <?php } } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<div class="modal fade" id="bulkManageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                <div>
                    <h4 class="fw-bolder text-dark mb-1">Manage Availability</h4>
                    <p class="text-muted small mb-0">Select multiple slots to quickly block or free up your schedule.</p>
                </div>
                <button type="button" class="btn-close bg-light rounded-circle p-2 shadow-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST">
                <div class="modal-body p-4">
                    <div class="bg-light p-1 rounded-pill d-flex mb-4 position-relative shadow-sm border">
                        <input type="radio" class="btn-check" name="action" id="actionBlock" value="block" checked>
                        <label class="btn btn-block-toggle flex-fill rounded-pill fw-bold py-2 m-0" for="actionBlock">
                            <i class="bi bi-lock-fill me-1"></i> Block Selected Slots
                        </label>
                        <input type="radio" class="btn-check" name="action" id="actionUnblock" value="unblock">
                        <label class="btn btn-unblock-toggle flex-fill rounded-pill fw-bold py-2 m-0" for="actionUnblock">
                            <i class="bi bi-unlock-fill me-1"></i> Free Up Selected Slots
                        </label>
                    </div>

                    <div class="card border border-light bg-light bg-opacity-50 rounded-4 p-3 mb-4 shadow-sm">
                        <label class="form-label fw-bold text-dark small text-uppercase mb-3">
                            <i class="bi bi-calendar-range me-2 text-primary"></i>Date Range
                        </label>
                        <div class="row g-3">
                            <div class="col-6 position-relative">
                                <label class="small text-muted mb-1 fw-semibold">From</label>
                                <input type="date" name="start_date" class="form-control form-control-lg border-0 shadow-sm rounded-3 text-dark" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-6 position-relative">
                                <label class="small text-muted mb-1 fw-semibold">To</label>
                                <input type="date" name="end_date" class="form-control form-control-lg border-0 shadow-sm rounded-3 text-dark" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="card border border-light bg-white rounded-4 p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <label class="form-label fw-bold text-dark small text-uppercase mb-0">
                                <i class="bi bi-clock-history me-2 text-primary"></i>Target Time Slots
                            </label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="selectAllToggle" checked style="cursor: pointer;">
                                <label class="form-check-label small fw-bold text-muted" for="selectAllToggle">Select All</label>
                            </div>
                        </div>
                        <div class="row g-2">
                            <?php 
                            foreach($time_slots as $session => $slots) { 
                                $color = 'text-primary';
                                if($session=='Morning') $color = 'text-warning';
                                elseif($session=='Afternoon') $color = 'text-danger';
                                
                                echo '<div class="col-12 mt-2"><small class="text-muted fw-bold mb-1 d-block"><i class="bi bi-circle-fill '.$color.' me-1" style="font-size: 0.5rem;"></i>'.$session.' Sessions</small></div>';
                                
                                foreach($slots as $t) {
                                    $val = date('H:i:s', strtotime($t)); 
                                    $unique_id = "mslot_" . str_replace(':', '', $val);
                            ?>
                                <div class="col-4 col-sm-3 col-md-2">
                                    <input type="checkbox" class="btn-check slot-checkbox" name="time_slots[]" id="<?php echo $unique_id; ?>" value="<?php echo $val; ?>" checked>
                                    <label class="btn btn-outline-secondary w-100 rounded-3 p-2 slot-pill-label" for="<?php echo $unique_id; ?>">
                                        <?php echo $t; ?>
                                    </label>
                                </div>
                            <?php } } ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 pt-0 pb-4 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted border shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="bulk_action" class="btn btn-dark rounded-pill px-5 fw-bold shadow-sm">Apply Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('selectAllToggle').addEventListener('change', function() {
        const isChecked = this.checked;
        const checkboxes = document.querySelectorAll('.slot-checkbox');
        checkboxes.forEach(function(checkbox) { checkbox.checked = isChecked; });
    });
    const checkboxes = document.querySelectorAll('.slot-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.slot-checkbox:checked').length === checkboxes.length;
            document.getElementById('selectAllToggle').checked = allChecked;
        });
    });
</script>

<?php include "../includes/footer.php"; ?>