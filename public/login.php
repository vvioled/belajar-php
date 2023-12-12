<?php
session_start();

require "../config.php";
require "../functions.php";

if (isset($_SESSION["user_id"])) {
    $st = $pdo->prepare("SELECT id FROM users WHERE id = :id");
    $st->execute(["id" => $_SESSION["user_id"]]);
    if ($st->fetch()) {
        header("Location: profile.php");
    }
}

function bad_requests(): void
{
    http_response_code(400);
    echo "<h1>Error 400 Bad Request!</h1>";
    exit();
}

$username_length_invalid = false;
$password_length_invalid = false;
$users_unvailable = false;
$password_unvailable = false;


if (isset($_POST['login'])) {

    $bp = new BelajarPHP();

    if (!$bp->validate_input(["username", "password"])) {
        bad_requests();
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!$bp->length_is_valid($username, 4)) {
        $username_length_invalid = true;
        goto out;
    }
    if (!$bp->length_is_valid($password, 6)) {
        $password_length_invalid = true;
        goto out;
    }

    if (!($bp->check_username_exist($username) || $bp->check_email_exist($username))) {
        $users_unvailable = true;
        goto out;
    }
    if (!password_verify($password, $bp->get("password", $username))) {
        $password_unvailable = true;
        goto out;
    }

    $_SESSION['user_id'] = $bp->get("id", $username);
    header("Location: profile.php");
}

out:
?>

<!DOCTYPE html>

<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <style>
        form {
            max-width: 400px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="vh-100 w-100 d-flex justify-content-center align-items-center">
            <form action="" method="post" class="card px-3 shadow w-100">
                <h1 class="mt-3 fw-bold text-uppercase text-center">Login</h1>
                <div class="form-group pb-3">
                    <label for="username" class="fw-semibold mb-2">Username atau email:</label>
                    <input placeholder="Masukkan username atau email" type="text" name="username" class="form-control"
                        required />
                    <?php if ($username_length_invalid) { ?>
                        <span class="text-danger mt-1">Input tidak boleh kurang dari 3</span>
                    <?php } ?>
                    <?php if ($users_unvailable) { ?>
                        <span class="text-danger mt-1">Username atau email tidak tersedia</span>
                    <?php } ?>
                </div>
                <div class="form-group pb-3">
                    <label for="password" class="fw-semibold mb-2">Password:</label>
                    <input placeholder="Masukkan Password" type="password" name="password" class="form-control">
                    <?php if ($password_length_invalid) { ?>
                        <span class="text-danger mt-1">panjang password tidak boleh kurang dari 6</span>
                    <?php } ?>
                    <?php if ($password_unvailable) { ?>
                        <span class="text-danger mt-1">Password salah</span>
                    <?php } ?>
                </div>
                <button type="submit" name="login" class="btn btn-success mb-3">Login</button>
                <span class="text-success text-center mb-3">Tidak punya akun? silahkan <a
                        href="./register.php">daftar</a></span>
            </form>
        </div>
    </div>
</body>

</html>