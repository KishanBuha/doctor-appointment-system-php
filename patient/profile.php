<?php
ob_start(); // Prevents "Headers already sent" errors
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../includes/db.php";

// Protect patient page
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['patient_id'];
$message = "";

// Display flash messages (PRG Pattern)
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

// Handle Profile Update Submission
if (isset($_POST['update_profile'])) {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pass  = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if the new email is already taken by another patient
    $check_email = "SELECT id FROM patients WHERE email='$email' AND id != '$patient_id'";
    $email_result = mysqli_query($conn, $check_email);
    
    if (mysqli_num_rows($email_result) > 0) {
        $_SESSION['flash_message'] = "<div class='alert alert-danger shadow-sm border-0 rounded-4 mb-4'><i class='bi bi-exclamation-triangle-fill me-2'></i> Error: This email address is already registered to another account.</div>";
    } else {
        // Update Query (Handle password conditionally)
        if (!empty($pass)) {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            $query = "UPDATE patients SET name='$name', email='$email', phone='$phone', password='$hashed_pass' WHERE id='$patient_id'";
        } else {
            $query = "UPDATE patients SET name='$name', email='$email', phone='$phone' WHERE id='$patient_id'";
        }

        if (mysqli_query($conn, $query)) {
            $_SESSION['patient_name'] = $name; // Update session name so header changes instantly
            $_SESSION['flash_message'] = "<div class='alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4'>
                            <i class='bi bi-check-circle-fill me-2'></i> Your profile has been updated successfully!
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>";
        } else {
            $_SESSION['flash_message'] = "<div class='alert alert-danger shadow-sm border-0 rounded-4 mb-4'>Error updating profile: " . mysqli_error($conn) . "</div>";
        }
    }
    
    // Redirect to clear POST data
    header("Location: profile.php");
    exit;
}

// Fetch current patient data to pre-fill the form
$query = "SELECT * FROM patients WHERE id='$patient_id'";
$result = mysqli_query($conn, $query);
$patient = mysqli_fetch_assoc($result);

include "../includes/patient-header.php"; 
?>

<style>
    /* Premium Profile UI */
    .profile-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .profile-header-banner {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 3rem 2rem;
        position: relative;
    }
    .profile-header-banner::after {
        content: '\F4E1'; /* Bootstrap Person Vcard Icon */
        font-family: "bootstrap-icons";
        position: absolute;
        right: 10px;
        bottom: -20px;
        font-size: 8rem;
        color: rgba(255, 255, 255, 0.05);
        transform: rotate(-10deg);
    }
    .profile-avatar-placeholder {
        width: 100px;
        height: 100px;
        background: #ffffff;
        color: #1e293b;
        font-size: 2.5rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-top: -50px;
        border: 4px solid #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        position: relative;
        z-index: 2;
    }
    
    /* Form Inputs */
    .form-floating > .form-control {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .form-floating > .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        background-color: #ffffff;
    }
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: #2563eb;
        font-weight: 600;
    }
    .btn-gradient {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        color: white;
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        color: white;
    }
</style>

<div class="container-fluid pb-5 pt-3">
    
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h3 class="fw-bolder text-dark mb-1">My Account</h3>
            <p class="text-muted mb-0">Manage your personal details and security settings.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            
            <?php echo $message; ?>

            <div class="profile-card">
                <div class="profile-header-banner text-center text-white">
                    <h4 class="fw-bolder mb-1"><?php echo htmlspecialchars($patient['name']); ?></h4>
                    <p class="mb-0 opacity-75 small"><i class="bi bi-envelope me-1"></i> <?php echo htmlspecialchars($patient['email']); ?></p>
                </div>

                <div class="card-body p-4 pt-0 px-md-5">
                    
                    <div class="d-flex justify-content-center mb-4">
                        <div class="profile-avatar-placeholder">
                            <?php 
                                // Get first letter of the name
                                echo strtoupper(substr($patient['name'], 0, 1)); 
                            ?>
                        </div>
                    </div>

                    <form method="POST" id="profileForm">
                        
                        <h6 class="fw-bold text-dark mb-3 text-uppercase small letter-spacing-1"><i class="bi bi-person-lines-fill text-primary me-2"></i>Personal Information</h6>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="nameInput" name="name" value="<?php echo htmlspecialchars($patient['name']); ?>" placeholder="Full Name" required>
                                    <label for="nameInput">Full Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="emailInput" name="email" value="<?php echo htmlspecialchars($patient['email']); ?>" placeholder="Email Address" required>
                                    <label for="emailInput">Email Address</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="phoneInput" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>" placeholder="Phone Number" required>
                                    <label for="phoneInput">Phone Number</label>
                                </div>
                            </div>
                        </div>

                        <hr class="text-muted opacity-10 my-4">

                        <h6 class="fw-bold text-dark mb-3 text-uppercase small letter-spacing-1"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Security & Authentication</h6>
                        
                        <div class="mb-4">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="passwordInput" name="password" placeholder="New Password">
                                <label for="passwordInput">New Password (Optional)</label>
                            </div>
                            <div class="form-text mt-2 small text-muted">
                                <i class="bi bi-info-circle me-1"></i> Leave this field blank if you do not wish to change your current password.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5 mb-2">
                            <button type="submit" name="update_profile" id="submitBtn" class="btn btn-gradient px-5 py-3 fw-bold fs-6 d-flex align-items-center">
                                Save Changes <i class="bi bi-check2-circle ms-2 fs-5"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="text-center mt-4 text-muted small">
                Account created on: <?php echo date('F d, Y', strtotime($patient['created_at'])); ?>
            </div>

        </div>
    </div>

</div>

<script>
    // PRG Pattern safeguard: clear POST data from browser history
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

    // Loading state for button
    document.getElementById('profileForm').addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        btn.innerHTML = 'Saving... <span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>';
        btn.disabled = true;
    });
</script>

<?php 
include "../includes/footer.php"; 
ob_end_flush(); 
?>