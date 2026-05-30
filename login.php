<?php
session_start();
include("config/db.php");

$error = "";

if (isset($_POST['login'])) {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepared statement - no SQL injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM dbproj_users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Secure password verify
        if (password_verify($password, $row['password'])) {

            $_SESSION['user_id']  = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role'];

            if ($row['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($row['role'] == 'seller') {
                header("Location: user/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        }
    }
    $error = "Invalid email or password.";
}
?>

<?php include("includes/header.php"); ?>

<div class="row justify-content-center">
<div class="col-md-5">
<div class="card p-4">

    <h2 class="text-center mb-3"><i class="bi bi-box-arrow-in-right"></i> Login</h2>

    <div id="js-error"></div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateLogin()">

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control bg-dark text-white border-secondary" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control bg-dark text-white border-secondary" required>
        </div>

        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>

    </form>

    <p class="text-center mt-3 text-muted">No account? <a href="register.php">Register here</a></p>

    <div class="mt-3 p-2 rounded" style="background:#1a1a2e; font-size:0.8rem;">
        <strong>Test Accounts:</strong><br>
        Admin: admin@shopsphere.com / password<br>
        Seller: seller@shopsphere.com / password<br>
        Customer: customer@shopsphere.com / password
    </div>

</div>
</div>
</div>

<script src="assets/js/validate.js"></script>
<?php include("includes/footer.php"); ?>