<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->email)) {
        try {
            // Check if email already subscribed
            $checkEmail = $pdo->prepare("
                SELECT id 
                FROM newsletter_subscriptions 
                WHERE email = ?
            ");
            $checkEmail->execute([$data->email]);
            
            if ($checkEmail->rowCount() > 0) {
                http_response_code(409);
                echo json_encode(['message' => 'Email already subscribed']);
                exit();
            }

            // Add new subscription
            $stmt = $pdo->prepare("
                INSERT INTO newsletter_subscriptions (email)
                VALUES (?)
            ");
            
            $stmt->execute([$data->email]);

            // Here you could integrate with an email service provider
            // For example, sending a welcome email or adding to a mailing list

            http_response_code(201);
            echo json_encode([
                'message' => 'Successfully subscribed to newsletter'
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Subscription failed',
                'message' => $e->getMessage()
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Email is required']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>