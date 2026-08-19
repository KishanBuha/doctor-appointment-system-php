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
$message = "";

// Handle Fee Update
if (isset($_POST['update_fee'])) {
    // Sanitize and convert to a secure float value
    $new_fee = floatval($_POST['consultation_fee']);
    
    $update_query = "UPDATE doctors SET consultation_fee = '$new_fee' WHERE id = '$doctor_id'";
    if (mysqli_query($conn, $update_query)) {
        $message = "<div class='alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4'>
                        <i class='bi bi-check-circle-fill me-2'></i> 
                        <strong>Success!</strong> Your consultation fee has been updated to ₹" . number_format($new_fee, 2) . ".
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    } else {
        $message = "<div class='alert alert-danger shadow-sm border-0 rounded-4'>Error updating fee.</div>";
    }
}

// Fetch the current fee
$query = "SELECT consultation_fee FROM doctors WHERE id = '$doctor_id'";
$result = mysqli_query($conn, $query);
$doctor_data = mysqli_fetch_assoc($result);
$current_fee = $doctor_data['consultation_fee'];

include "../includes/doctor-header.php"; 
?>

<div class="container-fluid pb-5">
    <div class="row mb-4 align-items-end">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Fee Structure</h3>
            <p class="text-muted mb-0">Manage your consultation pricing for patients.</p>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-lg-6 col-md-8">
            
            <?php echo $message; ?>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 text-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex p-3 mb-3 shadow-sm">
                        <i class="bi bi-wallet2 fs-2"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Consultation Fee</h4>
                    <p class="text-muted small">This is the base price patients will see when booking an appointment with you.</p>
                </div>
                
                <div class="card-body p-4 pt-2">
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">Current Fee per visit</label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0 text-dark fw-bold">₹</span>
                                <input type="number" step="0.01" min="0" name="consultation_fee" 
                                       class="form-control bg-light border-0 fw-bold fs-4 text-dark" 
                                       value="<?php echo number_format($current_fee, 2, '.', ''); ?>" required>
                            </div>
                            <div class="form-text mt-2 small text-muted"><i class="bi bi-info-circle me-1"></i>You can update this at any time. Changes will apply to new bookings only.</div>
                        </div>

                        <button type="submit" name="update_fee" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm fs-6">
                            Save Changes <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
