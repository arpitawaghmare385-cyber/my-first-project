<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit;
}

require_once "db.php";

/* =========================================================
   HELPER FUNCTIONS
========================================================= */

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function tableExists($conn, $table)
{
    $table = $conn->real_escape_string($table);

    $result = $conn->query("SHOW TABLES LIKE '$table'");

    return ($result && $result->num_rows > 0);
}

function getColumns($conn, $table)
{
    $columns = [];

    if (!tableExists($conn, $table)) {
        return $columns;
    }

    $result = $conn->query("SHOW COLUMNS FROM `$table`");

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }

    return $columns;
}

function firstColumn($columns, $possible)
{
    foreach ($possible as $column) {
        if (in_array($column, $columns, true)) {
            return $column;
        }
    }

    return null;
}

function countTable($conn, $table)
{
    if (!tableExists($conn, $table)) {
        return 0;
    }

    $result = $conn->query("SELECT COUNT(*) AS total FROM `$table`");

    if ($result) {
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    return 0;
}

/* =========================================================
   DATABASE STATUS
========================================================= */

$dbConnected = false;

if (isset($conn) && !$conn->connect_error) {
    $dbConnected = true;
}

/* =========================================================
   TABLE DETECTION
========================================================= */

$usersTable = tableExists($conn, "users") ? "users" : null;

$bookingsTable = null;

if (tableExists($conn, "bookings")) {
    $bookingsTable = "bookings";
} elseif (tableExists($conn, "booking")) {
    $bookingsTable = "booking";
}

$messagesTable = null;

if (tableExists($conn, "contact_messages")) {
    $messagesTable = "contact_messages";
} elseif (tableExists($conn, "messages")) {
    $messagesTable = "messages";
} elseif (tableExists($conn, "contact")) {
    $messagesTable = "contact";
}

$packagesTable = tableExists($conn, "packages") ? "packages" : null;

$destinationsTable = tableExists($conn, "destinations") ? "destinations" : null;

/* =========================================================
   COLUMN DETECTION
========================================================= */

$userColumns = $usersTable ? getColumns($conn, $usersTable) : [];

$userIdColumn = firstColumn(
    $userColumns,
    ["id", "user_id", "uid"]
);

$userNameColumn = firstColumn(
    $userColumns,
    ["name", "full_name", "username", "user_name"]
);

$userEmailColumn = firstColumn(
    $userColumns,
    ["email", "email_address"]
);

$userPhoneColumn = firstColumn(
    $userColumns,
    ["phone", "phone_number", "mobile", "contact"]
);

$userDateColumn = firstColumn(
    $userColumns,
    ["created_at", "registered_at", "date", "created_date"]
);

/* BOOKING COLUMNS */

$bookingColumns = $bookingsTable
    ? getColumns($conn, $bookingsTable)
    : [];

$bookingIdColumn = firstColumn(
    $bookingColumns,
    ["id", "booking_id", "bid"]
);

$bookingNameColumn = firstColumn(
    $bookingColumns,
    ["name", "full_name", "customer_name", "username"]
);

$bookingEmailColumn = firstColumn(
    $bookingColumns,
    ["email", "email_address"]
);

$bookingPhoneColumn = firstColumn(
    $bookingColumns,
    ["phone", "phone_number", "mobile", "contact"]
);

$bookingPackageColumn = firstColumn(
    $bookingColumns,
    ["package", "package_name", "tour", "tour_name", "destination"]
);

$bookingDateColumn = firstColumn(
    $bookingColumns,
    ["booking_date", "date", "travel_date", "created_at"]
);

$bookingStatusColumn = firstColumn(
    $bookingColumns,
    ["status", "booking_status"]
);

/* MESSAGE COLUMNS */

$messageColumns = $messagesTable
    ? getColumns($conn, $messagesTable)
    : [];

$messageNameColumn = firstColumn(
    $messageColumns,
    ["name", "full_name", "username"]
);

$messageEmailColumn = firstColumn(
    $messageColumns,
    ["email", "email_address"]
);

$messageTextColumn = firstColumn(
    $messageColumns,
    ["message", "message_text", "content", "description"]
);

$messageDateColumn = firstColumn(
    $messageColumns,
    ["created_at", "date", "sent_at"]
);

/* PACKAGE COLUMNS */

$packageColumns = $packagesTable
    ? getColumns($conn, $packagesTable)
    : [];

$packageIdColumn = firstColumn(
    $packageColumns,
    ["id", "package_id"]
);

$packageNameColumn = firstColumn(
    $packageColumns,
    ["name", "package_name", "title"]
);

$packagePriceColumn = firstColumn(
    $packageColumns,
    ["price", "amount", "tour_price"]
);

/* DESTINATION COLUMNS */

$destinationColumns = $destinationsTable
    ? getColumns($conn, $destinationsTable)
    : [];

$destinationNameColumn = firstColumn(
    $destinationColumns,
    ["name", "destination_name", "title"]
);

/* =========================================================
   DELETE USER
========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        isset($_POST["delete_user"]) &&
        $usersTable &&
        $userIdColumn
    ) {

        $deleteId = $_POST["delete_user"];

        $stmt = $conn->prepare(
            "DELETE FROM `$usersTable` WHERE `$userIdColumn` = ?"
        );

        if ($stmt) {

            $stmt->bind_param("s", $deleteId);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: admin.php?section=users");
        exit;
    }
}

/* =========================================================
   DASHBOARD COUNTS
========================================================= */

$totalUsers = $usersTable
    ? countTable($conn, $usersTable)
    : 0;

$totalBookings = $bookingsTable
    ? countTable($conn, $bookingsTable)
    : 0;

/* Count packages from the website's HTML section */

$totalPackages = 16;


$totalDestinations = $destinationsTable
    ? countTable($conn, $destinationsTable)
    : 0;

$totalMessages = $messagesTable
    ? countTable($conn, $messagesTable)
    : 0;

$confirmedBookings = 0;
$pendingBookings = 0;
$cancelledBookings = 0;

if ($bookingsTable) {

    $sql = "SELECT * FROM `$bookingsTable`";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {

            $status = "";

            if ($bookingStatusColumn && isset($row[$bookingStatusColumn])) {
                $status = strtolower(trim($row[$bookingStatusColumn]));
            }

            if ($status === "confirmed" || $status === "confirm") {
                $confirmedBookings++;
            }
            elseif ($status === "cancelled" || $status === "canceled") {
                $cancelledBookings++;
            }
            else {
                $pendingBookings++;
            }
        }
    }
}

