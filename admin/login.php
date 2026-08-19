<?php
session_start();
include "../includes/db.php";

$message = "";

// Create default admin if not exists (hashed password)
$checkAdmin = mysqli_query($conn, "SELECT * FROM admin");
if (mysqli_num_rows($checkAdmin) == 0) {
    $hashed = password_hash("admin123", PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO admin (username, password) VALUES ('admin', '$hashed')");
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "Admin not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/react-theme.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow p-4 border-0">
                <h3 class="text-center mb-4 fw-bold">Admin Login</h3>

                <?php if ($message != "") { ?>
                    <div class="alert alert-warning text-center">
                        <?php echo $message; ?>
                    </div>
                <?php } ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary w-100 py-2 shadow-sm">
                        Login
                    </button>
                </form>
                <div class="text-center mt-4">
                    <small>Need to create an account? <a href="register.php">Register Admin</a></small>
                </div>
<div class="text-center mt-3">
                    <a href="../index.php" class="btn btn-link btn-sm text-secondary text-decoration-none">← Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../includes/footer.php"; ?>