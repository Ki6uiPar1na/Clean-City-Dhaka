<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT to_email, subject, sent_at FROM sent_emails 
                            ORDER BY sent_at DESC LIMIT 5");
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'emails' => $emails
        ]);
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch recent emails'
        ]);
    }
}
?>