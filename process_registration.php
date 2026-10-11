
<?php
session_start();

function cleanInput($value) {
    return trim((string)$value);
}

function showMessage($message, $success = false) {
    $color = $success ? "#155724" : "#721c24";
    $background = $success ? "#d4edda" : "#f8d7da";

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>StudentHub Registration</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div style="max-width:650px;margin:60px auto;padding:25px;
                    background:#e1e1da;border:1px solid #011935;
                    border-radius:10px;text-align:center;
                    font-family:Arial,sans-serif;">
            <h2 style="color:#011935;">StudentHub Portal</h2>
            <p style="padding:15px;border-radius:6px;color:' . $color .
                 ';background:' . $background . ';">'
                 . htmlspecialchars($message, ENT_QUOTES, "UTF-8") .
            '</p>
            <a href="registration.html">Back to Registration</a>
        </div>
    </body>
    </html>';
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registration.html");
    exit;
}

$name = cleanInput($_POST["name"] ?? "");
$enrollment = cleanInput($_POST["enrollment"] ?? "");
$email = cleanInput($_POST["email"] ?? "");
$mobile = cleanInput($_POST["mobile"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";
$course = cleanInput($_POST["course"] ?? "");
$year = cleanInput($_POST["year"] ?? "");
$semester = trim($_POST['semester'] ?? '');
$gender = cleanInput($_POST["gender"] ?? "");
$terms = isset($_POST["terms"]);

$errors = [];

if (!preg_match("/^[a-zA-Z ]{2,50}$/", $name)) {
    $errors[] = "Enter a valid name (2–50 letters).";
}

if (!preg_match("/^[A-Za-z0-9\/-]{5,20}$/", $enrollment)) {
    $errors[] = "Enter a valid enrollment ID.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Enter a valid email address.";
}

if (!preg_match("/^[6-9][0-9]{9}$/", $mobile)) {
    $errors[] = "Enter a valid 10-digit Indian mobile number.";
}

if (
    strlen($password) < 8 ||
    !preg_match("/[a-z]/", $password) ||
    !preg_match("/[A-Z]/", $password) ||
    !preg_match("/[0-9]/", $password)
) {
    $errors[] = "Password must have at least 8 characters, including uppercase, lowercase and a number.";
}

if ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
}

$allowedCourses = [
    "Computer Engineering",
    "Information Technology",
    "Computer Science",
    "Electronics Engineering"
];

if (!in_array($course, $allowedCourses, true)) {
    $errors[] = "Select a valid course.";
}

$allowedYears = [
    "1st Year", "2nd Year", "3rd Year", "4th Year"
];
if (!in_array($semester, ['1', '2', '3', '4', '5', '6', '7', '8'], true)) {
    $errors[] = "Please select a valid semester.";
}
if (!in_array($year, $allowedYears, true)) {
    $errors[] = "Select a valid year.";
}

$allowedGenders = ["Male", "Female", "Other"];

if (!in_array($gender, $allowedGenders, true)) {
    $errors[] = "Select a valid gender.";
}

if (!$terms) {
    $errors[] = "Please accept the Terms and Conditions.";
}

if (!empty($errors)) {
    showMessage(implode(" ", $errors));
    exit;
}

$dataDirectory = __DIR__ . "/data";

if (!is_dir($dataDirectory) &&
    !mkdir($dataDirectory, 0755, true)) {
    showMessage("Unable to create the data directory.");
    exit;
}

$file = $dataDirectory . "/registrations.csv";
$isNewFile = !file_exists($file);

$handle = fopen($file, "a");

if ($handle === false) {
    showMessage("Unable to save registration. Check folder permissions.");
    exit;
}

if (!flock($handle, LOCK_EX)) {
    fclose($handle);
    showMessage("Unable to save registration right now. Please try again.");
    exit;
}

if ($isNewFile) {
    fputcsv($handle, [
        "Name", "Enrollment", "Email", "Mobile",
        "Password Hash", "Course", "Year", "Gender", "Registered At"
    ]);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$record = [
    $name,
    $enrollment,
    $email,
    $mobile,
    $passwordHash,
    $course,
    $year,
    $semester
    $gender,
    date("Y-m-d H:i:s")
];

$saved = fputcsv($handle, $record);

flock($handle, LOCK_UN);
fclose($handle);

if ($saved === false) {
    showMessage("Registration could not be saved. Please try again.");
    exit;
}

showMessage("Registration successful! Your details have been saved.", true);
?>
