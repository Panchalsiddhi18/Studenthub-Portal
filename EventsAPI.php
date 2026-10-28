
<?php
session_start();

if (
    empty($_SESSION["student_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    header("Location: Login.html");
    exit;
}

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION["csrf_token"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Events API</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            color: #222;
        }

        header {
            background: #011935;
            color: white;
            padding: 20px;
        }

        main {
            max-width: 1000px;
            margin: 25px auto;
            padding: 15px;
        }

        .panel {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 6px 0 12px;
            box-sizing: border-box;
        }

        button {
            background: #011935;
            color: white;
            border: 0;
            padding: 10px 14px;
            border-radius: 4px;
            cursor: pointer;
            margin: 3px;
        }

        .delete {
            background: #8a1111;
        }

        .event {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            margin: 12px 0;
        }

        #message {
            margin: 12px 0;
        }

        
.page-header {
    background: #011935;
    color: white;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.page-header h2 {
    margin: 0;
}

.dashboard-btn {
    color: white;
    background: #2c5aa0;
    padding: 10px 14px;
    border-radius: 5px;
    text-decoration: none;
}

.dashboard-btn:hover {
    background: #1e4278;
}

    </style>
</head>

<body>

<header class="page-header">
    <h2>StudentHub Admin Panel</h2>
    <a href="AdminDashboard.php" class="dashboard-btn">
        ← Back to Dashboard
    </a>
</header>


<main>

    <section class="panel">
        <h2 id="formTitle">Add Event</h2>

        <form id="eventForm">
            <input type="hidden" id="eventId">

            <label>Event Name</label>
            <input id="title" maxlength="200" required>

            <label>Date</label>
            <input id="date" type="date" required>

            <label>Category</label>
            <input id="category" maxlength="100" required>

            <label>Venue</label>
            <input id="venue" maxlength="255" required>

            <label>Description</label>
            <textarea id="description" required></textarea>

            <button type="submit">Save Event</button>
            <button type="button" id="cancelEdit">Cancel Edit</button>
        </form>

        <p id="message" role="status"></p>
    </section>

    <section class="panel">
        <h2>All Events</h2>
        <button type="button" id="reload">Refresh Events</button>
        <div id="events">Loading events...</div>
    </section>

</main>

<script>
const api = "api_events.php";
const csrfToken = <?= json_encode($csrfToken) ?>;

const eventsContainer = document.getElementById("events");
const message = document.getElementById("message");
const form = document.getElementById("eventForm");

// Common function for POST requests
async function postEvent(payload) {
    const response = await fetch(api, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": csrfToken
        },
        body: JSON.stringify(payload)
    });

    const result = await response.json();

    if (!response.ok) {
        throw new Error(result.error || "Request failed");
    }

    return result;
}

// Load events from API
async function loadEvents() {
    eventsContainer.textContent = "Loading events...";

    try {
        const response = await fetch(api, {
            method: "GET",
            cache: "no-store"
        });

        if (!response.ok) {
            const result = await response.json();
            throw new Error(result.error || "Could not load events");
        }

        const events = await response.json();
        eventsContainer.replaceChildren();

        if (!Array.isArray(events)) {
            throw new Error("Invalid events response");
        }

        if (events.length === 0) {
            eventsContainer.textContent = "No events found.";
            return;
        }

        events.forEach(event => {
            const card = document.createElement("div");
            card.className = "event";

            const heading = document.createElement("h3");
            heading.textContent = event.title;
            card.appendChild(heading);

            const details = document.createElement("p");
            details.textContent =
                `${event.date} | ${event.category} | ${event.venue}`;
            card.appendChild(details);

            const description = document.createElement("p");
            description.textContent = event.description;
            card.appendChild(description);

            // Edit button
            const edit = document.createElement("button");
            edit.textContent = "Edit";

            edit.addEventListener("click", () => {
                document.getElementById("eventId").value = event.id;
                document.getElementById("title").value = event.title;
                document.getElementById("date").value = event.date;
                document.getElementById("category").value = event.category;
                document.getElementById("venue").value = event.venue;
                document.getElementById("description").value =
                    event.description;

                document.getElementById("formTitle").textContent = "Edit Event";

                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
            });

            card.appendChild(edit);

            // Delete button
            const del = document.createElement("button");
            del.textContent = "Delete";
            del.className = "delete";

            del.addEventListener("click", () => {
                deleteEvent(event.id);
            });

            card.appendChild(del);
            eventsContainer.appendChild(card);
        });

    } catch (error) {
        eventsContainer.textContent = error.message;
    }
}

// Add or edit event
form.addEventListener("submit", async event => {
    event.preventDefault();

    const id = document.getElementById("eventId").value;

    const payload = {
        action: id ? "edit" : "add",
        title: document.getElementById("title").value.trim(),
        date: document.getElementById("date").value,
        category: document.getElementById("category").value.trim(),
        venue: document.getElementById("venue").value.trim(),
        description: document.getElementById("description").value.trim()
    };

    if (id) {
        payload.id = Number(id);
    }

    try {
        const result = await postEvent(payload);

        message.textContent = result.message;
        form.reset();

        document.getElementById("eventId").value = "";
        document.getElementById("formTitle").textContent = "Add Event";

        await loadEvents();

    } catch (error) {
        message.textContent = error.message;
    }
});

// Delete event
async function deleteEvent(id) {
    if (!confirm("Are you sure you want to delete this event?")) {
        return;
    }

    try {
        const result = await postEvent({
            action: "delete",
            id: Number(id)
        });

        message.textContent = result.message;
        await loadEvents();

    } catch (error) {
        message.textContent = error.message;
    }
}

// Cancel edit
document.getElementById("cancelEdit").addEventListener("click", () => {
    form.reset();
    document.getElementById("eventId").value = "";
    document.getElementById("formTitle").textContent = "Add Event";
    message.textContent = "";
});

// Refresh events
document.getElementById("reload").addEventListener("click", loadEvents);

// Initial load
loadEvents();
</script>

</body>


</html>
