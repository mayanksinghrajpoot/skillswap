<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->name) &&
        !empty($data->email) &&
        !empty($data->message)
    ) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (name, email, message)
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([
                $data->name,
                $data->email,
                $data->message
            ]);

            // Here you could add email notification functionality
            // mail('admin@skillswap.com', 'New Contact Form Submission', $data->message);

            http_response_code(201);
            echo json_encode([
                'message' => 'Message sent successfully'
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to send message',
                'message' => $e->getMessage()
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>