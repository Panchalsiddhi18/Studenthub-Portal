
<?php
session_start();

// Clear all session data
$_SESSION = [];
session_unset();
session_destroy();

// Prevent protected pages from being shown using browser cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Redirect to the original designed login page
header("Location: Login.html");
exit;
?>
