<?php
include "../includes/db.php"; // Only include DB, NOT the header

$message = "";

if (isset($_POST['register'])) {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM patients WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Email already registered!";
    } else {
        $query = "INSERT INTO patients (name, email, phone, password)
                  VALUES ('$name', '$email', '$phone', '$password')";

        if (mysqli_query($conn, $query)) {
            $message = "Registration successful. <a href='login.php'>Login here</a>";
        } else {
            $message = "Something went wrong!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patient Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/react-theme.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow p-4 border-0">
                    <h3 class="text-center mb-3 fw-bold text-primary">Patient Registration</h3>
                    
                    <?php if ($message != "") { ?>
                        <div class="alert alert-info text-center"><?php echo $message; ?></div>
                    <?php } ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
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
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <small>Already registered? <a href="login.php">Login here</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>