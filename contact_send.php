<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

define('GMAIL_USER', 'gsstha326@gmail.com');
define('GMAIL_PASS', 'ociy qchk xkwu eprd');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $subject = trim($_POST['subject']) ?: 'No Subject';
    $message = trim($_POST['message']);

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['contact_error'] = 'Please fill in all required fields.';
        header('Location: contact.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['contact_error'] = 'Please enter a valid email address.';
        header('Location: contact.php');
        exit;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = GMAIL_USER;
        $mail->Password   = GMAIL_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Send TO your inbox
        $mail->setFrom(GMAIL_USER, 'The Coffee Table Website');
        $mail->addAddress(GMAIL_USER, 'The Coffee Table');
        $mail->addReplyTo($email, $name);

        $mail->Subject = 'Contact Form: ' . $subject;
        $mail->isHTML(true);
        $mail->Body = "
            <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;padding:30px;border:2px solid #D2B48C;border-radius:12px;'>
                <h2 style='color:#8B4513;text-align:center;'>☕ New Contact Form Message</h2>
                <table style='width:100%;border-collapse:collapse;'>
                    <tr style='background:#FFF8F0;'>
                        <td style='padding:10px;font-weight:bold;color:#5D4037;width:30%;'>Name</td>
                        <td style='padding:10px;'>" . htmlspecialchars($name) . "</td>
                    </tr>
                    <tr>
                        <td style='padding:10px;font-weight:bold;color:#5D4037;'>Email</td>
                        <td style='padding:10px;'><a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></td>
                    </tr>
                    <tr style='background:#FFF8F0;'>
                        <td style='padding:10px;font-weight:bold;color:#5D4037;'>Subject</td>
                        <td style='padding:10px;'>" . htmlspecialchars($subject) . "</td>
                    </tr>
                    <tr>
                        <td style='padding:10px;font-weight:bold;color:#5D4037;vertical-align:top;'>Message</td>
                        <td style='padding:10px;'>" . nl2br(htmlspecialchars($message)) . "</td>
                    </tr>
                </table>
                <hr style='border-color:#D2B48C;margin-top:20px;'>
                <p style='text-align:center;color:#8B4513;font-size:0.85rem;'>The Coffee Table - Wyndham Vale Community Website</p>
            </div>
        ";
        $mail->AltBody = "From: {$name} ({$email})\nSubject: {$subject}\n\nMessage:\n{$message}";
        $mail->send();

        $_SESSION['contact_success'] = 'Thank you ' . $name . '! Your message has been sent. We will get back to you soon.';
        header('Location: contact.php');
        exit;

    } catch (Exception $e) {
        $_SESSION['contact_error'] = 'Sorry, your message could not be sent. Please try again later.';
        header('Location: contact.php');
        exit;
    }
}

header('Location: contact.php');
exit;
?>
