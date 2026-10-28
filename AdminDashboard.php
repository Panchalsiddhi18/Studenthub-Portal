
<?php
session_start();

require_once "db.php";

// 1. Check login
if (!isset($_SESSION["student_id"])) {
    header("Location: Login.html");
    exit;
}

// 2. Only admins can access this page
if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location: Home.php");
    exit;
}

$adminName = $_SESSION["full_name"] ?? "Admin";

$totalStudents = 0;
$totalEvents = 0;
$totalRegistrations = 0;
$auditLogs = [];
$dashboardError = "";

// 3. Get dashboard statistics
try {
    $stmt = $pdo->query(
        "SELECT COUNT(*) FROM students WHERE role = 'student'"
    );
    $totalStudents = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM events");
    $totalEvents = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM registrations");
    $totalRegistrations = (int) $stmt->fetchColumn();

} catch (PDOException $e) {
    $dashboardError = "Some dashboard statistics could not be loaded. Check your database tables.";
}

// 4. Get recent audit logs
try {
    $stmt = $pdo->query(
        "SELECT id, admin_id, action, details, created_at
         FROM audit_logs
         ORDER BY id DESC
         LIMIT 10"
    );

    $auditLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $auditLogs = [];
    $dashboardError .= " Audit log table is not available yet.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | StudentHub</title>

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
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        header h2 {
            margin: 0;
        }

        .logout {
            background: #d9534f;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 5px;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .welcome {
            margin-bottom: 25px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px #00000012;
            border-top: 4px solid #2c5aa0;
        }

        .card h3 {
            margin-top: 0;
            font-size: 17px;
        }

        .number {
            font-size: 32px;
            font-weight: bold;
            color: #2c5aa0;
        }

        .section {
            background: white;
            padding: 25px;
            margin-top: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px #0000000a;
        }

        .links {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 15px;
        }

        .links a {
            background: #011935;
            color: white;
            padding: 12px 16px;
            border-radius: 5px;
            text-decoration: none;
        }

        .links a:hover {
            background: #2c5aa0;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th {
            background: #011935;
            color: white;
            text-align: left;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        tbody tr:hover {
            background: #f4f6f9;
        }

        .notice {
            background: #fff3cd;
            color: #664d03;
            border: 1px solid #ffecb5;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .empty {
            color: #666;
            padding: 12px 0;
        }

        footer {
            text-align: center;
            padding: 20px;
            margin-top: 35px;
            color: #555;
        }

        @media (max-width: 700px) {
            .cards {
                grid-template-columns: 1fr;
            }

            header {
                padding: 18px;
            }

            .container {
                padding: 0 12px;
            }

            .section {
                padding: 16px;
            }
        }
    </style>
</head>

<body>

<header>
    <h2>StudentHub Admin Panel</h2>
    <a class="logout" href="logout.php">Logout</a>
</header>

<main class="container">

    <section class="welcome">
        <h1>Welcome, <?= htmlspecialchars($adminName, ENT_QUOTES, "UTF-8") ?>!</h1>
        <p>Manage your StudentHub Portal from this dashboard.</p>
    </section>

    <?php if ($dashboardError !== ""): ?>
        <div class="notice">
            <?= htmlspecialchars($dashboardError, ENT_QUOTES, "UTF-8") ?>
        </div>
    <?php endif; ?>

    <!-- Dashboard Statistics -->
    <section class="cards">

        <div class="card">
            <h3>Total Students</h3>
            <div class="number"><?= $totalStudents ?></div>
        </div>

        <div class="card">
            <h3>Total Events</h3>
            <div class="number"><?= $totalEvents ?></div>
        </div>

        <div class="card">
            <h3>Total Event Registrations</h3>
            <div class="number"><?= $totalRegistrations ?></div>
        </div>

    </section>

    <!-- Admin Actions -->
    <section class="section">
        <h2>Admin Actions</h2>
        <p>Choose an area to manage.</p>

        <div class="links">
            <a href="AdminDashboard.php">Dashboard</a>
            <a href="ViewStudents.php">View / Search Students</a>
            <a href="ManageEvents.php">Manage Events</a>
            <a href="EventsAPI.php">Events API</a>
            <a href="Home.php">View Student Portal</a>
            <a href="logout.php">Logout</a>
        </div>
    </section>

    <!-- Audit Log -->
    <section class="section">
        <h2>Recent Admin Activities</h2>
        <p>Latest activity records stored in the audit log.</p>

        <?php if (empty($auditLogs)): ?>

            <p class="empty">
                No activity records found yet. Activity logging must be enabled
                in the relevant admin actions.
            </p>

        <?php else: ?>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Admin ID</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>Date &amp; Time</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($auditLogs as $log): ?>
                            <tr>
                                <td>
                                    <?= (int) $log["admin_id"] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $log["action"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $log["details"] ?? "",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $log["created_at"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