/* =========================================================
   LOAD USERS
========================================================= */

$users = [];

if ($usersTable) {

    $orderBy = $userIdColumn
        ? "ORDER BY `$userIdColumn` DESC"
        : "";

    $sql = "SELECT * FROM `$usersTable` $orderBy LIMIT 100";

    $result = $conn->query($sql);

    if ($result) {

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
}

/* =========================================================
   LOAD BOOKINGS
========================================================= */

$bookings = [];

if ($bookingsTable) {

    $orderBy = $bookingIdColumn
        ? "ORDER BY `$bookingIdColumn` DESC"
        : "";

    $sql = "SELECT * FROM `$bookingsTable` $orderBy LIMIT 100";

    $result = $conn->query($sql);

    if ($result) {

        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
}

/* =========================================================
   LOAD MESSAGES
========================================================= */

$messages = [];

if ($messagesTable) {

    $orderBy = $messageDateColumn
        ? "ORDER BY `$messageDateColumn` DESC"
        : "";

    $sql = "SELECT * FROM `$messagesTable` $orderBy LIMIT 100";

    $result = $conn->query($sql);

    if ($result) {

        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
}

/* =========================================================
   LOAD PACKAGES
========================================================= */

$packages = [];

if ($packagesTable) {

    $orderBy = $packageIdColumn
        ? "ORDER BY `$packageIdColumn` DESC"
        : "";

    $sql = "SELECT * FROM `$packagesTable` $orderBy LIMIT 100";

    $result = $conn->query($sql);

    if ($result) {

        while ($row = $result->fetch_assoc()) {
            $packages[] = $row;
        }
    }
}

/* =========================================================
   LOAD DESTINATIONS
========================================================= */

$destinations = [];

if ($destinationsTable) {

    $sql = "SELECT * FROM `$destinationsTable` LIMIT 100";

    $result = $conn->query($sql);

    if ($result) {

        while ($row = $result->fetch_assoc()) {
            $destinations[] = $row;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Travelease Admin Dashboard</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f6f9;
    color: #222;
}

/* =========================================================
   SIDEBAR
========================================================= */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 240px;
    height: 100vh;
    background: #111827;
    color: white;
    padding: 25px 15px;
    overflow-y: auto;
}

.logo {
    text-align: center;
    font-size: 25px;
    font-weight: bold;
    margin-bottom: 35px;
}

.logo span {
    color: #22c55e;
}

.sidebar a {
    display: block;
    color: #d1d5db;
    text-decoration: none;
    padding: 13px 15px;
    margin-bottom: 7px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.2s;
}

.sidebar a:hover,
.sidebar a.active {
    background: #2563eb;
    color: white;
}

.logout {
    margin-top: 30px !important;
    background: #dc2626;
    color: white !important;
}

/* =========================================================
   MAIN
========================================================= */

.main {
    margin-left: 240px;
    padding: 25px;
}

/* =========================================================
   TOP BAR
========================================================= */

.topbar {
    background: white;
    padding: 18px 22px;
    border-radius: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.topbar h1 {
    font-size: 25px;
}

.db-status {
    padding: 8px 13px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: bold;
}

.connected {
    background: #dcfce7;
    color: #166534;
}

.not-connected {
    background: #fee2e2;
    color: #991b1b;
}

/* =========================================================
   SECTIONS
========================================================= */

.section {
    display: none;
}

.section.active {
    display: block;
}

/* =========================================================
   CARDS
========================================================= */

.cards {
    display: grid;
    grid-template-columns:
        repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.card {
    background: white;
    padding: 23px;
    border-radius: 12px;
    box-shadow:
        0 2px 10px rgba(0,0,0,0.06);
}

.card h3 {
    font-size: 15px;
    color: #6b7280;
    margin-bottom: 12px;
}

.card .number {
    font-size: 32px;
    font-weight: bold;
}

.blue {
    border-left: 5px solid #2563eb;
}

.green {
    border-left: 5px solid #16a34a;
}

.orange {
    border-left: 5px solid #f59e0b;
}

.purple {
    border-left: 5px solid #9333ea;
}

/* =========================================================
   CONTENT BOX
========================================================= */

.content-box {
    background: white;
    padding: 22px;
    border-radius: 12px;
    box-shadow:
        0 2px 10px rgba(0,0,0,0.06);
    margin-bottom: 25px;
    overflow-x: auto;
}

.content-box h2 {
    margin-bottom: 18px;
}

/* =========================================================
   TABLE
========================================================= */

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 650px;
}

th,
td {
    padding: 13px 12px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

th {
    background: #f3f4f6;
    font-size: 14px;
}

td {
    font-size: 14px;
}

tr:hover td {
    background: #f9fafb;
}

/* =========================================================
   STATUS
========================================================= */

.status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
}

.status.confirmed {
    background: #dcfce7;
    color: #166534;
}

.status.pending {
    background: #fef3c7;
    color: #92400e;
}

.status.cancelled {
    background: #fee2e2;
    color: #991b1b;
}

/* =========================================================
   DELETE BUTTON
========================================================= */

.delete-btn {
    border: none;
    background: #dc2626;
    color: white;
    padding: 7px 12px;
    border-radius: 6px;
    cursor: pointer;
}

.delete-btn:hover {
    background: #b91c1c;
}

/* =========================================================
   SEARCH
========================================================= */

.search-box {
    margin-bottom: 18px;
}

.search-box input {
    width: 100%;
    max-width: 400px;
    padding: 12px 15px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    outline: none;
    font-size: 14px;
}

.search-box input:focus {
    border-color: #2563eb;
}

/* =========================================================
   REPORTS
========================================================= */

.report-grid {
    display: grid;
    grid-template-columns:
        repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.report-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow:
        0 2px 10px rgba(0,0,0,0.06);
}

.report-card h3 {
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 10px;
}

.report-card strong {
    font-size: 30px;
}

/* =========================================================
   INFO MESSAGE
========================================================= */

.info {
    background: #eff6ff;
    color: #1e40af;
    padding: 14px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.warning {
    background: #fff7ed;
    color: #9a3412;
    padding: 14px;
    border-radius: 8px;
    margin-bottom: 15px;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 800px) {

    .sidebar {
        width: 200px;
    }

    .main {
        margin-left: 200px;
    }
}

@media (max-width: 600px) {

    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .main {
        margin-left: 0;
    }

    .topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
}

</style>
</head>
<body>

<!-- =========================================================
     SIDEBAR
========================================================= -->

<div class="sidebar">

    <div class="logo">
        <span>Travelease</span>
        <br>
        Admin
    </div>

    <a class="nav-link active"
       data-section="dashboard">
        📊 Dashboard
    </a>

    <a class="nav-link"
       data-section="users">
        👤 Users
    </a>

    <a class="nav-link"
       data-section="bookings">
        📅 Bookings
    </a>

    <a class="nav-link"
       data-section="packages">
        🧳 Packages
    </a>

    <a class="nav-link"
       data-section="destinations">
        📍 Destinations
    </a>

    <a class="nav-link"
       data-section="messages">
        💬 Messages
    </a>

    <a class="nav-link"
       data-section="reports">
        📈 Reports
    </a>

    <a class="logout" href="admin-logout.php">
    🚪 Logout
</a>
</div>

<!-- =========================================================
     MAIN
========================================================= -->

<div class="main">

    <!-- TOP BAR -->

    <div class="topbar">

        <h1>Admin Dashboard</h1>

        <?php if ($dbConnected): ?>

            <div class="db-status connected">
                ● DATABASE CONNECTED
            </div>

        <?php else: ?>

            <div class="db-status not-connected">
                ● DATABASE ERROR
            </div>

        <?php endif; ?>

    </div>

    <!-- =====================================================
         DASHBOARD
    ====================================================== -->

    <section
        id="dashboard"
        class="section active">

        <div class="cards">

            <div class="card blue">

                <h3>Total Users</h3>

                <div class="number">
                    <?= $totalUsers ?>
                </div>

            </div>

            <div class="card green">

                <h3>Total Bookings</h3>

                <div class="number">
                    <?= $totalBookings ?>
                </div>

            </div>

            <div class="card orange">

                <h3>Total Packages</h3>

                <div class="number">
                    <?= $totalPackages ?>
                </div>
            </div>

            <div class="card purple">

                <h3>Destinations</h3>

                <div class="number">
                    <?= $totalDestinations ?>
                </div>
            </div>
        </div>

        <div class="content-box">

            <h2>Booking Overview</h2>

            <div class="cards">

                <div class="card green">

                    <h3>Confirmed</h3>

                    <div class="number">
                        <?= $confirmedBookings ?>
                    </div>

                </div>

                <div class="card orange">

                    <h3>Pending</h3>

                    <div class="number">
                        <?= $pendingBookings ?>
                    </div>

                </div>

                <div class="card blue">

                    <h3>Messages</h3>

                    <div class="number">
                        <?= $totalMessages ?>
                    </div>

                </div>

                <div class="card purple">

                    <h3>Cancelled</h3>

                    <div class="number">
                        <?= $cancelledBookings ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$usersTable): ?>

            <div class="warning">
                <strong>Users table not found.</strong>
                Create/connect your users table in the
                <strong>travelease</strong> database.
            </div>

        <?php endif; ?>

        <?php if (!$bookingsTable): ?>

            <div class="warning">
                <strong>Bookings table not found.</strong>
                Booking data will appear here after the
                booking form is connected with MySQL.
            </div>

        <?php endif; ?>

    </section>

    <!-- =====================================================
         USERS
    ====================================================== -->

    <section
        id="users"
        class="section">

        <div class="content-box">

            <h2>Registered Users</h2>

            <?php if (!$usersTable): ?>

                <div class="warning">
                    Users table is not available in database.
                </div>

            <?php elseif (count($users) === 0): ?>

                <div class="info">
                    No users registered yet.
                </div>

            <?php else: ?>

                <div class="search-box">

                    <input
                        type="text"
                        id="userSearch"
                        placeholder="Search user...">

                </div>

                <table id="usersTable">

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>

                        </tr>
            </thead>
                    <tbody>

                    <?php foreach ($users as $user): ?>

                        <tr>

                            <td>
                                <?= e(
                                    $userIdColumn
                                    ? ($user[$userIdColumn] ?? "")
                                    : ""
                                ) ?>
                            </td>

                            <td>
                                <?= e(
                                    $userNameColumn
                                    ? ($user[$userNameColumn] ?? "")
                                    : ""
                                ) ?>
                            </td>

                            <td>
                                <?= e(
                                    $userEmailColumn
                                    ? ($user[$userEmailColumn] ?? "")
                                    : ""
                                ) ?>
                            </td>

                            <td>
                                <?= e(
                                    $userPhoneColumn
                                    ? ($user[$userPhoneColumn] ?? "")
                                    : ""
                                ) ?>
                            </td>

                            <td>

                                <?php if ($userIdColumn): ?>

                                    <form
                                        method="POST"
                                        onsubmit="return confirm('Delete this user?');">

                                        <input
                                            type="hidden"
                                            name="delete_user"
                                            value="<?= e($user[$userIdColumn] ?? '') ?>">

                                        <button
                                            type="submit"
                                            class="delete-btn">

                                            Delete

                                        </button>

                                    </form>

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            <?php endif; ?>

        </div>
    </section>

    <!-- =====================================================
     BOOKINGS - SEPARATE PENDING, CONFIRMED, CANCELLED
====================================================== -->

<section id="bookings" class="section">

<div class="content-box">
    <h2>Manage Bookings</h2>
    <?php if (!$bookingsTable): ?>
        <div class="warning">
            <strong>Bookings table not found.</strong>
            Please check your MySQL database.
        </div>
    <?php elseif (count($bookings) === 0): ?>
        <div class="info">No bookings available.</div>

    <?php else: ?>
        <?php
        $pendingBookings = [];
        $confirmedBookings = [];
        $cancelledBookings = [];

        foreach ($bookings as $booking) {

            $statusValue = $bookingStatusColumn
                ? strtolower(trim($booking[$bookingStatusColumn] ?? "pending"))
                : "pending";

            if ($statusValue === "") {
                $statusValue = "pending";
            }

            if ($statusValue === "confirmed" || $statusValue === "confirm") {
               $confirmedBookings[] = $booking;
            } elseif ($statusValue === "cancelled" || $statusValue === "canceled") {
               $cancelledBookings[] = $booking;
            } else {
               $pendingBookings[] = $booking;
}
        }

       $bookingGroups = [
    "Pending Bookings" => $pendingBookings,
    "Confirmed Bookings" => $confirmedBookings,
    "Cancelled Bookings" => $cancelledBookings,
        ];
        ?>

        <?php foreach ($bookingGroups as $groupTitle => $groupBookings): ?>

            <div class="booking-group">
               <h3>
               <?= e($groupTitle) ?>
               (<?= count($groupBookings) ?>)
            </h3>

                <?php if (count($groupBookings) === 0): ?>

                    <div class="info">
                        No <?= e(strtolower($groupTitle)) ?> available.
                    </div>

                <?php else: ?>

                    <div style="overflow-x:auto;">

                        <table>

                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Package</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($groupBookings as $booking): ?>

                                    <?php
                                    $statusValue = $bookingStatusColumn
                                        ? trim($booking[$bookingStatusColumn] ?? "")
                                        : "";

                                    if ($statusValue === "") {
                                        $statusValue = "Pending";
                                    }

                                    $statusClass = strtolower($statusValue);
                                    ?>

                                    <tr>

                                        <td>
                                            <?= e($bookingIdColumn ? ($booking[$bookingIdColumn] ?? "") : "") ?>
                                        </td>

                                        <td>
                                            <?= e($bookingNameColumn ? ($booking[$bookingNameColumn] ?? "") : "") ?>
                                        </td>

                                        <td>
                                            <?= e($bookingEmailColumn ? ($booking[$bookingEmailColumn] ?? "") : "") ?>
                                        </td>

                                        <td>
                                            <?= e($bookingPhoneColumn ? ($booking[$bookingPhoneColumn] ?? "") : "") ?>
                                        </td>

                                        <td>
                                            <?= e($bookingPackageColumn ? ($booking[$bookingPackageColumn] ?? "") : "") ?>
                                        </td>

                                        <td>
                                            <?= e($bookingDateColumn ? ($booking[$bookingDateColumn] ?? "") : "") ?>
                                        </td>

                                        <td>
                                            <span class="status <?= e($statusClass) ?>">
                                                <?= e($statusValue) ?>
                                            </span>
                                        </td>
                                        <td>
    <?php if (in_array(strtolower(trim($statusValue)), ["pending", "paid"])): ?>

        <form method="POST" action="update-booking-status.php"
              style="display:inline;">

            <input type="hidden" name="booking_id"
                   value="<?= e($bookingIdColumn ? ($booking[$bookingIdColumn] ?? "") : "") ?>">

            <input type="hidden" name="status" value="Confirmed">

            <button type="submit"
                    name="update_status"
                    style="background:green;color:white;padding:8px 12px;border:0;border-radius:5px;cursor:pointer;">
                Confirm
            </button>

        </form>

        <form method="POST" action="update-booking-status.php"
              style="display:inline;">

            <input type="hidden" name="booking_id"
                   value="<?= e($bookingIdColumn ? ($booking[$bookingIdColumn] ?? "") : "") ?>">

            <input type="hidden" name="status" value="Cancelled">

            <button type="submit"
                    name="update_status"
                    style="background:red;color:white;padding:8px 12px;border:0;border-radius:5px;cursor:pointer;">
                Cancel
            </button>

        </form>
    <?php else: ?>

    <span>
        <?= e($statusValue) ?>
    </span>

<?php endif; ?>
</td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </div>

            <br>

        <?php endforeach; ?>

    <?php endif; ?>

</div>
</section>

    <!-- PACKAGES SECTION -->

<section id="packages" class="section">
<div class="content-box">

    <h2>Travel Packages</h2>

    <p style="margin-bottom: 18px; color: #6b7280;">
        Total Tour Packages: <strong>16</strong>
    </p>

    <table>

        <thead>
            <tr>
                <th>Sr. No.</th>
                <th>Package Name</th>
                <th>Duration</th>
                <th>Price</th>
            </tr>
        </thead>

        <tbody>

            <tr><td>1</td><td>Mahabaleshwar</td><td>3 Days / 2 Nights</td><td>₹6,999</td></tr>

            <tr><td>2</td><td>Lonavala</td><td>2 Days / 1 Night</td><td>₹4,999</td></tr>

            <tr><td>3</td><td>Matheran</td><td>2 Days / 1 Night</td><td>₹4,499</td></tr>

            <tr><td>4</td><td>Panchgani</td><td>2 Days / 1 Night</td><td>₹4,999</td></tr>

            <tr><td>5</td><td>Igatpuri</td><td>2 Days / 1 Night</td><td>₹4,499</td></tr>

            <tr><td>6</td><td>Bhandardara</td><td>2 Days / 1 Night</td><td>₹4,999</td></tr>

            <tr><td>7</td><td>Rajgad Trek</td><td>2 Days / 1 Night</td><td>₹3,999</td></tr>

            <tr><td>8</td><td>Sinhagad</td><td>1 Day Tour</td><td>₹1,999</td></tr>

            <tr><td>9</td><td>Alibaug</td><td>2 Days / 1 Night</td><td>₹4,999</td></tr>

            <tr><td>10</td><td>Tarkarli</td><td>3 Days / 2 Nights</td><td>₹7,999</td></tr>

            <tr><td>11</td><td>Ganpatipule</td><td>2 Days / 1 Night</td><td>₹4,999</td></tr>

            <tr><td>12</td><td>Ajanta & Ellora</td><td>2 Days / 1 Night</td><td>₹5,999</td></tr>

            <tr><td>13</td><td>Shirdi</td><td>2 Days / 1 Night</td><td>₹3,999</td></tr>

            <tr><td>14</td><td>Trimbakeshwar</td><td>1 Day Tour</td><td>₹2,499</td></tr>

            <tr><td>15</td><td>Kolhapur</td><td>2 Days / 1 Night</td><td>₹4,499</td></tr>

            <tr><td>16</td><td>Kolad Adventure</td><td>2 Days / 1 Night</td><td>₹5,499</td></tr>
        </tbody>
    </table>
</div>
</section>

    <!-- =====================================================
         DESTINATIONS
    ====================================================== -->

    <section
        id="destinations"
        class="section">

        <div class="content-box">

            <h2>Destinations</h2>

            <?php if (!$destinationsTable): ?>

                <div class="warning">

                    Destinations table is not available
                    in the database.

                </div>

            <?php elseif (count($destinations) === 0): ?>

                <div class="info">

                    No destinations found.

                </div>

            <?php else: ?>

                <table>

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Destination</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($destinations as $destination): ?>

                        <tr>

                            <td>
                                <?= e(
                                    isset($destination["id"])
                                    ? $destination["id"]
                                    : ""
                                ) ?>
                            </td>

                            <td>

                                <?= e(
                                    $destinationNameColumn
                                    ? (
                                        $destination[
                                            $destinationNameColumn
                                        ] ?? ""
                                    )
                                    : ""
                                ) ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            <?php endif; ?>

        </div>

    </section>

    <!-- =====================================================
         MESSAGES
    ====================================================== -->

    <section
        id="messages"
        class="section">

        <div class="content-box">

            <h2>Contact Messages</h2>

            <?php if (!$messagesTable): ?>

                <div class="warning">

                    Contact messages table is not available.

                </div>

            <?php elseif (count($messages) === 0): ?>

                <div class="info">

                    No contact messages found.

                </div>

            <?php else: ?>

                <table>

                    <thead>

                        <tr>

                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Date</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($messages as $message): ?>

                        <tr>

                            <td>

                                <?= e(
                                    $messageNameColumn
                                    ? (
                                        $message[
                                            $messageNameColumn
                                        ] ?? ""
                                    )
                                    : ""
                                ) ?>

                            </td>

                            <td>

                                <?= e(
                                    $messageEmailColumn
                                    ? (
                                        $message[
                                            $messageEmailColumn
                                        ] ?? ""
                                    )
                                    : ""
                                ) ?>

                            </td>

                            <td>

                                <?= e(
                                    $messageTextColumn
                                    ? (
                                        $message[
                                            $messageTextColumn
                                        ] ?? ""
                                    )
                                    : ""
                                ) ?>

                            </td>

                            <td>

                                <?= e(
                                    $messageDateColumn
                                    ? (
                                        $message[
                                            $messageDateColumn
                                        ] ?? ""
                                    )
                                    : ""
                                ) ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            <?php endif; ?>

        </div>

    </section>

    <!-- =====================================================
         REPORTS
    ====================================================== -->

    <section
        id="reports"
        class="section">

        <div class="content-box">

            <h2>Reports</h2>

            <div class="report-grid">

                <div class="report-card">

                    <h3>Total Users</h3>

                    <strong>
                        <?= $totalUsers ?>
                    </strong>

                </div>

                <div class="report-card">

                    <h3>Total Bookings</h3>

                    <strong>
                        <?= $totalBookings ?>
                    </strong>

                </div>

                <div class="report-card">
                    <h3>Confirmed Bookings</h3>
                 <strong>
                 <?= count($confirmedBookings) ?>
                </strong>

                </div>

                <div class="report-card">

                    <h3>Pending Bookings</h3>

                    <strong>
                        <?= count($pendingBookings) ?>
                    </strong>

                </div>

                <div class="report-card">

                    <h3>Cancelled Bookings</h3>

                    <strong>
                        <?= count($cancelledBookings) ?>
                    </strong>

                </div>

                <div class="report-card">

                    <h3>Packages</h3>

                    <strong>
                        <?= $totalPackages ?>
                    </strong>

                </div>

                <div class="report-card">

                    <h3>Destinations</h3>

                    <strong>
                        <?= $totalDestinations ?>
                    </strong>

                </div>

                <div class="report-card">

                    <h3>Messages</h3>

                    <strong>
                        <?= $totalMessages ?>
                    </strong>
                </div>
            </div>
        </div>
    </section>
</div>
<script>

/* =========================================================
   SIDEBAR NAVIGATION
========================================================= */

const navLinks =
    document.querySelectorAll(".nav-link");

const sections =
    document.querySelectorAll(".section");

navLinks.forEach(function(link) {

    link.addEventListener("click", function() {

        const target =
            this.getAttribute("data-section");

        navLinks.forEach(function(item) {

            item.classList.remove("active");

        });

        this.classList.add("active");

        sections.forEach(function(section) {

            section.classList.remove("active");

        });

        const selected =
            document.getElementById(target);

        if (selected) {

            selected.classList.add("active");

        }

    });

});

/* =========================================================
   USER SEARCH
========================================================= */

const userSearch =
    document.getElementById("userSearch");

if (userSearch) {

    userSearch.addEventListener(
        "keyup",
        function() {

            const value =
                this.value.toLowerCase();

            const rows =
                document.querySelectorAll(
                    "#usersTable tbody tr"
                );

            rows.forEach(function(row) {

                const text =
                    row.textContent.toLowerCase();

                if (text.includes(value)) {

                    row.style.display = "";

                } else {

                    row.style.display = "none";

                }

            });
        }
    );
}

/* =========================================================
   OPEN SECTION FROM URL
========================================================= */

const params =
    new URLSearchParams(window.location.search);

const requestedSection =
    params.get("section");

if (requestedSection) {

    navLinks.forEach(function(link) {

        link.classList.remove("active");

    });

    sections.forEach(function(section) {

        section.classList.remove("active");

    });

    const requested =
        document.getElementById(
            requestedSection
        );

    const requestedLink =
        document.querySelector(
            '[data-section="' +
            requestedSection +
            '"]'
        );

    if (requested) {

        requested.classList.add("active");
    }

    if (requestedLink) {

        requestedLink.classList.add("active");
    }
}

</script>
</body>
</html>