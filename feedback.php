<?php

include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    $sql = "INSERT INTO feedback (name, email, message)
            VALUES ('$name', '$email', '$message')";

    if ($conn->query($sql) === TRUE) {

        echo "<script>
                alert('Feedback submitted successfully!');
                window.location.href='index.php';
              </script>";

    } else {

        echo "Error: " . $conn->error;
    }
}

?>