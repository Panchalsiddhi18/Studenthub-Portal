
<?php
session_start();
require_once "db.php";

// Check login
if (!isset($_SESSION["student_id"])) {
    header("Location: Login.html");
    exit;
}

// Only admin can access this page
if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location: Home.php");
    exit;
}

// Session timeout: 15 minutes
if (
    isset($_SESSION["last_activity"]) &&
    time() - (int)$_SESSION["last_activity"] > 900
) {
    session_unset();
    session_destroy();
    header("Location: Login.html");
    exit;
}
$_SESSION["last_activity"] = time();

// CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Escape HTML output
function h($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Save admin activity in audit_logs
function logAdminActivity($pdo, $action, $details) {
    $adminId = (int)($_SESSION["student_id"] ?? 0);

    $stmt = $pdo->prepare(
        "INSERT INTO audit_logs (admin_id, action, details)
         VALUES (:admin_id, :action, :details)"
    );

    $stmt->execute([
        "admin_id" => $adminId,
        "action" => $action,
        "details" => $details
    ]);
}

// Redirect after successful action
function redirectEvents($status) {
    header("Location: ManageEvents.php?" . $status);
    exit;
}

// Upload event poster securely
function uploadPoster($file) {
    if (
        !isset($file) ||
        $file["error"] === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    if ($file["error"] !== UPLOAD_ERR_OK) {
        throw new RuntimeException("Poster upload failed.");
    }

    if ($file["size"] > 2 * 1024 * 1024) {
        throw new RuntimeException(
            "Poster must be 2 MB or smaller."
        );
    }

    if (!is_uploaded_file($file["tmp_name"])) {
        throw new RuntimeException("Invalid uploaded file.");
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file["tmp_name"]);

    $allowed = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp"
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException(
            "Only JPG, PNG and WEBP posters are allowed."
        );
    }

    $folder = __DIR__ . "/uploads/events/";

    if (
        !is_dir($folder) &&
        !mkdir($folder, 0755, true) &&
        !is_dir($folder)
    ) {
        throw new RuntimeException(
            "Cannot create poster folder."
        );
    }

    $filename = bin2hex(random_bytes(16))
        . "." . $allowed[$mime];

    if (
        !move_uploaded_file(
            $file["tmp_name"],
            $folder . $filename
        )
    ) {
        throw new RuntimeException(
            "Could not save poster."
        );
    }

    return $filename;
}

// Remove poster safely
function removePoster($filename) {
    if (!$filename) {
        return;
    }

    $filename = basename($filename);
    $path = __DIR__ . "/uploads/events/" . $filename;

    if (is_file($path)) {
        unlink($path);
    }
}

$error = "";
$success = "";
$editEvent = null;

$statusMessages = [
    "added"   => "Event added successfully!",
    "updated" => "Event updated successfully!",
    "deleted" => "Event deleted successfully!"
];

foreach ($statusMessages as $key => $message) {
    if (isset($_GET[$key])) {
        $success = $message;
        break;
    }
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["csrf_token"], $token)
    ) {
        http_response_code(403);
        exit("Invalid request token. Please reload the page.");
    }

    $action = $_POST["action"] ?? "";

    try {

        // ADD OR UPDATE EVENT
        if ($action === "add" || $action === "update") {

            $id = filter_input(
                INPUT_POST,
                "event_id",
                FILTER_VALIDATE_INT
            );

            $name = trim($_POST["event_name"] ?? "");
            $category = trim($_POST["category"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $date = trim($_POST["event_date"] ?? "");
            $venue = trim($_POST["venue"] ?? "");

            if (
                $name === "" ||
                $category === "" ||
                $description === "" ||
                $date === "" ||
                $venue === ""
            ) {
                throw new RuntimeException(
                    "Please fill in all fields."
                );
            }

            if (
                mb_strlen($name) > 200 ||
                mb_strlen($category) > 100 ||
                mb_strlen($venue) > 255
            ) {
                throw new RuntimeException(
                    "One or more fields are too long."
                );
            }

            $dateObject = DateTime::createFromFormat(
                "!Y-m-d",
                $date
            );
            $dateErrors = DateTime::getLastErrors();

            if (
                !$dateObject ||
                $dateObject->format("Y-m-d") !== $date ||
                (
                    $dateErrors !== false &&
                    (
                        $dateErrors["warning_count"] > 0 ||
                        $dateErrors["error_count"] > 0
                    )
                )
            ) {
                throw new RuntimeException(
                    "Please enter a valid event date."
                );
            }

            if ($action === "update" && !$id) {
                throw new RuntimeException("Invalid event ID.");
            }

            $oldPoster = null;
            $newPoster = null;
            $eventId = null;

            // Find existing event for update
            if ($action === "update") {
                $find = $pdo->prepare(
                    "SELECT event_id, event_name, poster
                     FROM events
                     WHERE event_id = :id"
                );
                $find->execute(["id" => $id]);
                $existing = $find->fetch(PDO::FETCH_ASSOC);

                if (!$existing) {
                    throw new RuntimeException(
                        "Event not found."
                    );
                }

                $oldPoster = $existing["poster"];
                $eventId = (int)$existing["event_id"];
            }

            // Poster is optional
            $newPoster = uploadPoster(
                $_FILES["poster"] ?? null
            );

            $poster = $newPoster ?? $oldPoster;

            try {

                if ($action === "add") {

                    $stmt = $pdo->prepare(
                        "INSERT INTO events
                        (
                            event_name,
                            category,
                            description,
                            event_date,
                            venue,
                            poster
                        )
                        VALUES
                        (
                            :name,
                            :category,
                            :description,
                            :event_date,
                            :venue,
                            :poster
                        )"
                    );

                    $stmt->execute([
                        "name" => $name,
                        "category" => $category,
                        "description" => $description,
                        "event_date" => $date,
                        "venue" => $venue,
                        "poster" => $poster
                    ]);

                    $eventId = (int)$pdo->lastInsertId();

                    // Record activity
                    logAdminActivity(
                        $pdo,
                        "Added Event",
                        "Event: " . $name .
                        " (ID: " . $eventId . ")"
                    );

                    redirectEvents("added=1");

                } else {

                    $stmt = $pdo->prepare(
                        "UPDATE events SET
                            event_name = :name,
                            category = :category,
                            description = :description,
                            event_date = :event_date,
                            venue = :venue,
                            poster = :poster
                         WHERE event_id = :id"
                    );

                    $stmt->execute([
                        "name" => $name,
                        "category" => $category,
                        "description" => $description,
                        "event_date" => $date,
                        "venue" => $venue,
                        "poster" => $poster,
                        "id" => $id
                    ]);

                    // Record activity
                    logAdminActivity(
                        $pdo,
                        "Updated Event",
                        "Event: " . $name .
                        " (ID: " . $id . ")"
                    );

                    if ($newPoster !== null) {
                        removePoster($oldPoster);
                    }

                    redirectEvents("updated=1");
                }

            } catch (Throwable $e) {

                // Remove newly uploaded file if operation fails
                if ($newPoster !== null) {
                    removePoster($newPoster);
                }

                throw $e;
            }

        // DELETE EVENT
        } elseif ($action === "delete") {

            $id = filter_input(
                INPUT_POST,
                "event_id",
                FILTER_VALIDATE_INT
            );

            if (!$id) {
                throw new RuntimeException(
                    "Invalid event ID."
                );
            }

            $find = $pdo->prepare(
                "SELECT event_id, event_name, poster
                 FROM events
                 WHERE event_id = :id"
            );

            $find->execute(["id" => $id]);
            $event = $find->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                throw new RuntimeException(
                    "Event not found."
                );
            }

            // Save the activity before deleting the event
            $pdo->beginTransaction();

            try {

                $stmt = $pdo->prepare(
                    "DELETE FROM events WHERE event_id = :id"
                );
                $stmt->execute(["id" => $id]);

                logAdminActivity(
                    $pdo,
                    "Deleted Event",
                    "Event: " . $event["event_name"] .
                    " (ID: " . $id . ")"
                );

                $pdo->commit();

            } catch (Throwable $e) {

                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                throw $e;
            }

            removePoster($event["poster"]);

            redirectEvents("deleted=1");

        } else {

            throw new RuntimeException("Invalid action.");
        }

    } catch (RuntimeException $e) {

        $error = $e->getMessage();

    } catch (PDOException $e) {

        error_log(
            "ManageEvents database error: " . $e->getMessage()
        );

        $error = "Database operation failed. Please check your database tables.";

    } catch (Throwable $e) {

        error_log(
            "ManageEvents error: " . $e->getMessage()
        );

        $error = "Something went wrong. Please try again.";
    }
}

