<?php
session_start();
include "../includes/db.php";

// Protect admin page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

/* --- ACTION 1: Hide/Show Doctor (Toggle Status) --- */
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    mysqli_query($conn, "UPDATE doctors SET status = IF(status = 1, 0, 1) WHERE id='$id'");
    header("Location: doctors.php");
    exit;
}

/* --- ACTION 2: Add New Doctor (Now handled via Modal) --- */
if (isset($_POST['add_doctor'])) {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $spec  = mysqli_real_escape_string($conn, $_POST['specialization']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM doctors WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $message = "<div class='alert alert-danger alert-dismissible fade show'>
                        Email already exists!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    } else {
        $query = "INSERT INTO doctors (name, specialization, email, phone, password, status)
                  VALUES ('$name', '$spec', '$email', '$phone', '$password', 1)";
        if(mysqli_query($conn, $query)) {
            $message = "<div class='alert alert-success alert-dismissible fade show'>
                            Doctor added successfully!
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>";
        }
    }
}

/* --- ACTION 3: Update Existing Doctor --- */
if (isset($_POST['update_doctor'])) {
    $id    = intval($_POST['id']);
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $spec  = mysqli_real_escape_string($conn, $_POST['specialization']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $query = "UPDATE doctors SET name='$name', specialization='$spec', email='$email', phone='$phone' WHERE id='$id'";
    
    if (mysqli_query($conn, $query)) {
        if (!empty($_POST['password'])) {
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE doctors SET password='$pass' WHERE id='$id'");
        }
        $message = "<div class='alert alert-success alert-dismissible fade show'>
                        Doctor details updated!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    } else {
        $message = "<div class='alert alert-danger'>Update failed: " . mysqli_error($conn) . "</div>";
    }
}

/* Fetch doctors */
$result = mysqli_query($conn, "SELECT * FROM doctors ORDER BY id DESC");
$total_doctors = mysqli_num_rows($result);
?>

<?php include "../includes/admin-header.php"; ?>

<div class="container">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Medical Staff</h3>
            <p class="text-muted mb-0">Manage doctor accounts and visibility</p>
        </div>
        
        <div class="d-flex gap-3 align-items-center">
            <span class="badge bg-white text-dark border px-3 py-2 shadow-sm rounded-pill">
                Total: <?php echo $total_doctors; ?>
            </span>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-lg me-2"></i>New Doctor
            </button>
        </div>
    </div>

    <?php echo $message; ?>

    <div class="card shadow border-0">
        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-primary fw-bold">Doctor Directory</h6>
            <div style="width: 300px;">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="doctorSearch" class="form-control bg-light border-start-0" placeholder="Search by name or spec...">
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Profile</th>
                        <th>Contact</th>
                        <th>Visibility</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="doctorTableBody">
                <?php 
                if ($total_doctors > 0) {
                    $imgIndex = 1;
                    while ($row = mysqli_fetch_assoc($result)) { 
                        $img = "../assets/images/doctors/doc" . (($imgIndex % 5) + 1) . ".png";
                        $imgIndex++;
                ?>
                    <tr class="doctor-row">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo $img; ?>" class="rounded-circle me-3 border" width="45" height="45" style="object-fit:cover;">
                                <div>
                                    <div class="fw-bold doctor-name"><?php echo $row['name']; ?></div>
                                    <small class="text-muted doctor-spec"><?php echo $row['specialization']; ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small class="d-block text-muted"><i class="bi bi-envelope me-1"></i> <?php echo $row['email']; ?></small>
                            <small class="d-block text-muted"><i class="bi bi-telephone me-1"></i> <?php echo $row['phone']; ?></small>
                        </td>
                        <td>
                            <?php if($row['status'] == 1) { ?>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">
                                    <i class="bi bi-eye-fill me-1"></i> Visible
                                </span>
                            <?php } else { ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2">
                                    <i class="bi bi-eye-slash-fill me-1"></i> Hidden
                                </span>
                            <?php } ?>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary rounded-pill me-1 edit-btn px-3"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="<?php echo $row['id']; ?>"
                                    data-name="<?php echo $row['name']; ?>"
                                    data-spec="<?php echo htmlspecialchars($row['specialization']); ?>"
                                    data-email="<?php echo $row['email']; ?>"
                                    data-phone="<?php echo $row['phone']; ?>">
                                <i class="bi bi-pencil-fill me-1"></i> Edit
                            </button>

                            <?php if($row['status'] == 1) { ?>
                                <a href="?toggle=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3" title="Hide Doctor">
                                    <i class="bi bi-eye-slash-fill me-1"></i> Hide
                                </a>
                            <?php } else { ?>
                                <a href="?toggle=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success rounded-pill px-3" title="Show Doctor">
                                    <i class="bi bi-eye-fill me-1"></i> Show
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } } else { ?>
                    <tr><td colspan="4" class="text-center py-5 text-muted">No doctors found.</td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Register New Doctor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Dr. John Doe" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Specialization</label>
                        <select name="specialization" class="form-select" required>
                            <option value="">Select Department...</option>
                            <option value="General Medicine (General Physician)">General Medicine (General Physician)</option>
                            <option value="Cardiologist (Heart Specialist)">Cardiologist (Heart Specialist)</option>
                            <option value="Dermatologist (Skin Specialist)">Dermatologist (Skin Specialist)</option>
                            <option value="Pediatrician (Child Specialist)">Pediatrician (Child Specialist)</option>
                            <option value="Neurologist (Brain Specialist)">Neurologist (Brain Specialist)</option>
                            <option value="Gynecologist (Women's Health)">Gynecologist (Women's Health)</option>
                            <option value="Orthopedic (Bone Specialist)">Orthopedic (Bone Specialist)</option>
                            <option value="ENT Specialist (Ear, Nose, Throat)">ENT Specialist (Ear, Nose, Throat)</option>
                            <option value="Dentist (Teeth Specialist)">Dentist (Teeth Specialist)</option>
                            <option value="Physiotherapist (Physical Therapy)">Physiotherapist (Physical Therapy)</option>
                            <option value="Psychiatrist (Mental Health)">Psychiatrist (Mental Health)</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-uppercase text-muted">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-uppercase text-muted">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Password</label>
                        <input type="text" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_doctor" class="btn btn-primary fw-bold">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Edit Doctor Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="edit-id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Specialization</label>
                        <select name="specialization" id="edit-spec" class="form-select" required>
                            <option value="General Medicine (General Physician)">General Medicine (General Physician)</option>
                            <option value="Cardiologist (Heart Specialist)">Cardiologist (Heart Specialist)</option>
                            <option value="Dermatologist (Skin Specialist)">Dermatologist (Skin Specialist)</option>
                            <option value="Pediatrician (Child Specialist)">Pediatrician (Child Specialist)</option>
                            <option value="Neurologist (Brain Specialist)">Neurologist (Brain Specialist)</option>
                            <option value="Gynecologist (Women's Health)">Gynecologist (Women's Health)</option>
                            <option value="Orthopedic (Bone Specialist)">Orthopedic (Bone Specialist)</option>
                            <option value="ENT Specialist (Ear, Nose, Throat)">ENT Specialist (Ear, Nose, Throat)</option>
                            <option value="Dentist (Teeth Specialist)">Dentist (Teeth Specialist)</option>
                            <option value="Physiotherapist (Physical Therapy)">Physiotherapist (Physical Therapy)</option>
                            <option value="Psychiatrist (Mental Health)">Psychiatrist (Mental Health)</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" id="edit-email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <input type="text" name="phone" id="edit-phone" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">New Password <small>(Leave blank to keep current)</small></label>
                        <input type="text" name="password" class="form-control" placeholder="Enter only if changing">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_doctor" class="btn btn-dark fw-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Live Search Logic
    document.getElementById('doctorSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('.doctor-row');
        
        rows.forEach(row => {
            let name = row.querySelector('.doctor-name').innerText.toLowerCase();
            let spec = row.querySelector('.doctor-spec').innerText.toLowerCase();
            if (name.includes(filter) || spec.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Populate Edit Modal
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit-id').value = this.getAttribute('data-id');
                document.getElementById('edit-name').value = this.getAttribute('data-name');
                document.getElementById('edit-spec').value = this.getAttribute('data-spec');
                document.getElementById('edit-email').value = this.getAttribute('data-email');
                document.getElementById('edit-phone').value = this.getAttribute('data-phone');
            });
        });
    });
</script>

<?php include "../includes/footer.php"; ?>