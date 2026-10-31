<?php

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html#booking");
    exit;
}

/* ================================
   GET FORM DATA
================================ */

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$persons = (int)($_POST["persons"] ?? 0);
$booking_date = $_POST["booking_date"] ?? "";
$package_name = trim($_POST["package_name"] ?? "");

/* ================================
   VALIDATION
================================ */

if (
    $name === "" ||
    $email === "" ||
    $phone === "" ||
    $persons < 1 ||
    $booking_date === "" ||
    $package_name === ""
) {

    die("
        <h2>Booking Failed</h2>
        <p>Please fill all booking details.</p>
        <a href='index.html#booking'>Go Back</a>
    ");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    die("
        <h2>Invalid Email</h2>
        <p>Please enter a valid email address.</p>
        <a href='index.html#booking'>Go Back</a>
    ");
}

/* ================================
   INSERT BOOKING
================================ */

$sql = "
    INSERT INTO bookings
    (
        name,
        email,
        phone,
        persons,
        package_name,
        booking_date,
        status
    )
    VALUES (?, ?, ?, ?, ?, ?, 'Pending')
";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    die("
        <h2>Booking Error</h2>
        <p>Unable to prepare booking request.</p>
    ");
}

$stmt->bind_param(
    "sssiss",
    $name,
    $email,
    $phone,
    $persons,
    $package_name,
    $booking_date
);

if ($stmt->execute()) {

    $booking_id = $conn->insert_id;

    $stmt->close();
    $conn->close();

    header("Location: payment.php?booking_id=" . $booking_id);
    exit;

} else {

    $stmt->close();

    die("
        <h2>Booking Failed</h2>

        <p>
            Something went wrong while saving your booking.
        </p>

        <a href='index.html#booking'>
            Try Again
        </a>
    ");
}
?>
    