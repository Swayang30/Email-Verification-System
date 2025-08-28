<?php
require_once 'functions.php';

// TODO: Implement the form and logic for email registration and verification
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Step 1: Handle email submission
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['email'] = $email;
            $_SESSION['verification_code'] = generateVerificationCode();
            if (sendVerificationEmail($email, $_SESSION['verification_code'])) {
                $message = "Verification code sent to $email.";
            } else {
                $message = "Failed to send verification code.";
            }
        } else {
            $message = "Invalid email format.";
        }
    }

    // Step 2: Handle code verification
    if (isset($_POST['verification_code'])) {
        $inputCode = trim($_POST['verification_code']);
        if (isset($_SESSION['verification_code']) && $inputCode === $_SESSION['verification_code']) {
            if (registerEmail($_SESSION['email'])) {
                $message = "Email successfully verified and registered.";
                unset($_SESSION['verification_code']);
                unset($_SESSION['email']);
            } else {
                $message = "Registration failed.";
            }
        } else {
            $message = "Invalid verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Registration</title>
</head>
<body>
    <h2>Register for GitHub Timeline Updates</h2>

    <?php if (!empty($message)): ?>
        <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <button id="submit-email">Submit</button>
    </form>

    <form method="POST">
        <label for="verification_code">Verification Code:</label>
        <input type="text" name="verification_code" maxlength="6" required>
        <button id="submit-verification">Verify</button>
    </form>
</body>
</html>
