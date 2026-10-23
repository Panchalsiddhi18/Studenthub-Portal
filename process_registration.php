
<?php
session_start();

// Clean input data
function cleanInput($value) {
    return trim((string)$value);
}

// Display success or error message
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

// Allow only form submissions
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registration.html");
    exit;
}

// Get form data
$name = cleanInput($_POST["name"] ?? "");
$enrollment = cleanInput($_POST["enrollment"] ?? "");
$email = cleanInput($_POST["email"] ?? "");
$mobile = cleanInput($_POST["mobile"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";
$course = cleanInput($_POST["course"] ?? "");
$year = cleanInput($_POST["year"] ?? "");
$semester = cleanInput($_POST["semester"] ?? "");
$gender = cleanInput($_POST["gender"] ?? "");
$terms = isset($_POST["terms"]);

// Validate form data
$errors = [];

if (!preg_match("/^[a-zA-Z ]{2,50}$/", $name)) {
    $errors[] = "Enter a valid name (2-50 letters).";
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

// Validate course
$allowedCourses = [
    "Computer Engineering",
    "Information Technology",
    "Computer Science",
    "Electronics Engineering"
];

if (!in_array($course, $allowedCourses, true)) {
    $errors[] = "Select a valid course.";
}

// Validate year and semester
$allowedYears = [
    "1st Year", "2nd Year", "3rd Year", "4th Year"
];

$allowedSemesters = [
    "1", "2", "3", "4", "5", "6", "7", "8"
];

if (!in_array($year, $allowedYears, true)) {
    $errors[] = "Select a valid year.";
}

if (!in_array($semester, $allowedSemesters, true)) {
    $errors[] = "Please select a valid semester.";
}

// Check that semester matches selected year
$yearSemesters = [
    "1st Year" => ["1", "2"],
    "2nd Year" => ["3", "4"],
    "3rd Year" => ["5", "6"],
    "4th Year" => ["7", "8"]
];

if (
    isset($yearSemesters[$year]) &&
    !in_array($semester, $yearSemesters[$year], true)
) {
    $errors[] = "The selected semester does not match the selected year.";
}

// Validate gender
$allowedGenders = ["Male", "Female", "Other"];

if (!in_array($gender, $allowedGenders, true)) {
    $errors[] = "Select a valid gender.";
}

// Check terms and conditions
if (!$terms) {
    $errors[] = "Please accept the Terms and Conditions.";
}

// Display validation errors
if (!empty($errors)) {
    showMessage(implode(" ", $errors));
    exit;
}

// Create data directory if it does not exist
$dataDirectory = __DIR__ . "/data";

if (!is_dir($dataDirectory) &&
    !mkdir($dataDirectory, 0755, true)) {
    showMessage("Unable to create the data directory.");
    exit;
}

// CSV file location
$file = $dataDirectory . "/registrations.csv";
$isNewFile = !file_exists($file);

// Open CSV file
$handle = fopen($file, "a");

if ($handle === false) {
    showMessage("Unable to save registration. Check folder permissions.");
    exit;
}

// Lock file while writing
if (!flock($handle, LOCK_EX)) {
    fclose($handle);
    showMessage("Unable to save registration right now. Please try again.");
    exit;
}

// Add header for a new or empty file
fseek($handle, 0, SEEK_END);
$isEmptyFile = (ftell($handle) === 0);

if ($isNewFile || $isEmptyFile) {
    fputcsv($handle, [
        "Name",
        "Enrollment",
        "Email",
        "Mobile",
        "Password Hash",
        "Course",
        "Year",
        "Semester",
        "Gender",
        "Registered At"
    ]);
}

// Hash password before saving
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Prepare registration record
$record = [
    $name,
    $enrollment,
    $email,
    $mobile,
    $passwordHash,
    $course,
    $year,
    $semester,
    $gender,
    date("Y-m-d H:i:s")
];

// Save registration data
$saved = fputcsv($handle, $record);

flock($handle, LOCK_UN);
fclose($handle);

// Show result
if ($saved === false) {
    showMessage("Registration could not be saved. Please try again.");
    exit;
}

showMessage(
    "Registration successful! Your details have been saved.",
    true
);
?>
