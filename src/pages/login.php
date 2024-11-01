<?php
require_once '../../private/required.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a secure token
}
if (isset($_SESSION['user_id'])) {
        header('Location: ../../src/pages/index.php'); // Redirect to login.php
        exit;
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['username_or_email'];
    $password = $_POST['password'];

    $user_id = loginUser($usernameOrEmail, $password);

    if ($user_id) {
        $_SESSION['user_id'] = $user_id; // Store user ID in session
        header('Location: index.php'); // Redirect to homepage after login
        exit;
    } else {
        $error = 'Invalid username/email or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../src/styles/style.css">
</head>
<body>
    <?php include '../../src/components/header.php'; ?>
    
    <main>
        <div class="auth-form">
            <h1>Login</h1>
            <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <label for="username">Username</label>
                <input type="text" name="username_or_email" required>

                <label for="password">Password</label>
                <input type="password" name="password" required>

                <button type="submit">Login</button>
            </form>
            <?php if (isset($error)) echo "<p>$error</p>"; ?>

            <!-- Extra option for register link -->
            <div class="extra-option">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </main>

    <?php include '../../src/components/footer.php'; ?>
</body>
</html>