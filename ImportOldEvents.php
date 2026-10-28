
<?php
session_start();

require_once "db.php";

if (($_SESSION["role"] ?? "") !== "admin") {
    http_response_code(403);
    exit("Only admin can import old events. Please login as admin.");
}

$jsonFile = __DIR__ . "/data/events.json";

if (!file_exists($jsonFile)) {
    exit("Error: data/events.json file not found.");
}

$events = json_decode(file_get_contents($jsonFile), true);

if (!is_array($events)) {
    exit("Error: Invalid events JSON file.");
}

$imported = 0;
$skipped = 0;

try {
    $pdo->beginTransaction();

    $check = $pdo->prepare(
        "SELECT event_id FROM events
         WHERE event_name = :name AND event_date = :event_date
         LIMIT 1"
    );

    $insert = $pdo->prepare(
        "INSERT INTO events
        (event_name, category, description, event_date, venue, poster)
        VALUES
        (:name, :category, :description, :event_date, :venue, :poster)"
    );

    foreach ($events as $event) {
        $name = trim($event["title"] ?? "");
        $date = trim($event["date"] ?? "");

        if ($name === "" || $date === "") {
            $skipped++;
            continue;
        }

        $check->execute([
            ":name" => $name,
            ":event_date" => $date
        ]);

        if ($check->fetch()) {
            $skipped++;
            continue;
        }

        $insert->execute([
            ":name" => $name,
            ":category" => $event["category"] ?? "Other",
            ":description" => $event["description"] ?? "",
            ":event_date" => $date,
            ":venue" => $event["venue"] ?? "",
            ":poster" => null
        ]);

        $imported++;
    }

    $pdo->commit();

    echo "<h2>Old Events Import Completed!</h2>";
    echo "<p>Events added: " . $imported . "</p>";
    echo "<p>Events skipped because they already exist or have invalid data: "
        . $skipped . "</p>";
    echo '<p><a href="ManageEvents.php">Back to Manage Events</a></p>';
    echo '<p><a href="Events.html">Open Student Events Page</a></p>';

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo "Import failed. Check the events table and database connection.";
}
?>