// Load selected event for editing
if (isset($_GET["edit"])) {

    $editId = filter_input(
        INPUT_GET,
        "edit",
        FILTER_VALIDATE_INT
    );

    if ($editId) {

        $stmt = $pdo->prepare(
            "SELECT event_id, event_name, category,
                    description, event_date, venue, poster
             FROM events
             WHERE event_id = :id"
        );

        $stmt->execute(["id" => $editId]);
        $editEvent = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$editEvent) {
            $error = "Event not found.";
        }

    } else {
        $error = "Invalid event ID.";
    }
}

// Load all events
try {

    $stmt = $pdo->query(
        "SELECT event_id, event_name, category,
                description, event_date, venue,
                poster, created_at
         FROM events
         ORDER BY event_date DESC"
    );

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    error_log(
        "Events loading error: " . $e->getMessage()
    );

    $events = [];
    $error = "Unable to load events. Please check your database.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Events | StudentHub</title>

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
    flex-wrap: wrap;
    gap: 12px;
}

header h2 {
    margin: 0;
}

header a {
    color: white;
    text-decoration: none;
    background: #2c5aa0;
    padding: 10px 14px;
    border-radius: 5px;
}

.container {
    max-width: 1150px;
    margin: 30px auto;
    padding: 0 20px;
}

