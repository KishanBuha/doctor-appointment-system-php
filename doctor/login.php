<?php
session_start();
include "../includes/db.php";

$message = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Added status check to ensure the doctor is approved/active
    $query = "SELECT * FROM doctors WHERE email='$email' AND status=1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['doctor_id'] = $row['id'];
            $_SESSION['doctor_name'] = $row['name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "Account not found or not yet approved by Admin.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Doctor Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/react-theme.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow p-4 border-0">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-dark">Doctor Login</h3>
                    <p class="text-muted">Access your professional dashboard</p>
                </div>

                <?php if ($message != "") { ?>
                    <div class="alert alert-danger text-center">
                        <?php echo $message; ?>
                    </div>
                <?php } ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="doctor@hospital.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" name="login" class="btn btn-dark w-100 py-2 shadow-sm">
                        Login to Dashboard
                    </button>
                    </form>
                <div class="text-center mt-4">
                    <small>New doctor? <a href="register.php">Apply for an account</a></small>
                </div>
                
                <div class="text-center mt-3">
                    <a href="../index.php" class="btn btn-link btn-sm text-secondary text-decoration-none">← Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../includes/footer.php"; ?>