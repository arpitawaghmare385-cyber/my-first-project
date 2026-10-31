<?php
session_start();
require_once "db.php";

// Accept only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin.php#bookings");
    exit;
}

// Validate booking ID
$bookingId = filter_input(INPUT_POST, "booking_id", FILTER_VALIDATE_INT);
$status = $_POST["status"] ?? "";

if (!$bookingId || !in_array($status, ["Confirmed", "Cancelled"], true)) {
    header("Location: admin.php#bookings");
    exit;
}

// Verify the admin session
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit;
}

// Update booking status
$stmt = $conn->prepare(
    "UPDATE bookings SET status = ? WHERE id = ?"
);

if (!$stmt) {
    die("Unable to prepare booking status update.");
}

$stmt->bind_param("si", $status, $bookingId);
$stmt->execute();

$stmt->close();

header("Location: admin.php#bookings");
exit;
?>
