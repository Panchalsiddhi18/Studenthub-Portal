
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

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id && $_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
}

if (!$id) {
    header("Location: ViewStudents.php");
    exit;
}

$error = "";
$success = "";

$stmt = $pdo->prepare(
    "SELECT student_id, full_name, username, email, mobile,
            course, year, semester, gender
     FROM students
     WHERE student_id = :id AND role = 'student'"
);
$stmt->execute(["id" => $id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header("Location: ViewStudents.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $year = trim($_POST["year"] ?? "");
    $semester = filter_input(INPUT_POST, "semester", FILTER_VALIDATE_INT);
    $gender = trim($_POST["gender"] ?? "");

    if ($name === "" || $email === "" || $mobile === "" ||
        $course === "" || $year === "" || !$semester ||
        $semester < 1 || $semester > 12 || $gender === "") {
        $error = "Please fill all fields correctly.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            $update = $pdo->prepare(
                "UPDATE students
                 SET full_name = :name,
                     email = :email,
                     mobile = :mobile,
                     course = :course,
                     year = :year,
                     semester = :semester,
                     gender = :gender
                 WHERE student_id = :id AND role = 'student'"
            );

            $update->execute([
                "name" => $name,
                "email" => $email,
                "mobile" => $mobile,
                "course" => $course,
                "year" => $year,
                "semester" => $semester,
                "gender" => $gender,
                "id" => $id
            ]);

            header("Location: ViewStudents.php?updated=1");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $error = "This email is already used by another account.";
            } else {
                $error = "Unable to update student. Please try again.";
            }
        }
    }

    $student["full_name"] = $name;
    $student["email"] = $email;
    $student["mobile"] = $mobile;
    $student["course"] = $course;
    $student["year"] = $year;
    $student["semester"] = $semester;
    $student["gender"] = $gender;
}

function h($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Student | StudentHub</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: Arial, sans-serif; background: #f4f6f9; color: #222; }
header { background: #011935; color: white; padding: 20px 30px; }
header a { color: white; text-decoration: none; }
.container { max-width: 700px; margin: 30px auto; padding: 0 20px; }
.panel { background: white; padding: 25px; border-radius: 8px; }
label { display: block; margin: 14px 0 6px; font-weight: bold; }
input, select { width: 100%; padding: 11px; border: 1px solid #bbb; border-radius: 5px; }
button, .back { display: inline-block; margin-top: 20px; padding: 12px 18px; border: 0; border-radius: 5px; background: #011935; color: white; text-decoration: none; cursor: pointer; }
.back { background: #646f7e; margin-left: 8px; }
.error { background: #ffe1e1; padding: 12px; border-radius: 5px; color: #8a1111; }
</style>
</head>
<body>
<header>
    <h2>StudentHub Admin Panel</h2>
    <a href="ViewStudents.php">← Back to Students</a>
</header>

<main class="container">
<section class="panel">
<h1>Edit Student</h1>
<p>Enrollment ID: <strong><?= h($student["username"]) ?></strong></p>

<?php if ($error !== ""): ?>
    <p class="error"><?= h($error) ?></p>
<?php endif; ?>

<form method="POST" action="EditStudent.php?id=<?= (int)$id ?>">
    <input type="hidden" name="id" value="<?= (int)$id ?>">

    <label for="full_name">Full Name</label>
    <input id="full_name" name="full_name" value="<?= h($student["full_name"]) ?>" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= h($student["email"]) ?>" required>

    <label for="mobile">Mobile</label>
    <input id="mobile" name="mobile" value="<?= h($student["mobile"]) ?>" required>

    <label for="course">Course</label>
    <input id="course" name="course" value="<?= h($student["course"]) ?>" required>

    <label for="year">Year</label>
    <input id="year" name="year" value="<?= h($student["year"]) ?>" required>

    <label for="semester">Semester</label>
    <input type="number" id="semester" name="semester" min="1" max="12" value="<?= h($student["semester"]) ?>" required>

    <label for="gender">Gender</label>
    <select id="gender" name="gender" required>
        <option value="">Select Gender</option>
        <option value="Female" <?= $student["gender"] === "Female" ? "selected" : "" ?>>Female</option>
        <option value="Male" <?= $student["gender"] === "Male" ? "selected" : "" ?>>Male</option>
        <option value="Other" <?= $student["gender"] === "Other" ? "selected" : "" ?>>Other</option>
    </select>

    <button type="submit">Save Changes</button>
    <a class="back" href="ViewStudents.php">Cancel</a>
</form>
</section>
</main>
</body>
</html>
