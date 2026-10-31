<?php

require_once "db.php";

$booking_id = (int)($_GET["booking_id"] ?? $_POST["booking_id"] ?? 0);

if ($booking_id <= 0) {
    die("Invalid Booking ID");
}

/* =====================================
   PAYMENT SUBMIT
===================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $payment_method = $_POST["payment_method"] ?? "";

    if ($payment_method === "") {
        die("
            <h2>Payment Failed</h2>
            <p>Please select a payment method.</p>
            <a href='payment.php?booking_id=$booking_id'>Go Back</a>
        ");
    }

    /* UPDATE BOOKING STATUS */

    $sql = "UPDATE bookings SET status = 'Paid' WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Payment update failed.");
    }

    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();

?>

<!DOCTYPE html>
<html>
<head>

    <title>Payment Successful - Travelease</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7f9;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .success-box {
            background: white;
            width: 500px;
            max-width: 90%;
            padding: 40px;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }

        .success-icon {
            font-size: 60px;
            margin-bottom: 15px;
        }

        h1 {
            color: #198754;
            margin-bottom: 15px;
        }

        p {
            font-size: 17px;
            margin: 10px 0;
            color: #555;
        }

        .booking-id {
            margin: 20px 0;
            padding: 15px;
            background: #f1f8ff;
            border-radius: 8px;
            font-size: 20px;
        }

        .home-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: #2196f3;
            color: white;
            text-decoration: none;
            border-radius: 7px;
        }

    </style>

</head>

<body>

    <div class="success-box">

        <div class="success-icon">✓</div>

        <h1>Payment Successful!</h1>

        <p>Your booking has been confirmed.</p>

        <div class="booking-id">

            <strong>Booking ID:</strong>
            <?php echo $booking_id; ?>

        </div>

        <p>
            Thank you for booking with <strong>Travelease</strong>.
        </p>

        <a href="index.html" class="home-btn">
            Back to Home
        </a>

    </div>

</body>
</html>

<?php

    $conn->close();
    exit;
}

/* =====================================
   SHOW PAYMENT PAGE
===================================== */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Travelease Payment</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7f9;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .payment-container {
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }

        h1 {
            text-align: center;
            color: #222;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
        }

        .booking-box {
            background: #eef7ff;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .booking-box p {
            font-size: 18px;
            margin: 5px 0;
        }

        h2 {
            margin-bottom: 15px;
            font-size: 20px;
        }

        .payment-option {
            border: 1px solid #ddd;
            padding: 16px;
            margin-bottom: 12px;
            border-radius: 8px;
        }

        .payment-option:hover {
            background: #f8fcff;
            border-color: #2196f3;
        }

        .payment-option input {
            margin-right: 10px;
        }

        .pay-btn {
            width: 100%;
            padding: 14px;
            margin-top: 20px;
            background: #2196f3;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
        }

        .pay-btn:hover {
            background: #1976d2;
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #555;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="payment-container">

    <h1>Travelease Payment</h1>

    <p class="subtitle">
        Complete your payment to confirm your booking
    </p>


    <div class="booking-box">

        <p>
            <strong>Booking ID:</strong>
            <?php echo htmlspecialchars($booking_id); ?>
        </p>

        <p>
            <strong>Status:</strong> Pending
        </p>

    </div>


    <h2>Select Payment Method</h2>


    <form method="POST">

        <input
            type="hidden"
            name="booking_id"
            value="<?php echo $booking_id; ?>"
        >


        <div class="payment-option">

            <label>

                <input
                    type="radio"
                    name="payment_method"
                    value="UPI"
                    required
                >

                UPI Payment

            </label>

        </div>


        <div class="payment-option">

            <label>

                <input
                    type="radio"
                    name="payment_method"
                    value="Card"
                >

                Credit / Debit Card

            </label>

        </div>


        <div class="payment-option">

            <label>

                <input
                    type="radio"
                    name="payment_method"
                    value="Cash"
                >

                Cash Payment

            </label>

        </div>


        <button type="submit" class="pay-btn">

            Proceed to Payment

        </button>

    </form>


    <a href="index.html#booking" class="back-btn">

        ← Back to Booking

    </a>

</div>

</body>

</html>