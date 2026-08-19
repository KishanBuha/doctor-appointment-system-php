<?php
session_start();
include "../includes/db.php";
$message = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $query = "SELECT * FROM patients WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['patient_id'] = $row['id'];
            header("Location: dashboard.php");
            exit;
        } else { $message = "Invalid password!"; }
    } else { $message = "User not found!"; }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patient Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/react-theme.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow p-4 border-0">
                    <h3 class="text-center mb-4 fw-bold text-primary">Patient Login</h3>
                    <?php if($message) echo "<div class='alert alert-danger'>$message</div>"; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100">Sign In</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <span>New here? </span><a href="register.php" class="fw-bold">Register Now</a>
                    </div>

                    <div class="text-center mt-3">
                        <a href="../index.php" class="btn btn-link btn-sm text-secondary text-decoration-none">← Back to Home</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>