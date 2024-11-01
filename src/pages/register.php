<?php
require_once '../../private/required.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../../src/pages/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Set the directory where profile pictures will be uploaded
    $uploadDir = __DIR__ . '/../../src/pictures/users/';
    $fileName = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
    $targetFile = $uploadDir . $fileName;

    // Check if the upload directory exists, if not create it
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            die('Failed to create upload directory');  // Error handling
        }
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
        // Save the relative path to the database
        $profilePicture = '../../src/pictures/users/' . $fileName;
    } else {
        // Handle errors (e.g., file not uploaded)
        $profilePicture = null;
        $error = 'Error uploading the profile picture.';
    }

    // Save user to the database (make sure to include profilePicture in the query)
    $register_success = registerUser($username, $email, $password, $profilePicture);

    if ($register_success) {
        header('Location: login.php');
        exit;
    } else {
        $error = 'Registration failed. Username or email might already be in use.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../../src/styles/style.css">
</head>
<body>
    <?php include '../../src/components/header.php'; ?>
    
    <main>
        <div class="auth-form">
            <h1>Register</h1>
            <form method="POST" enctype="multipart/form-data"> <!-- Add enctype here -->
                <label for="username">Username</label>
                <input type="text" name="username" required>
                
                <label for="email">Email</label>
                <input type="email" name="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" required>
                <label for="profile_picture">Choose Profile Picture</label>
                <label for="profile_picture" class="custom-file-upload">
                    By clicking here.
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                </label>

                <button type="submit">Register</button>
            </form>
            <?php if (isset($error)) echo "<p>$error</p>"; ?>

            <!-- Extra option for login link -->
            <div class="extra-option">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </main>

    <?php include '../../src/components/footer.php'; ?>
</body>
</html>