.panel {
    background: white;
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px #00000012;
}

label {
    display: block;
    margin: 14px 0 6px;
    font-weight: bold;
}

input, textarea {
    width: 100%;
    padding: 11px;
    border: 1px solid #bbb;
    border-radius: 5px;
    font: inherit;
}

textarea {
    min-height: 90px;
    resize: vertical;
}

button, .action {
    display: inline-block;
    margin-top: 10px;
    padding: 10px 14px;
    background: #011935;
    color: white;
    border: 0;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    font: inherit;
}

.cancel {
    background: #646f7e;
}

.delete {
    background: #8a1111;
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
}

th, td {
    text-align: left;
    padding: 12px;
    border: 1px solid #ddd;
    vertical-align: top;
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
    border-radius: 5px;
    margin: 12px 0;
}

.success {
    background: #d9f4df;
    color: #145523;
}

.error {
    background: #ffe1e1;
    color: #8a1111;
}

.poster {
    width: 90px;
    max-height: 90px;
    object-fit: contain;
}

.actions {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
}

.actions form {
    margin: 0;
}

@media (max-width: 600px) {
    header {
        padding: 18px;
    }

    .container {
        padding: 0 12px;
        margin: 18px auto;
    }

    .panel {
        padding: 16px;
    }
}
</style>
</head>

<body>

<header>
    <h2>StudentHub Admin Panel</h2>
    <a href="AdminDashboard.php">Back to Dashboard</a>
</header>

<main class="container">

