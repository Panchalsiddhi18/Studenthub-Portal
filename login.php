<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST["login"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($login === "" || $password === "") {
        header("Location: Login.html?error=empty");
        exit;
    }

    try {
        $sql = "SELECT student_id, full_name, username, email,
                       password_hash, role
                FROM students
                WHERE username = :username OR email = :email
                LIMIT 1";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            "username" => $login,
            "email" => $login
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password_hash"])) {

            // Generate a new session ID after successful login
            session_regenerate_id(true);

            // Store logged-in user information
            $_SESSION["student_id"] = $user["student_id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["last_activity"] = time();

            // Redirect according to the user's role
            if ($user["role"] === "admin") {
                header("Location: AdminDashboard.php");
            } else {
                header("Location: Home.php");
            }

            exit;

        } else {
            header("Location: Login.html?error=invalid");
            exit;
        }

    } catch (PDOException $e) {
        error_log("Login database error: " . $e->getMessage());
        header("Location: Login.html?error=server");
        exit;
    }
}

// If login.php is opened directly, return to the designed login page.
header("Location: Login.html");
exit;
?>
