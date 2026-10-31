<?php

include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($name == "" || $email == "" || $phone == "" || $subject == "" || $message == "") {
        die("Please fill all fields.");
    }

    $sql = "INSERT INTO contact_messages 
            (name, email, phone, subject, message)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

    if ($stmt->execute()) {
        echo "<script>
                alert('Your message has been sent successfully!');
                window.location.href='index.html#contact';
              </script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: index.html#contact");
    exit;
}

?>