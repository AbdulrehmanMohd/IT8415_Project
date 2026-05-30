<?php
session_start();
include("config/db.php");

if (isset($_POST['register'])) {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // simple for assignment

    // check if email exists
    $check = mysqli_query($conn, "SELECT * FROM dbproj_users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists!";
    } else {

        $sql = "INSERT INTO dbproj_users (username, email, password, role)
                VALUES ('$username', '$email', '$password', 'customer')";

        mysqli_query($conn, $sql);

        header("Location: index.php");
            exit();
    }
}
?>

<h2>Register</h2>

<form method="POST">

    <input type="text" name="username" placeholder="Username" required><br><br>

    <input type="email" name="email" placeholder="Email" required><br><br>

    <input type="password" name="password" placeholder="Password" required><br><br>

    <button type="submit" name="register">Register</button>

</form>

<?php if (isset($error)) echo $error; ?>