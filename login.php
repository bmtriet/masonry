<?php
// Must be before ANY HTML output
require_once __DIR__ . '/includes/auth.php'; // Includes session_start()

// If already logged in, redirect to admin page
if (isLoggedIn()) {
    header('Location: admin.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($username, $password)) {
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="w-full max-w-xs">
  <form method="POST" action="login.php" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
     <h1 class="text-2xl font-bold text-center mb-6 text-gray-700">Admin Login</h1>
    <?php if ($error): ?>
        <p class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>
    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
        Username
      </label>
      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" name="username" type="text" placeholder="Username" required>
    </div>
    <div class="mb-6">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
        Password
      </label>
      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" name="password" type="password" placeholder="******************" required>
      <!-- Optional: Add "forgot password" link if needed -->
    </div>
    <div class="flex items-center justify-between">
      <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
        Sign In
      </button>
       <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="index.php">
        Back to Site
      </a>
    </div>
  </form>
  <p class="text-center text-gray-500 text-xs">
    &copy;<?= date('Y') ?> ShoeStore Admin.
  </p>
</div>

</body>
</html>