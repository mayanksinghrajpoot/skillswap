<?php
function validateSession($pdo, $sessionToken) {
    if (empty($sessionToken)) {
        return false;
    }

    try {
        // Clean up expired sessions
        $cleanupStmt = $pdo->prepare("
            DELETE FROM sessions 
            WHERE expires_at < NOW()
        ");
        $cleanupStmt->execute();

        // Check for valid session
        $stmt = $pdo->prepare("
            SELECT s.*, u.full_name, u.email, u.user_type
            FROM sessions s
            JOIN users u ON s.user_id = u.id
            WHERE s.session_token = ? AND s.expires_at > NOW()
        ");
        $stmt->execute([$sessionToken]);

        if ($stmt->rowCount() > 0) {
            $session = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get user skills
            $skillStmt = $pdo->prepare("
                SELECT skill 
                FROM user_skills 
                WHERE user_id = ?
            ");
            $skillStmt->execute([$session['user_id']]);
            $skills = $skillStmt->fetchAll(PDO::FETCH_COLUMN);

            return array_merge($session, ['skills' => $skills]);
        }

        return false;
    } catch (PDOException $e) {
        return false;
    }
}

function requireAuth($pdo) {
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    
    if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'No authorization token provided']);
        exit();
    }

    $sessionToken = $matches[1];
    $session = validateSession($pdo, $sessionToken);

    if (!$session) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid or expired session']);
        exit();
    }

    return $session;
}

function refreshSession($pdo, $sessionToken) {
    try {
        // Extend session expiration
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $stmt = $pdo->prepare("
            UPDATE sessions 
            SET expires_at = ? 
            WHERE session_token = ? AND expires_at > NOW()
        ");
        
        $stmt->execute([$expiresAt, $sessionToken]);
        
        if ($stmt->rowCount() > 0) {
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        return false;
    }
}
?>