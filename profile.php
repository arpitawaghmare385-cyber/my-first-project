<?php
session_start();
include "db.php";

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];

// Get user details
$stmt = $conn->prepare(
    "SELECT username, email FROM users WHERE username = ?"
);

$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found!";
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>My Profile - Travelease</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
        }

        .profile-box {
            width: 400px;
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

        .profile-info {
            margin: 15px 0;
            padding: 12px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        .profile-info strong {
            display: block;
            margin-bottom: 5px;
        }

        .logout-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
            padding: 12px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .logout-btn:hover {
            background: #555;
        }

        .home-btn {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #333;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="profile-box">

    <h2>My Profile</h2>

    <div class="profile-info">
        <strong>Username</strong>
        <?php echo htmlspecialchars($user["username"]); ?>
    </div>

    <div class="profile-info">
        <strong>Email</strong>
        <?php echo htmlspecialchars($user["email"]); ?>
    </div>

    <a href="logout.php" class="logout-btn">
        Logout
    </a>

    <a href="index.html" class="home-btn">
        Back to Home
    </a>

</div>

</body>

</html>