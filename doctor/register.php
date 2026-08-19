<?php
include "../includes/db.php";

$message = "";

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM doctors WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $message = "<div class='alert alert-danger'>Email already registered!</div>";
    } else {
        $query = "INSERT INTO doctors (name, email, phone, specialization, password, status) 
                  VALUES ('$name', '$email', '$phone', '$specialization', '$password', 0)";

        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert alert-success'>Application submitted! Please wait for Admin approval. <a href='login.php'>Login here</a></div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor Registration | Hospital Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/react-theme.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow p-4 border-0">
                <h3 class="text-center mb-4 fw-bold">Doctor Registration</h3>
                <?php echo $message; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Dr. John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Specialization</label>
                        <select name="specialization" class="form-control" required>
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
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary w-100 py-2">Apply to Join</button>
                </form>
                <div class="text-center mt-3">
                    <small>Already registered? <a href="login.php">Login here</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../includes/footer.php"; ?>