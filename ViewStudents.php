
<?php
session_start();
require_once "db.php";

// Admin-only access
if (!isset($_SESSION["student_id"])) {
    header("Location: Login.html");
    exit;
}

if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location: Home.php");
    exit;
}

$search = trim($_GET["search"] ?? "");
$students = [];
$error = "";

try {
    if ($search !== "") {
        $sql = "SELECT student_id, full_name, username, email,
                       mobile, course, year, semester, gender
                FROM students
                WHERE role = 'student'
                AND (
                    full_name LIKE :name
                    OR username LIKE :username
                    OR email LIKE :email
                )
                ORDER BY student_id DESC";

        $stmt = $pdo->prepare($sql);
        $keyword = "%" . $search . "%";

        $stmt->execute([
            "name" => $keyword,
            "username" => $keyword,
            "email" => $keyword
        ]);
    } else {
        $sql = "SELECT student_id, full_name, username, email,
                       mobile, course, year, semester, gender
                FROM students
                WHERE role = 'student'
                ORDER BY student_id DESC";

        $stmt = $pdo->query($sql);
    }

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Unable to load students. Please check your database tables.";
}

function safe($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students | StudentHub</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #222;
        }

        header {
            background: #011935;
            color: white;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
        }

        header h2 {
            margin: 0;
        }

        .header-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .header-links a {
            color: white;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 5px;
            background: #2c5aa0;
        }

        .header-links .logout {
            background: #d9534f;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .panel {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px #00000012;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }

        .search-form input {
            flex: 1;
            min-width: 0;
            padding: 12px;
            border: 1px solid #bbb;
            border-radius: 5px;
            font-size: 15px;
        }

        button, .reset {
            border: none;
            border-radius: 5px;
            padding: 12px 18px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }

        button {
            background: #011935;
            color: white;
        }

        .reset {
            background: #e1e1da;
            color: #222;
        }

        .count {
            margin-bottom: 15px;
            color: #444;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 950px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        th {
            background: #011935;
            color: white;
        }

        tbody tr:nth-child(even) {
            background: #f4f6f9;
        }

        .message {
            padding: 12px;
            background: #fff3cd;
            border-radius: 5px;
            margin: 15px 0;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #555;
        }

        @media (max-width: 600px) {
            header {
                padding: 18px;
            }

            .panel {
                padding: 15px;
            }

            .search-form {
                flex-wrap: wrap;
            }

            .search-form input {
                flex-basis: 100%;
            }
        }
    </style>
</head>

<body>

<header>
    <h2>StudentHub Admin Panel</h2>

    <nav class="header-links">
        <a href="AdminDashboard.php">Dashboard</a>
        <a href="logout.php" class="logout">Logout</a>
    </nav>
</header>

<main class="container">
    <section class="panel">
        <h1>View Students</h1>
        <p>View and search registered student records.</p>

        <form class="search-form" method="GET" action="ViewStudents.php">
            <input
                type="search"
                name="search"
                placeholder="Search by name, enrollment ID or email"
                value="<?= safe($search) ?>"
            >

            <button type="submit">Search</button>
            <a class="reset" href="ViewStudents.php">Reset</a>
        </form>

        <?php if ($error !== ""): ?>
            <div class="message"><?= safe($error) ?></div>
        <?php else: ?>

            <p class="count">
                <?php if ($search !== ""): ?>
                    Search results for <strong><?= safe($search) ?></strong>:
                <?php else: ?>
                    Registered students:
                <?php endif; ?>

                <strong><?= count($students) ?></strong>
            </p>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Enrollment ID</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Semester</th>
                            <th>Gender</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($students) > 0): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?= safe($student["student_id"]) ?></td>
                                    <td><?= safe($student["full_name"]) ?></td>
                                    <td><?= safe($student["username"]) ?></td>
                                    <td><?= safe($student["email"]) ?></td>
                                    <td><?= safe($student["mobile"]) ?></td>
                                    <td><?= safe($student["course"]) ?></td>
                                    <td><?= safe($student["year"]) ?></td>
                                    <td><?= safe($student["semester"]) ?></td>
                                    <td><?= safe($student["gender"]) ?></td>
                                    
<td>
    <a href="EditStudent.php?id=<?= (int)$student['student_id'] ?>">
        Edit
    </a>

    <form action="DeleteStudent.php" method="POST"
          onsubmit="return confirm('Are you sure you want to delete this student?');"
          style="display:inline;">
        <input type="hidden" name="id"
               value="<?= (int)$student['student_id'] ?>">
        <button type="submit"
                style="background:#d9534f;color:white;border:0;padding:7px 10px;cursor:pointer;">
            Delete
        </button>
    </form>
</td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10">
                                    No students found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </section>
</main>

<footer>
    StudentHub Portal &copy; <?= date("Y") ?>
</footer>

</body>
</html>
