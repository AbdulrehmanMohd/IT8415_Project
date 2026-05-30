<?php
session_start();
include("config/db.php");

$error = "";
$success = "";

if (isset($_POST['register'])) {

    // Server-side validation
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $role     = $_POST['role'] ?? 'customer';

    if (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if email exists (prepared statement)
        $stmt = mysqli_prepare($conn, "SELECT user_id FROM dbproj_users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "This email is already registered.";
        } else {
            // Secure password hash
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            $stmt2 = mysqli_prepare($conn, "INSERT INTO dbproj_users (username, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, "ssss", $username, $email, $hashed, $role);
            mysqli_stmt_execute($stmt2);

            $success = "Account created! You can now <a href='login.php'>login</a>.";
        }
    }
}
?>

<?php include("includes/header.php"); ?>

<div class="row justify-content-center">
<div class="col-md-5">
<div class="card p-4">

    <h2 class="text-center mb-3"><i class="bi bi-person-plus"></i> Register</h2>

    <div id="js-error"></div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateRegister()">

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control bg-dark text-white border-secondary" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control bg-dark text-white border-secondary" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control bg-dark text-white border-secondary" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control bg-dark text-white border-secondary" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Register as</label>
            <select name="role" class="form-select bg-dark text-white border-secondary">
                <option value="customer">Customer</option>
                <option value="seller">Seller / Creator</option>
            </select>
        </div>

        <button type="submit" name="register" class="btn btn-primary w-100">Create Account</button>

    </form>

    <p class="text-center mt-3 text-muted">Already have an account? <a href="login.php">Login</a></p>

</div>
</div>
</div>

<script src="assets/js/validate.js"></script>
<?php include("includes/footer.php"); ?>