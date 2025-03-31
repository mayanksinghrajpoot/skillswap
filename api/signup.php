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
        !empty($data->fullName) &&
        !empty($data->email) &&
        !empty($data->password) &&
        !empty($data->userType)
    ) {
        try {
            // Check if email already exists
            $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkEmail->execute([$data->email]);
            
            if ($checkEmail->rowCount() > 0) {
                http_response_code(409);
                echo json_encode(['error' => 'Email already exists']);
                exit();
            }

            // Hash password
            $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);

            // Begin transaction
            $pdo->beginTransaction();

            // Insert user
            $stmt = $pdo->prepare("
                INSERT INTO users (full_name, email, password, user_type)
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data->fullName,
                $data->email,
                $hashedPassword,
                $data->userType
            ]);

            $userId = $pdo->lastInsertId();

            // Insert skills if provided
            if (!empty($data->skills) && is_array($data->skills)) {
                $skillStmt = $pdo->prepare("
                    INSERT INTO user_skills (user_id, skill)
                    VALUES (?, ?)
                ");

                foreach ($data->skills as $skill) {
                    $skillStmt->execute([$userId, $skill]);
                }
            }

            // Commit transaction
            $pdo->commit();

            // Create session token
            $sessionToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

            $sessionStmt = $pdo->prepare("
                INSERT INTO sessions (user_id, session_token, expires_at)
                VALUES (?, ?, ?)
            ");
            $sessionStmt->execute([$userId, $sessionToken, $expiresAt]);

            http_response_code(201);
            echo json_encode([
                'message' => 'User registered successfully',
                'userId' => $userId,
                'sessionToken' => $sessionToken
            ]);

        } catch (PDOException $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode([
                'error' => 'Registration failed',
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