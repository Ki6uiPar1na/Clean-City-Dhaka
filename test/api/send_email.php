<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!filter_var($data['to'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    if (empty($data['subject']) || empty($data['message'])) {
        echo json_encode(['success' => false, 'message' => 'Subject and message are required']);
        exit;
    }

    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Clean City <noreply@cleancity.com>' . "\r\n";
    
    // Try to send email
    $mailSent = mail($data['to'], $data['subject'], $data['message'], $headers);
    
    if ($mailSent) {
        // Log the email in database
        try {
            $stmt = $pdo->prepare("INSERT INTO sent_emails (to_email, subject, message) VALUES (?, ?, ?)");
            $stmt->execute([$data['to'], $data['subject'], $data['message']]);
            
            echo json_encode(['success' => true]);
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to log email']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send email']);
    }
}
?>