<section class="panel">

    <h1>Manage Events</h1>
    <p>Add, view, edit and delete college events.</p>

    <?php if ($success !== ""): ?>
        <div class="message success">
            <?= h($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="message error">
            <?= h($error) ?>
        </div>
    <?php endif; ?>

    <h2>
        <?= $editEvent ? "Edit Event" : "Add New Event" ?>
    </h2>

    <form
        method="POST"
        action="ManageEvents.php<?= $editEvent ? "?edit=" . (int)$editEvent["event_id"] : "" ?>"
        enctype="multipart/form-data"
    >

        <input
            type="hidden"
            name="csrf_token"
            value="<?= h($_SESSION["csrf_token"]) ?>"
        >

        <input
            type="hidden"
            name="action"
            value="<?= $editEvent ? "update" : "add" ?>"
        >

        <?php if ($editEvent): ?>
            <input
                type="hidden"
                name="event_id"
                value="<?= (int)$editEvent["event_id"] ?>"
            >
        <?php endif; ?>

        <label for="event_name">Event Name</label>
        <input
            type="text"
            id="event_name"
            name="event_name"
            maxlength="200"
            required
            value="<?= h($editEvent["event_name"] ?? "") ?>"
        >

        <label for="category">Category</label>
        <input
            type="text"
            id="category"
            name="category"
            maxlength="100"
            required
            value="<?= h($editEvent["category"] ?? "") ?>"
        >

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            required
        ><?= h($editEvent["description"] ?? "") ?></textarea>

        <label for="event_date">Event Date</label>
        <input
            type="date"
            id="event_date"
            name="event_date"
            required
            value="<?= h($editEvent["event_date"] ?? "") ?>"
        >

        <label for="venue">Venue</label>
        <input
            type="text"
            id="venue"
            name="venue"
            maxlength="255"
            required
            value="<?= h($editEvent["venue"] ?? "") ?>"
        >

        <label for="poster">
            Event Poster (JPG, PNG or WEBP; max 2 MB)
        </label>

        <input
            type="file"
            id="poster"
            name="poster"
            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
        >

        <?php if (!empty($editEvent["poster"])): ?>
            <p>Current poster:</p>

            <img
                class="poster"
                src="<?= "uploads/events/" . rawurlencode(basename($editEvent["poster"])) ?>"
                alt="Current event poster"
            >

            <p>
                Choose a new poster only if you want to replace the current one.
            </p>
        <?php endif; ?>

        <button type="submit">
            <?= $editEvent ? "Save Changes" : "Add Event" ?>
        </button>

        <?php if ($editEvent): ?>
            <a class="action cancel" href="ManageEvents.php">
                Cancel Edit
            </a>
        <?php endif; ?>

    </form>

</section>

<section class="panel">

    <h2>All Events (<?= count($events) ?>)</h2>

    <div class="table-wrapper">

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Event Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($events): ?>

                <?php foreach ($events as $event): ?>

                    <tr>
                        <td><?= h($event["event_id"]) ?></td>

                        <td>
                            <?php if (!empty($event["poster"])): ?>
                                <img
                                    class="poster"
                                    src="<?= "uploads/events/" . rawurlencode(basename($event["poster"])) ?>"
                                    alt="Event poster"
                                >
                            <?php else: ?>
                                No poster
                            <?php endif; ?>
                        </td>

                        <td><?= h($event["event_name"]) ?></td>
                        <td><?= h($event["category"]) ?></td>
                        <td><?= h($event["description"]) ?></td>
                        <td><?= h($event["event_date"]) ?></td>
                        <td><?= h($event["venue"]) ?></td>

                        <td>
                            <div class="actions">

                                <a
                                    class="action"
                                    href="ManageEvents.php?edit=<?= (int)$event["event_id"] ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="ManageEvents.php"
                                    onsubmit="return confirm('Are you sure you want to delete this event?');"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= h($_SESSION["csrf_token"]) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="event_id"
                                        value="<?= (int)$event["event_id"] ?>"
                                    >

                                    <button
                                        class="delete"
                                        type="submit"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8">No events found.</td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>

    </div>

</section>

</main>

</body>
</html>
