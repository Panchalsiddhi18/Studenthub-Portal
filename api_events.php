
<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store");

function respond($status, $data) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// 1. Check login and admin role
if (
    empty($_SESSION["student_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    respond(403, ["error" => "Access denied. Admin login required."]);
}

// 2. Check session timeout (15 minutes)
if (
    isset($_SESSION["last_activity"]) &&
    time() - $_SESSION["last_activity"] > 900
) {
    $_SESSION = [];
    session_destroy();
    respond(401, ["error" => "Session expired. Please log in again."]);
}

$_SESSION["last_activity"] = time();

// 3. Check CSRF token for changes
$method = $_SERVER["REQUEST_METHOD"];

if ($method === "POST") {
    $token = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";

    if (
        empty($_SESSION["csrf_token"]) ||
        $token === "" ||
        !hash_equals($_SESSION["csrf_token"], $token)
    ) {
        respond(403, ["error" => "Invalid CSRF token. Refresh the page."]);
    }
}

$file = __DIR__ . "/data/events.json";

if (!file_exists($file)) {
    respond(404, ["error" => "Events file not found"]);
}

$events = json_decode(file_get_contents($file), true);

if (!is_array($events)) {
    respond(500, ["error" => "Invalid events JSON"]);
}

// 4. GET: Read events
if ($method === "GET") {
    echo json_encode($events, JSON_PRETTY_PRINT);
    exit;
}

if ($method !== "POST") {
    header("Allow: GET, POST");
    respond(405, ["error" => "Method not allowed"]);
}

// 5. Read JSON request
$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    respond(400, ["error" => "Invalid JSON input"]);
}

$action = $input["action"] ?? "";

if ($action === "add") {
    $title = trim($input["title"] ?? "");
    $date = trim($input["date"] ?? "");
    $category = trim($input["category"] ?? "");
    $venue = trim($input["venue"] ?? "");
    $description = trim($input["description"] ?? "");

    if (
        $title === "" || $date === "" ||
        $category === "" || $venue === "" ||
        $description === ""
    ) {
        respond(422, ["error" => "All fields are required"]);
    }

    $dateObj = DateTime::createFromFormat("!Y-m-d", $date);

    if (!$dateObj || $dateObj->format("Y-m-d") !== $date) {
        respond(422, ["error" => "Invalid date"]);
    }

    $ids = array_column($events, "id");
    $newId = empty($ids) ? 1 : max($ids) + 1;

    $events[] = [
        "id" => $newId,
        "title" => $title,
        "date" => $date,
        "category" => $category,
        "venue" => $venue,
        "description" => $description
    ];

} elseif ($action === "edit") {
    $id = filter_var($input["id"] ?? null, FILTER_VALIDATE_INT);
    $found = false;

    foreach ($events as &$event) {
        if ($id && $event["id"] == $id) {
            $title = trim($input["title"] ?? "");
            $date = trim($input["date"] ?? "");
            $category = trim($input["category"] ?? "");
            $venue = trim($input["venue"] ?? "");
            $description = trim($input["description"] ?? "");

            if (
                $title === "" || $date === "" ||
                $category === "" || $venue === "" ||
                $description === ""
            ) {
                unset($event);
                respond(422, ["error" => "All fields are required"]);
            }

            $dateObj = DateTime::createFromFormat("!Y-m-d", $date);

            if (!$dateObj || $dateObj->format("Y-m-d") !== $date) {
                unset($event);
                respond(422, ["error" => "Invalid date"]);
            }

            $event["title"] = $title;
            $event["date"] = $date;
            $event["category"] = $category;
            $event["venue"] = $venue;
            $event["description"] = $description;

            $found = true;
            break;
        }
    }
    unset($event);

    if (!$found) {
        respond(404, ["error" => "Event not found"]);
    }

} elseif ($action === "delete") {
    $id = filter_var($input["id"] ?? null, FILTER_VALIDATE_INT);

    if (!$id) {
        respond(400, ["error" => "Invalid event ID"]);
    }

    $oldCount = count($events);

    $events = array_values(array_filter(
        $events,
        fn($event) => $event["id"] != $id
    ));

    if (count($events) === $oldCount) {
        respond(404, ["error" => "Event not found"]);
    }

} else {
    respond(400, ["error" => "Unknown action"]);
}

// 6. Save updated JSON
$json = json_encode(
    $events,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
);

if ($json === false ||
    file_put_contents($file, $json, LOCK_EX) === false) {
    respond(500, ["error" => "Unable to save events"]);
}

echo json_encode([
    "message" => "Operation successful",
    "events" => $events
]);
?>
