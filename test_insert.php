
<?php
require_once "db.php";

$enrollment = "PRACTICAL001";
$name = "Test Student";
$email = "teststudent@example.com";
$mobile = "9876543210";

try {
    $sql = "INSERT INTO students
            (enrollment_id, name, email, mobile)
            VALUES (:enrollment, :name, :email, :mobile)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':enrollment' => $enrollment,
        ':name' => $name,
        ':email' => $email,
        ':mobile' => $mobile
    ]);

    echo "Student inserted successfully!";

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "Student already exists or duplicate data found.";
    } else {
        echo "Unable to insert student.";
    }
}
?>
