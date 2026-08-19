<?php
// Include database connection
include "../includes/db.php"; 

$message = "";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Securely hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username already exists
        $check = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Username already exists!";
        } else {
            $query = "INSERT INTO admin (username, password) VALUES ('$username', '$hashed_password')";
            if (mysqli_query($conn, $query)) {
                $message = "Admin registered successfully! <a href='login.php'>Login here</a>";
            } else {
                $message = "Registration failed!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/react-theme.css"> 
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow p-4 border-0">
                <h3 class="text-center mb-4 fw-bold">Admin Registration</h3>

                <?php if ($message != "") { ?>
                    <div class="alert alert-info text-center"><?php echo $message; ?></div>
                <?php } ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Create username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Create password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
                    </div>

                    <button type="submit" name="register" class="btn btn-primary w-100 py-2 shadow-sm">
                        Register Admin
                    </button>
                </form>

                <div class="text-center mt-4">
                    <small>Already have an account? <a href="login.php">Back to Login</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../includes/footer.php"; ?>