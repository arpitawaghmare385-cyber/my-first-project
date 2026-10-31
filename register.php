<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    // Check if username or email already exists
    $check = $conn->prepare(
        "SELECT id FROM users WHERE username = ? OR email = ?"
    );

    $check->bind_param("ss", $username, $email);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows > 0) {

        $message = "Username or Email already exists!";

    } else {

        // Insert new user
        $stmt = $conn->prepare(
            "INSERT INTO users (username, email, password)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {

            echo "<script>
                    alert('Registration Successful!');
                    window.location.href='login.php';
                  </script>";
            exit();

        } else {

            $message = "Registration failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Travelease Registration</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
        }

        .register-box {
            width: 350px;
            margin: 80px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #555;
        }

        .message {
            text-align: center;
            color: red;
            margin-bottom: 10px;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #333;
            font-weight: bold;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="register-box">

    <h2>Create Account</h2>

    <?php
    if ($message != "") {
        echo "<p class='message'>$message</p>";
    }
    ?>

    <form method="POST">

        <input
            type="text"
            name="username"
            placeholder="Username"
            required
        >

        <input
            type="email"
            name="email"
            placeholder="Email"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >

        <button type="submit">
            Register
        </button>

    </form>

    <div class="login-link">
        Already have an account?
        <a href="login.php">Login</a>
    </div>

</div>

</body>

</html>