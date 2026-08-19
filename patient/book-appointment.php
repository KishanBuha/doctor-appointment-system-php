<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../includes/db.php";

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

// ==========================================
// AJAX ENDPOINT: Fetch Unavailable Slots dynamically
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'get_unavailable_slots') {
    header('Content-Type: application/json');
    $doc_id = mysqli_real_escape_string($conn, $_GET['doctor_id']);
    $date   = mysqli_real_escape_string($conn, $_GET['date']);
    
    $unavailable = [];
    
    // 1. Get booked appointments by other patients
    $book_query = "SELECT appointment_time FROM appointments WHERE doctor_id='$doc_id' AND appointment_date='$date' AND status NOT IN ('Cancelled', 'Rejected')";
    $book_res = mysqli_query($conn, $book_query);
    while($row = mysqli_fetch_assoc($book_res)) {
        $unavailable[] = date('H:i', strtotime($row['appointment_time'])); // Format to H:i
    }
    
    // 2. Get explicit blocks placed by the doctor
    $block_query = "SELECT block_time FROM blocked_slots WHERE doctor_id='$doc_id' AND block_date='$date'";
    $block_res = mysqli_query($conn, $block_query);
    while($row = mysqli_fetch_assoc($block_res)) {
        $unavailable[] = date('H:i', strtotime($row['block_time'])); // Format to H:i
    }
    
    echo json_encode(array_unique($unavailable));
    exit;
}
// ==========================================

$message = "";

