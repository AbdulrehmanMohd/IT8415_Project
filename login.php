<?php
session_start();
include("config/db.php");

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $query = "SELECT * FROM dbproj_users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }

    } else {
        $error = "Invalid email or password";
    }
}
?>

<?php include("includes/header.php"); ?>

<div class="row justify-content-center">
    <div class="col-md-5">

        <div class="card p-4">

            <h2 class="text-center mb-3">Login</h2>

            <?php if (isset($error)) { ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php } ?>

            <form method="POST">

                <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>

                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

                <button type="submit" name="login" class="btn btn-primary w-100">
                    Login
                </button>

            </form>

            <p class="text-center mt-3">
                <a href="register.php">Create an account</a>
            </p>

        </div>

    </div>
</div>

<?php include("includes/footer.php"); ?>