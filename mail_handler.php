// Source - https://stackoverflow.com/a/18382062
// Posted by Funk Forty Niner, modified by community. See post 'Timeline' for change history
// Retrieved 2026-02-03, License - CC BY-SA 3.0

<?php 
if(isset($_POST['submit'])){
    $to = "sammoore359@gmail.com"; // this is your Email address
    $from = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $user_message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

    // Basic validation to ensure the email is actually a valid format
    if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address. Please go back and try again.";
        exit;
    }

    $subject = "Form submission";
    $subject2 = "Copy of your form submission";
    $message = $first_name . " " . $last_name . " wrote the following:" . "\n\n" . $_POST['message'];
    $message2 = "Here is a copy of your message " . $first_name . "\n\n" . $_POST['message'];

    $headers = "From:" . $from;
    $headers2 = "From:" . $to;
    mail($to,$subject,$message,$headers);
    mail($from,$subject2,$message2,$headers2); // sends a copy of the message to the sender
    echo "Mail Sent. Thank you " . $first_name . ", we will contact you shortly.";
    // You can also use header('Location: thank_you.php'); to redirect to another page.
    // You cannot use header and echo together. It's one or the other.
    }
?>