if (isset($_POST['book'])) {
    $patient_id = $_SESSION['patient_id'];
    $doctor_id  = mysqli_real_escape_string($conn, $_POST['doctor_id']);
    $date       = mysqli_real_escape_string($conn, $_POST['appointment_date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['appointment_time']);
    
    $end_time   = date('H:i', strtotime($start_time . ' +30 minutes')); 
    $reason     = mysqli_real_escape_string($conn, $_POST['reason']);

    // Final security check in case they bypass JS
    $check_query = "SELECT * FROM appointments WHERE doctor_id = '$doctor_id' AND appointment_date = '$date' AND status NOT IN ('Cancelled', 'Rejected') AND ((appointment_time < '$end_time' AND appointment_end_time > '$start_time'))";
    $result = mysqli_query($conn, $check_query);

    $block_check_query = "SELECT * FROM blocked_slots WHERE doctor_id = '$doctor_id' AND block_date = '$date' AND block_time = '$start_time'";
    $block_result = mysqli_query($conn, $block_check_query);

    if (mysqli_num_rows($result) > 0 || mysqli_num_rows($block_result) > 0) {
        $message = "<div class='alert alert-warning alert-dismissible fade show shadow-sm border-0 rounded-4'><i class='bi bi-exclamation-triangle-fill me-2'></i> <strong>Slot Unavailable!</strong> This slot is no longer available. Please choose a different time.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $query = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, appointment_end_time, reason, status) VALUES ('$patient_id', '$doctor_id', '$date', '$start_time', '$end_time', '$reason', 'Pending')";
        if (mysqli_query($conn, $query)) {
            $formatted_time = date('h:i A', strtotime($start_time));
            $message = "<div class='alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4'><i class='bi bi-check-circle-fill me-2'></i> Request sent! Time: <strong>$formatted_time</strong>. Please wait for clinic approval.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } else {
            $message = "<div class='alert alert-danger shadow-sm border-0 rounded-4'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Fetch active doctors
$doctors_query = mysqli_query($conn, "SELECT * FROM doctors WHERE status = 1");
$specs_query = mysqli_query($conn, "SELECT DISTINCT specialization FROM doctors WHERE status = 1");

$time_slots = [
    'Morning' => ['09:00 AM', '09:30 AM', '10:00 AM', '10:30 AM', '11:00 AM', '11:30 AM'],
    'Afternoon' => ['12:00 PM', '12:30 PM', '02:00 PM', '02:30 PM', '03:00 PM', '03:30 PM', '04:00 PM', '04:30 PM'],
    'Early Evening' => ['05:00 PM', '05:30 PM', '06:00 PM', '06:30 PM', '07:00 PM', '07:30 PM']
];

include "../includes/patient-header.php"; 
?>

<style>
    .hero-search-banner { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 20px; position: relative; overflow: hidden; }
    .hero-search-banner::before { content: ""; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; }
    .search-pill { background: rgba(255, 255, 255, 0.95); border-radius: 50px; padding: 8px; }
    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.08) !important; }
    .doctor-avatar-container { background-color: #f8fafc; border-right: 1px solid #f1f5f9; }
    .form-control-custom:focus, .form-select-custom:focus { box-shadow: none; border-color: transparent; }
    
    .btn-check:checked + .btn-outline-primary { background-color: #2563eb !important; border-color: #2563eb !important; color: white !important; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3); }
    .slot-label { font-size: 0.85rem; font-weight: 600; transition: all 0.2s ease; background: #f8fafc; border-color: #e2e8f0; color: #475569; }
    .slot-label:hover:not(.slot-disabled) { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; }
    
    .slot-disabled {
        background-color: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
        color: #cbd5e1 !important;
        text-decoration: line-through;
        cursor: not-allowed !important;
        pointer-events: none;
    }
</style>

<div class="container-fluid pb-5">
    
    <?php echo $message; ?>

    <div class="hero-search-banner p-4 p-md-5 mb-5 text-white shadow">
        <div class="position-relative z-1">
            <h2 class="fw-bold mb-2">Find Your Doctor</h2>
            <p class="opacity-75 mb-4 fs-6">Browse our directory of top-tier specialists and book your visit instantly.</p>
            
            <div class="search-pill shadow-sm d-flex flex-column flex-md-row gap-2">
                <div class="input-group border-md-end">
                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control form-control-custom bg-transparent border-0" placeholder="Search doctor by name...">
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-funnel"></i></span>
                    <select id="specFilter" class="form-select form-select-custom bg-transparent border-0 text-muted">
                        <option value="all">All Departments</option>
                        <?php while($s = mysqli_fetch_assoc($specs_query)) { ?>
                            <option value="<?php echo $s['specialization']; ?>"><?php echo $s['specialization']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <h4 class="fw-bold text-dark mb-0">Available Specialists</h4>
        <span class="text-muted small">Select a doctor to view slots</span>
    </div>

    <div class="row g-4" id="doctorGrid">
    <?php
if (mysqli_num_rows($doctors_query) > 0) {
    while ($doc = mysqli_fetch_assoc($doctors_query)) {
        
        // Check if the doctor uploaded a custom profile image
        if (!empty($doc['profile_image']) && file_exists("../assets/images/doctors/" . $doc['profile_image'])) {
            $img = "../assets/images/doctors/" . $doc['profile_image'];
        } else {
            // Fallback to a default placeholder avatar if they haven't uploaded one
            $img = "../assets/images/doctors/doc1.png"; 
        }
        
        $fee = isset($doc['consultation_fee']) && $doc['consultation_fee'] > 0 ? $doc['consultation_fee'] : 0.00;
?>
        
        <div class="col-md-6 col-xl-4 doctor-card" data-name="<?php echo strtolower($doc['name']); ?>" data-spec="<?php echo $doc['specialization']; ?>">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100 hover-lift">
                <div class="row g-0 h-100">
                    <div class="col-4 doctor-avatar-container d-flex flex-column align-items-center justify-content-center p-3">
                        <div class="position-relative mb-2">
                            <img src="<?php echo $img; ?>" class="rounded-circle shadow-sm bg-white" width="80" height="80" style="object-fit:cover; border: 3px solid white;">
                            <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle"></span>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill small" style="font-size: 0.7rem;">Accepting Patients</span>
                    </div>
                    <div class="col-8">
                        <div class="card-body d-flex flex-column h-100 py-3 px-4">
                            <h5 class="fw-bold mb-1 text-dark text-truncate" title="<?php echo $doc['name']; ?>"><?php echo $doc['name']; ?></h5>
                            <p class="text-muted small mb-3 text-uppercase fw-bold opacity-75 text-truncate" style="font-size: 0.75rem;">
                                <i class="bi bi-heart-pulse text-primary me-1"></i> <?php echo $doc['specialization']; ?>
                            </p>
                            
                            <div class="mb-3">
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25 shadow-sm">
                                    <i class="bi bi-currency-rupee"></i><?php echo number_format($fee, 2); ?> / Visit
                                </span>
                            </div>

                            <div class="mt-auto">
                                <button class="btn btn-primary w-100 rounded-pill py-2 fw-bold text-nowrap" data-bs-toggle="modal" data-bs-target="#bookModal<?php echo $doc['id']; ?>">Book Slots <i class="bi bi-calendar-event ms-1"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="bookModal<?php echo $doc['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light border-bottom-0 pb-0 pt-4 px-4 align-items-start">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $img; ?>" class="rounded-circle me-3 shadow-sm border border-white" width="50" height="50" style="object-fit:cover;">
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0">Book with <?php echo $doc['name']; ?></h5>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="text-primary small fw-bold"><?php echo $doc['specialization']; ?></span>
                                    <span class="text-muted small">•</span>
                                    <span class="text-success small fw-bold"><i class="bi bi-currency-rupee"></i><?php echo number_format($fee, 2); ?></span>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close bg-white rounded-circle p-2 shadow-sm" data-bs-dismiss="modal"></button>
                    </div>

                    <form method="POST">
                        <div class="modal-body text-start p-4">
                            <input type="hidden" name="doctor_id" value="<?php echo $doc['id']; ?>">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark small text-uppercase mb-2">1. Select Date <span class="text-danger">*</span></label>
                                <input type="date" name="appointment_date" class="form-control form-control-lg bg-light border-0 shadow-sm rounded-3 dynamic-date-picker" data-docid="<?php echo $doc['id']; ?>" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-bold text-dark small text-uppercase mb-0">2. Select Time Slot <span class="text-danger">*</span></label>
                                    <span class="text-primary small fw-bold prompt-msg-<?php echo $doc['id']; ?>"><i class="bi bi-info-circle me-1"></i>Select a date first</span>
                                </div>
                                <div class="row g-2">
                                    <?php foreach($time_slots as $session => $slots) { ?>
                                        <div class="col-12 mt-3">
                                            <h6 class="text-muted fw-bold small text-uppercase mb-1 border-bottom pb-1"><?php echo $session; ?></h6>
                                        </div>
                                        <?php 
                                        foreach($slots as $slot) {
                                            $slot_val = date('H:i', strtotime($slot));
                                            $unique_id = "slot_" . $doc['id'] . "_" . str_replace(':', '', $slot_val);
                                        ?>
                                        <div class="col-4 col-sm-3 col-md-2">
                                            <input type="radio" class="btn-check slot-radio-<?php echo $doc['id']; ?>" name="appointment_time" id="<?php echo $unique_id; ?>" value="<?php echo $slot_val; ?>" disabled required>
                                            <label class="btn btn-outline-primary w-100 rounded-3 p-2 slot-label slot-label-<?php echo $doc['id']; ?>" for="<?php echo $unique_id; ?>"><?php echo $slot; ?></label>
                                        </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <label class="form-label fw-bold text-dark small text-uppercase mb-2">3. Reason for Visit <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control bg-light border-0 shadow-sm rounded-3" rows="3" placeholder="Briefly describe your symptoms..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top-0 pt-3 pb-3 px-4">
                            <button type="button" class="btn btn-white text-muted fw-bold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="book" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Confirm Booking</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php } } else { echo '<div class="col-12"><div class="alert alert-warning text-center rounded-4 border-0 shadow-sm">No doctors available.</div></div>'; } ?>
    </div>
    
    <div id="noResults" class="text-center py-5 d-none">
        <div class="bg-white rounded-circle d-inline-flex p-4 shadow-sm mb-3"><i class="bi bi-search text-muted fs-1"></i></div>
        <h4 class="text-dark fw-bold">No specialists found</h4>
        <button class="btn btn-outline-primary rounded-pill px-4 mt-2" onclick="resetFilters()">Clear Search</button>
    </div>
</div>

<script>
    // --- AJAX Logic for Dynamic Slot Availability ---
    document.querySelectorAll('.dynamic-date-picker').forEach(picker => {
        picker.addEventListener('change', async function() {
            const selectedDate = this.value;
            const docId = this.getAttribute('data-docid');
            const radios = document.querySelectorAll('.slot-radio-' + docId);
            const labels = document.querySelectorAll('.slot-label-' + docId);
            const promptMsg = document.querySelector('.prompt-msg-' + docId);
            
            // If user clears the date, disable everything again
            if (!selectedDate) {
                radios.forEach(radio => radio.disabled = true);
                labels.forEach(label => label.classList.remove('slot-disabled'));
                if (promptMsg) promptMsg.style.display = 'block';
                return;
            }

            // Hide the 'Select a date' prompt
            if (promptMsg) promptMsg.style.display = 'none';

            // Temporarily enable ALL slots while we fetch data
            radios.forEach(radio => radio.disabled = false);
            labels.forEach(label => label.classList.remove('slot-disabled'));

            try {
                // Call the PHP script silently in the background
                const response = await fetch(`book-appointment.php?action=get_unavailable_slots&doctor_id=${docId}&date=${selectedDate}`);
                const unavailableSlots = await response.json();
                
                // Loop through radio buttons. If their value matches the unavailable list, disable it!
                radios.forEach(radio => {
                    if (unavailableSlots.includes(radio.value)) {
                        radio.disabled = true; // Make unclickable
                        radio.checked = false; // Ensure it's not selected
                        
                        // Add the visual "greyed out" CSS class to the label
                        const label = document.querySelector(`label[for="${radio.id}"]`);
                        if(label) label.classList.add('slot-disabled');
                    }
                });
            } catch (error) {
                console.error("Failed to fetch available slots", error);
            }
        });
    });

    // --- Search Filter Logic ---
    const searchInput = document.getElementById('searchInput');
    const specFilter = document.getElementById('specFilter');
    const cards = document.querySelectorAll('.doctor-card');
    const noResults = document.getElementById('noResults');

    function filterDoctors() {
        const term = searchInput.value.toLowerCase();
        const category = specFilter.value;
        let visibleCount = 0;
        cards.forEach(card => {
            const matchesSearch = card.getAttribute('data-name').includes(term);
            const matchesSpec = category === 'all' || card.getAttribute('data-spec') === category;
            if (matchesSearch && matchesSpec) { card.style.display = ''; visibleCount++; } 
            else { card.style.display = 'none'; }
        });
        noResults.classList.toggle('d-none', visibleCount > 0);
    }
    searchInput.addEventListener('keyup', filterDoctors);
    specFilter.addEventListener('change', filterDoctors);
    function resetFilters() { searchInput.value = ''; specFilter.value = 'all'; filterDoctors(); }
</script>

<?php include "../includes/footer.php"; ?>
