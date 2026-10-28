
<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

try {
    $sql = "SELECT event_id, event_name, category, description,
                   event_date, venue, poster
            FROM events
            ORDER BY event_date ASC, event_id DESC";

    $stmt = $pdo->query($sql);
    $events = $stmt->fetchAll();

    $result = [];

    foreach ($events as $event) {
        $result[] = [
            "id" => $event["event_id"],
            "title" => $event["event_name"],
            "category" => $event["category"],
            "description" => $event["description"],
            "date" => $event["event_date"],
            "venue" => $event["venue"],
            "poster" => $event["poster"]
        ];
    }

    echo json_encode(
        $result,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        "error" => "Unable to load events from database."
    ]);
}
?>
