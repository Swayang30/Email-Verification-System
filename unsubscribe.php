<?php
require_once 'functions.php';

// TODO: Implement the form and logic for email unsubscription.
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Step 1: Handle unsubscribe email submission
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['unsubscribe_email'] = $email;
            $_SESSION['unsubscribe_code'] = generateVerificationCode();
            if (sendVerificationEmail($email, $_SESSION['unsubscribe_code'])) {
                $message = "Unsubscribe verification code sent to $email.";
            } else {
                $message = "Failed to send unsubscribe code.";
            }
        } else {
            $message = "Invalid email format.";
        }
    }

    // Step 2: Handle unsubscribe code verification
    if (isset($_POST['unsubscribe_verification_code'])) {
        $inputCode = trim($_POST['unsubscribe_verification_code']);
        if (isset($_SESSION['unsubscribe_code']) && $inputCode === $_SESSION['unsubscribe_code']) {
            if (unsubscribeEmail($_SESSION['unsubscribe_email'])) {
                $message = "Successfully unsubscribed.";
                unset($_SESSION['unsubscribe_email']);
                unset($_SESSION['unsubscribe_code']);
            } else {
                $message = "Email not found or could not be unsubscribed.";
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
    <title>Unsubscribe</title>
</head>
<body>
    <h2>Unsubscribe from GitHub Timeline Updates</h2>

    <?php if (!empty($message)): ?>
        <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <form method="POST">
        <label for="unsubscribe_email">Email:</label>
        <input type="email" name="unsubscribe_email" required>
        <button id="submit-unsubscribe">Unsubscribe</button>
    </form>

    <form method="POST">
        <label for="unsubscribe_verification_code">Verification Code:</label>
        <input type="text" name="unsubscribe_verification_code">
        <button id="verify-unsubscribe">Verify</button>
    </form>
</body>
</html>


?>
