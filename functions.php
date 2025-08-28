<?php

/**
 * Generate a 6-digit numeric verification code.
 */
function generateVerificationCode(): string {
    // TODO: Implement this function
    return str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
}

/**
 * Send a verification code to an email.
 */
function sendVerificationEmail(string $email, string $code): bool {
    // TODO: Implement this function
    ini_set("SMTP", "127.0.0.1");
    ini_set("smtp_port", "1025"); // Mailpit or Mailhog port
    ini_set("sendmail_from", "no-reply@example.com");

    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>{$code}</strong></p>";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";
    return mail($email, $subject, $message, $headers);

    // $logFile = __DIR__ . '/email_log.txt';
    // $subject = "To: {$email}\nSubject: Your Verification Code\n";
    // $message = "Your verification code is: {$code}\n";
    // $headers = "From: no-reply@example.com\r\n";



    // return file_put_contents($logFile, "{$subject}{$message}{$headers}\n", FILE_APPEND | LOCK_EX) !== false;

}

/**
 * Register an email by storing it in a file.
 */
function registerEmail(string $email): bool {
  $file = __DIR__ . '/registered_emails.txt';
    // TODO: Implement this function
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];
    if (!in_array($email, $emails)) {
        return file_put_contents($file, $email . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
    }
    return true; // already registered
}

/**
 * Unsubscribe an email by removing it from the list.
 */
function unsubscribeEmail(string $email): bool {
  $file = __DIR__ . '/registered_emails.txt';
    // TODO: Implement this function
    if (!file_exists($file)) return false;

    $emails = file($file, FILE_IGNORE_NEW_LINES);
    $updated = array_filter($emails, fn($e) => trim($e) !== trim($email));
    return file_put_contents($file, implode(PHP_EOL, $updated) . PHP_EOL, LOCK_EX) !== false;
}

/**
 * Fetch GitHub timeline.
 */
function fetchGitHubTimeline() {
    // TODO: Implement this function
     return [
        ['event' => 'Push', 'user' => 'octocat'],
        ['event' => 'Pull Request', 'user' => 'hubbernaut']
    ];
}

/**
 * Format GitHub timeline data. Returns a valid HTML sting.
 */
function formatGitHubData(array $data): string {
    // TODO: Implement this function
    $html = "<h2>GitHub Timeline Updates</h2>";
    $html .= "<table border=\"1\">";
    $html .= "<tr><th>Event</th><th>User</th></tr>";
    foreach ($data as $item) {
        $event = htmlspecialchars($item['event']);
        $user = htmlspecialchars($item['user']);
        $html .= "<tr><td>{$event}</td><td>{$user}</td></tr>";
    }
    $html .= "</table>";
    return $html;
}

/**
 * Send the formatted GitHub updates to registered emails.
 */
function sendGitHubUpdatesToSubscribers(): void {

  ini_set("SMTP", "127.0.0.1");           // Mailpit/MailHog
  ini_set("smtp_port", "1025");
  ini_set("sendmail_from", "no-reply@example.com");

  $file = __DIR__ . '/registered_emails.txt';
    // TODO: Implement this function
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$emails) return;
    $timelineData = fetchGitHubTimeline();
    $formatted = formatGitHubData($timelineData);

    $subject = "Latest GitHub Updates";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";

    foreach ($emails as $email) {
        $unsubscribeLink = "http://localhost:8025/src/unsubscribe.php?email=" . urlencode($email);
        $body = $formatted . "<p><a href=\"{$unsubscribeLink}\" id=\"unsubscribe-button\">Unsubscribe</a></p>";
        
        mail($email, $subject, $body, $headers);
    }
}
