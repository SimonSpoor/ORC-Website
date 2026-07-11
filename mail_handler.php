<?php
$message_sent = false;
$message_error = '';

function smtp_send($to, $subject, $body, $fromEmail, $fromName, $smtpHost, $smtpPort, $smtpUsername, $smtpPassword) {
    $socket = fsockopen($smtpHost, $smtpPort, $errno, $errstr, 15);
    if (!$socket) {
        throw new Exception("Unable to connect to SMTP server: {$errstr} ({$errno})");
    }

    $readResponse = function () use ($socket) {
        return trim(fgets($socket, 515));
    };

    $sendCommand = function ($command) use ($socket, $readResponse) {
        fwrite($socket, $command . "\r\n");
        return $readResponse();
    };

    $response = $readResponse();
    if (substr($response, 0, 3) !== '220') {
        throw new Exception("SMTP connection failed: {$response}");
    }

    if (substr($sendCommand('EHLO ' . gethostname()), 0, 3) !== '250') {
        throw new Exception('EHLO failed.');
    }

    if ($smtpUsername !== '' && $smtpPassword !== '') {
        if (substr($sendCommand('AUTH LOGIN'), 0, 3) !== '334') {
            throw new Exception('SMTP authentication failed.');
        }

        if (substr($sendCommand(base64_encode($smtpUsername)), 0, 3) !== '334') {
            throw new Exception('SMTP username failed.');
        }

        if (substr($sendCommand(base64_encode($smtpPassword)), 0, 3) !== '235') {
            throw new Exception('SMTP password failed.');
        }
    }

    if (substr($sendCommand('MAIL FROM:<'.$fromEmail.'>'), 0, 3) !== '250') {
        throw new Exception('MAIL FROM failed.');
    }

    if (substr($sendCommand('RCPT TO:<'.$to.'>'), 0, 3) !== '250' && substr($sendCommand('RCPT TO:<'.$to.'>'), 0, 3) !== '251') {
        throw new Exception('RCPT TO failed.');
    }

    if (substr($sendCommand('DATA'), 0, 3) !== '354') {
        throw new Exception('DATA failed.');
    }

    fwrite($socket, "From: {$fromName} <{$fromEmail}>\r\nTo: {$to}\r\nSubject: {$subject}\r\n\r\n{$body}\r\n.\r\n");
    $response = $readResponse();
    if (substr($response, 0, 3) !== '250') {
        throw new Exception('Email data was not accepted.');
    }

    $sendCommand('QUIT');
    fclose($socket);
}

if (isset($_POST['email']) && isset($_POST['name']) && isset($_POST['subject']) && isset($_POST['message'])) {
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $userName = trim($_POST['name']);
        $userEmail = trim($_POST['email']);
        $userSubject = trim($_POST['subject']);
        $userMessage = trim($_POST['message']);

        $to = getenv('CONTACT_TO_EMAIL') ?: 'your-email@example.com';
        $smtpHost = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $smtpPort = (int) (getenv('SMTP_PORT') ?: 465);
        $smtpUsername = getenv('SMTP_USERNAME') ?: '';
        $smtpPassword = getenv('SMTP_PASSWORD') ?: '';
        $fromEmail = getenv('SMTP_FROM_EMAIL') ?: $smtpUsername ?: 'no-reply@example.com';
        $fromName = getenv('SMTP_FROM_NAME') ?: 'Website Contact Form';

        $subject = 'New contact form submission: ' . $userSubject;
        $body = "Name: {$userName}\r\nEmail: {$userEmail}\r\nSubject: {$userSubject}\r\n\r\n{$userMessage}";

        try {
            smtp_send($to, $subject, $body, $fromEmail, $fromName, $smtpHost, $smtpPort, $smtpUsername, $smtpPassword);
            $message_sent = true;
        } catch (Exception $e) {
            $message_error = $e->getMessage();
        }
    } else {
        $message_error = 'Please enter a valid email address.';
    }
}
?>

<!DOCTYPE html>
<head>
    <title>Form submission</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="contact-form.css" />
</head>
<body>
    <h1 style="font-family:'Monomaniac One', sans-serif;margin-left:15%">Contact Us</h1>
    <div id="contact-form">
        <?php if ($message_sent): ?>
            <p style="color: green;">Your message was sent successfully.</p>
        <?php elseif ($message_error !== ''): ?>
            <p style="color: red;"><?php echo htmlspecialchars($message_error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" required>

            <label for="email">E-Mail</label>
            <input type="email" name="email" id="email" required>

            <label for="subject">Subject</label>
            <input type="text" name="subject" id="subject" required>

            <label for="message">Message</label>
            <textarea name="message" id="message" required></textarea>

            <br>

            <button>Send</button>
        </form>
    </div>
</body>
</html>
