<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->email) && !empty($data->password)) {
        try {
            // Get user by email
            $stmt = $pdo->prepare("
                SELECT id, full_name, email, password, user_type 
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$data->email]);

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verify password
                if (password_verify($data->password, $user['password'])) {
                    // Get user skills
                    $skillStmt = $pdo->prepare("
                        SELECT skill 
                        FROM user_skills 
                        WHERE user_id = ?
                    ");
                    $skillStmt->execute([$user['id']]);
                    $skills = $skillStmt->fetchAll(PDO::FETCH_COLUMN);

                    // Create new session token
                    $sessionToken = bin2hex(random_bytes(32));
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

                    // Remove old sessions
                    $deleteOldSessions = $pdo->prepare("
                        DELETE FROM sessions 
                        WHERE user_id = ? OR expires_at < NOW()
                    ");
                    $deleteOldSessions->execute([$user['id']]);

                    // Create new session
                    $sessionStmt = $pdo->prepare("
                        INSERT INTO sessions (user_id, session_token, expires_at)
                        VALUES (?, ?, ?)
                    ");
                    $sessionStmt->execute([$user['id'], $sessionToken, $expiresAt]);

                    // Remove password from response
                    unset($user['password']);

                    http_response_code(200);
                    echo json_encode([
                        'message' => 'Login successful',
                        'user' => array_merge($user, ['skills' => $skills]),
                        'sessionToken' => $sessionToken
                    ]);
                } else {
                    http_response_code(401);
                    echo json_encode(['error' => 'Invalid credentials']);
                }
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Login failed',
                'message' => $e->getMessage()
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing email or password']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>