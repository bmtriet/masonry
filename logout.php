<?php
require_once __DIR__ . '/includes/auth.php'; // Includes session_start() and logout() function
logout();
header('Location: login.php'); // Redirect to login page after logout
exit;
?>