
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and clean form data
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    // Validate form data
    if ($name == "" || $email == "" || $subject == "" || $message == "") {
        die("Error: All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Please enter a valid email address.");
    }

    // Create data folder if it does not exist
    if (!is_dir("data")) {
        mkdir("data", 0777, true);
    }

    // Open CSV file
    $file = fopen("data/contact_messages.csv", "a");

    if ($file === false) {
        die("Error: Unable to save your message.");
    }

    // Save form data safely as CSV
    if (flock($file, LOCK_EX)) {
        fputcsv($file, [$name, $email, $subject, $message, date("Y-m-d H:i:s")]);
        flock($file, LOCK_UN);
        fclose($file);

        echo "<h2>Message sent successfully!</h2>";
        echo "<p>Thank you for contacting StudentHub Portal.</p>";
        echo '<a href="contact.html">Go Back to Contact Page</a>';
    } else {
        fclose($file);
        die("Error: Unable to save your message.");
    }

} else {
    header("Location: contact.html");
    exit;
}
?>
