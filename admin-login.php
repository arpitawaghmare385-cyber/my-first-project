<?php
session_start();
require_once "db.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: admin.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please enter email and password.";
    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password
             FROM admins
             WHERE email = ?
             LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $admin = $result->fetch_assoc();

            if (password_verify(trim($password), $admin["password"])) {

                session_regenerate_id(true);

                $_SESSION["admin_id"] = $admin["id"];
                $_SESSION["admin_name"] = $admin["name"];
                $_SESSION["admin_email"] = $admin["email"];

                header("Location: admin.php");
                exit;

            } else {
                $error = "Invalid email or password.";
            }

        } else {
            $error = "Invalid email or password.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Login - Travelease</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;

    font-family: Arial, sans-serif;

    background: linear-gradient(
        135deg,
        #111827,
        #2563eb
    );
}

.login-box {

    width: 390px;

    background: white;

    padding: 35px;

    border-radius: 15px;

    box-shadow:
        0 10px 35px rgba(0,0,0,0.25);
}

.logo {

    text-align: center;

    font-size: 28px;

    font-weight: bold;

    margin-bottom: 5px;

    color: #111827;
}

.logo span {
    color: #2563eb;
}

.subtitle {

    text-align: center;

    color: #6b7280;

    margin-bottom: 28px;

    font-size: 14px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {

    display: block;

    margin-bottom: 7px;

    font-weight: bold;

    font-size: 14px;
}

.form-group input {

    width: 100%;

    padding: 12px 14px;

    border: 1px solid #d1d5db;

    border-radius: 8px;

    outline: none;

    font-size: 14px;
}

.form-group input:focus {
    border-color: #2563eb;
}

.login-btn {

    width: 100%;

    padding: 13px;

    border: none;

    border-radius: 8px;

    background: #2563eb;

    color: white;

    font-size: 16px;

    font-weight: bold;

    cursor: pointer;
}

.login-btn:hover {
    background: #1d4ed8;
}

.error {

    background: #fee2e2;

    color: #991b1b;

    padding: 11px;

    border-radius: 7px;

    margin-bottom: 18px;

    text-align: center;

    font-size: 14px;
}

.back {

    display: block;

    text-align: center;

    margin-top: 20px;

    text-decoration: none;

    color: #2563eb;

    font-size: 14px;
}

</style>

</head>

<body>

<div class="login-box">

    <div class="logo">
        <span>Travelease</span> Admin
    </div>

    <div class="subtitle">
        Admin Dashboard Login
    </div>

    <?php if ($error): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <div class="form-group">

            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Enter admin email"
                required
            >

        </div>

        <div class="form-group">

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter password"
                required
            >

        </div>

        <button
            type="submit"
            class="login-btn">

            Login to Dashboard

        </button>

    </form>

    <a
        href="index.html"
        class="back">

        ← Back to Travelease

    </a>

</div>

</body>

</html>