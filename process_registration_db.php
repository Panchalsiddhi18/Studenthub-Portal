
<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registration.html");
    exit;
}

$full_name = trim($_POST["name"] ?? "");
$username  = trim($_POST["enrollment"] ?? "");
$email     = trim($_POST["email"] ?? "");
$mobile    = trim($_POST["mobile"] ?? "");
$password  = $_POST["password"] ?? "";
$confirm   = $_POST["confirmPassword"] ?? "";
$course    = trim($_POST["course"] ?? "");
$year      = trim($_POST["year"] ?? "");
$semester  = filter_input(INPUT_POST, "semester", FILTER_VALIDATE_INT);
$gender    = $_POST["gender"] ?? "";
$terms     = $_POST["terms"] ?? "";

$errors = [];

if (!preg_match("/^[A-Za-z ]{2,50}$/", $full_name)) {
    $errors[] = "Enter a valid full name.";
}

if (!preg_match("/^[A-Za-z0-9_-]{5,20}$/", $username)) {
    $errors[] = "Enrollment ID must contain 5-20 letters, numbers, _ or -.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Enter a valid email address.";
}

if (!preg_match("/^[6-9][0-9]{9}$/", $mobile)) {
    $errors[] = "Enter a valid 10-digit mobile number.";
}

if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}$/", $password)) {
    $errors[] = "Password must have at least 8 characters, uppercase, lowercase and a number.";
}

if ($password !== $confirm) {
    $errors[] = "Passwords do not match.";
}

$allowed_courses = [
    "Computer Engineering",
    "Information Technology",
    "Computer Science",
    "Electronics Engineering"
];

$allowed_years = [
    "1st Year",
    "2nd Year",
    "3rd Year",
    "4th Year"
];

$allowed_genders = ["Male", "Female", "Other"];

if (!in_array($course, $allowed_courses, true)) {
    $errors[] = "Select a valid course.";
}

if (!in_array($year, $allowed_years, true)) {
    $errors[] = "Select a valid year.";
}

if ($semester === false || $semester < 1 || $semester > 8) {
    $errors[] = "Select a valid semester.";
}

if (!in_array($gender, $allowed_genders, true)) {
    $errors[] = "Select a valid gender.";
}

if ($terms !== "accepted") {
    $errors[] = "Please accept the Terms & Conditions.";
}

if (!empty($errors)) {
    echo "<h3>Registration failed</h3><ul>";
    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error, ENT_QUOTES, "UTF-8") . "</li>";
    }
    echo "</ul><p><a href='registration.html'>Go back to registration</a></p>";
    exit;
}

try {
    // Check whether email or enrollment ID already exists.
    $check = $pdo->prepare(
        "SELECT student_id FROM students
         WHERE email = :email OR username = :username
         LIMIT 1"
    );

    $check->execute([
        ":email" => $email,
        ":username" => $username
    ]);

    if ($check->fetch()) {
        echo "<h3>Email or Enrollment ID already registered.</h3>";
        echo "<p><a href='registration.html'>Go back</a></p>";
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $insert = $pdo->prepare(
        "INSERT INTO students
        (full_name, username, email, password_hash, mobile,
         course, year, semester, gender)
        VALUES
        (:full_name, :username, :email, :password_hash, :mobile,
         :course, :year, :semester, :gender)"
    );

    $insert->execute([
        ":full_name" => $full_name,
        ":username" => $username,
        ":email" => $email,
        ":password_hash" => $password_hash,
        ":mobile" => $mobile,
        ":course" => $course,
        ":year" => $year,
        ":semester" => $semester,
        ":gender" => $gender
    ]);

    header("Location: login.php?registered=1");
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo "Registration could not be completed. Please try again.";
}
?>
