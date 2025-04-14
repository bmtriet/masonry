<?php
// Start session management
// Ensure this is called BEFORE any output is sent to the browser
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// !!! INSECURE: Hardcoded credentials. Use environment variables or secure config in production !!!
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'zaq@123123'); // Consider hashing the password in a real app

function isLoggedIn() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

function login($username, $password) {
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        // In a real app, you would verify a hashed password here: password_verify($password, HASHED_ADMIN_PASSWORD)
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username; // Store username if needed
        return true;
    }
    return false;
}

function logout() {
    // Unset all session variables
    $_SESSION = array();

    // If session cookies are used, delete the cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php'); // Redirect to login page
        exit; // Stop script execution
    }
}

?>