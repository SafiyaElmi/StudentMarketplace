<?php
session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $check = $conn->prepare("SELECT userID FROM user WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {

        $_SESSION['error'] = "Email already exists.";

        header("Location: register.php");
        exit();

    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO user
            (firstName, lastName, email, password)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssss",
        $first_name,
        $last_name,
        $email,
        $hashedPassword,
    );

    if ($stmt->execute()) {

        $_SESSION['success'] = "Registration successful. Please login.";

        header("Location: login.php");
        exit();

    } else {

        $_SESSION['error'] = "Registration failed: " . $stmt->error;

        header("Location: register.php");
        exit();

    }

    $stmt->close();
    $check->close();
}

$conn->close();
?>