
<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["student_id"])) {
    header("Location: Login.html");
    exit;
}

if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location: Home.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ViewStudents.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: ViewStudents.php");
    exit;
}

try {
    $stmt = $pdo->prepare(
        "DELETE FROM students
         WHERE student_id = :id AND role = 'student'"
    );
    $stmt->execute(["id" => $id]);

    header("Location: ViewStudents.php?deleted=1");
    exit;

} catch (PDOException $e) {
    http_response_code(409);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delete Student | StudentHub</title>
    </head>
    <body style="font-family:Arial,sans-serif;padding:30px;">
        <h2>Unable to Delete Student</h2>
        <p>This student may have related event registrations. Resolve those records before deleting the student.</p>
        <a href="ViewStudents.php">Back to Students</a>
    </body>
    </html>
    <?php
}
?>
