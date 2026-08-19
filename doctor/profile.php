<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../includes/db.php";

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit;
}

$doctor_id = $_SESSION['doctor_id'];
$message = "";

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

// Handle Profile Update
if (isset($_POST['update_profile'])) {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $spec  = mysqli_real_escape_string($conn, $_POST['specialization']);
    $pass  = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if email already exists for another doctor
    $check_email = mysqli_query($conn, "SELECT id FROM doctors WHERE email='$email' AND id != '$doctor_id'");
    
    if (mysqli_num_rows($check_email) > 0) {
        $_SESSION['flash_message'] = "<div class='alert alert-danger shadow-sm border-0 rounded-4'>Error: This email is already registered to another doctor.</div>";
        header("Location: profile.php");
        exit;
    }

    $image_query_append = "";

    // Handle File Upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $file_name = $_FILES['profile_image']['name'];
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        
        $ext_array = explode('.', $file_name);
        $file_ext = strtolower(end($ext_array));

        if (in_array($file_ext, $allowed_ext)) {
            // Create a unique filename to prevent overwriting
            $new_img_name = "doc_" . $doctor_id . "_" . time() . "." . $file_ext;
            $upload_path = "../assets/images/doctors/" . $new_img_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $image_query_append = ", profile_image='$new_img_name'";
            } else {
                $_SESSION['flash_message'] = "<div class='alert alert-danger shadow-sm border-0 rounded-4'>Error uploading the image to the server.</div>";
                header("Location: profile.php");
                exit;
            }
        } else {
            $_SESSION['flash_message'] = "<div class='alert alert-danger shadow-sm border-0 rounded-4'>Invalid image format. Please upload JPG, PNG, or WEBP files only.</div>";
            header("Location: profile.php");
            exit;
        }
    }

    // Update query (with or without password)
    if (!empty($pass)) {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        $query = "UPDATE doctors SET name='$name', email='$email', phone='$phone', specialization='$spec', password='$hashed_pass' $image_query_append WHERE id='$doctor_id'";
    } else {
        $query = "UPDATE doctors SET name='$name', email='$email', phone='$phone', specialization='$spec' $image_query_append WHERE id='$doctor_id'";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['doctor_name'] = $name; 
        $_SESSION['flash_message'] = "<div class='alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4'>
                        <i class='bi bi-check-circle-fill me-2'></i> Profile updated successfully!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    } else {
        $_SESSION['flash_message'] = "<div class='alert alert-danger shadow-sm border-0 rounded-4'>Error updating profile.</div>";
    }
    
    header("Location: profile.php");
    exit;
}

// Fetch current doctor data
$doc_query = mysqli_query($conn, "SELECT * FROM doctors WHERE id='$doctor_id'");
$doctor = mysqli_fetch_assoc($doc_query);

// Determine current profile image
$current_image = !empty($doctor['profile_image']) ? "../assets/images/doctors/" . $doctor['profile_image'] : "../assets/images/doctors/doc1.png";

include "../includes/doctor-header.php"; 
?>

<div class="container-fluid pb-5">
    <div class="row mb-4 align-items-end">
        <div class="col-12">
            <h3 class="fw-bold text-dark mb-1">My Profile</h3>
            <p class="text-muted mb-0">Manage your personal information and credentials.</p>
        </div>
    </div>

    <div class="row justify-content-center mt-3">
        <div class="col-lg-8">
            <?php echo $message; ?>
            
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex p-3 me-3">
                        <i class="bi bi-person-badge fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Personal Details</h5>
                        <small class="text-muted">Update your clinic directory listing.</small>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <div class="position-relative me-4">
                                <img src="<?php echo $current_image; ?>" alt="Profile" class="rounded-circle shadow-sm border border-3 border-white" style="width: 100px; height: 100px; object-fit: cover; background: #f8fafc;">
                                <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1 border border-2 border-white" style="line-height: 1; transform: translate(20%, -10%);">
                                    <i class="bi bi-camera-fill small"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label fw-bold text-dark small text-uppercase">Profile Photo</label>
                                <input type="file" name="profile_image" class="form-control bg-light border-0" accept="image/png, image/jpeg, image/jpg, image/webp">
                                <small class="text-muted d-block mt-1">Recommended size: Square (JPG, PNG, WEBP max 2MB).</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Full Name</label>
                                <input type="text" name="name" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($doctor['name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Specialization</label>
                                <input type="text" name="specialization" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Email Address</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($doctor['email']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Phone Number</label>
                                <input type="text" name="phone" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($doctor['phone']); ?>" required>
                            </div>
                        </div>

                        <hr class="text-muted opacity-25 mb-4">
                        
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock text-primary me-2"></i>Security</h6>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">New Password</label>
                            <input type="password" name="password" class="form-control bg-light border-0 py-2" placeholder="Leave blank to keep current password">
                            <div class="form-text mt-1 small">Only fill this if you want to change your password.</div>
                        </div>

                        <div class="text-end">
                            <button type="submit" name="update_profile" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include "../includes/footer.php"; 
ob_end_flush();
?>