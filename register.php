
<?php
$message = "";
$messageType = "";

$conn = new mysqli("localhost", "root", "", "studenthub_submit");

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST["full_name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if (
        $fullName === "" ||
        $username === "" ||
        $email === "" ||
        $password === "" ||
        $confirmPassword === ""
    ) {
        $message = "Please fill in all required fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $message = "Username must be 3-50 characters using letters, numbers or underscore.";
        $messageType = "error";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
        $messageType = "error";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
        $messageType = "error";
    } else {
        $check = $conn->prepare(
            "SELECT student_id FROM students WHERE email = ? OR username = ?"
        );
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email or username already exists.";
            $messageType = "error";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO students (full_name, username, email, password_hash)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $fullName, $username, $email, $hash);

            if ($stmt->execute()) {
                $message = "Registration successful! You can now log in.";
                $messageType = "success";
            } else {
                $message = "Registration failed. Please try again.";
                $messageType = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f5f9;
            margin: 0;
            padding: 30px 15px;
        }
        .container {
            max-width: 420px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 12px #0001;
        }
        h2 { text-align: center; color: #011935; }
        label { display: block; margin-top: 12px; }
        input {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #011935;
            color: white;
            border: 0;
            cursor: pointer;
        }
        .success { color: #087a32; }
        .error { color: #c62828; }
    </style>
</head>
<body>
<div class="container">
    <h2>StudentHub Registration</h2>

    <?php if ($message !== ""): ?>
        <p class="<?= htmlspecialchars($messageType) ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label>Full Name</label>
        <input type="text" name="full_name" required maxlength="100">

        <label>Username</label>
        <input type="text" name="username" required minlength="3" maxlength="50"
               pattern="[a-zA-Z0-9_]+">

        <label>Email</label>
        <input type="email" name="email" required maxlength="100">

        <label>Password</label>
        <input type="password" name="password" required minlength="8">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required minlength="8">

        <button type="submit">Register</button>
    </form>
</div>
</body>
</html>
<?php $conn->close(); ?>
