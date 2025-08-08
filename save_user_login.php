<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new mysqli('localhost', 'u2977412_daniill8484', 'Dan13579', 'u2977412_roosty');
if ($db->connect_error) {
    error_log('DB Connection Error: ' . $db->connect_error);
    echo json_encode(['status' => 'error', 'message' => 'Ошибка подключения к БД']);
    exit;
}

function parseUserId($user_id) {
    if (strpos($user_id, 'ya-') === 0) {
        return [
            'provider' => 'yandex',
            'provider_id' => str_replace('ya-', '', $user_id),
            'is_yandex' => true
        ];
    }
    return [
        'provider' => 'google',
        'provider_id' => $user_id,
        'is_yandex' => false
    ];
}

$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['user_id']) || empty($input['login'])) {
    echo json_encode(['status' => 'error', 'message' => 'Неверные данные']);
    exit;
}

$userData = parseUserId($input['user_id']);
$provider = $userData['provider'];
$provider_id = $userData['provider_id'];
$email = $input['login'];
$display_name = $input['display_name'] ?? '';
$avatar_url = $input['avatar_url'] ?? '';

try {
    $stmt = $db->prepare("SELECT id FROM users WHERE provider_id = ? AND provider = ?");
    $stmt->bind_param("ss", $provider_id, $provider);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = false;
    $user_db_id = null;
    if ($row = $result->fetch_assoc()) {
        $exists = true;
        $user_db_id = $row['id'];
    }
    $stmt->close();

    if ($exists) {
        $stmt = $db->prepare("UPDATE users SET email = ?, display_name = ?, avatar_url = ?, updated_at = NOW() WHERE provider_id = ? AND provider = ?");
        $stmt->bind_param("sssss", $email, $display_name, $avatar_url, $provider_id, $provider);
    } else {
        $stmt = $db->prepare("INSERT INTO users (provider, provider_id, email, display_name, avatar_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $provider, $provider_id, $email, $display_name, $avatar_url);
    }

    if ($stmt->execute()) {
        if (!$exists) {
            $user_db_id = $stmt->insert_id;
        }
        $stmt->close();

        $_SESSION['user_id'] = $userData['is_yandex'] ? 'ya-' . $provider_id : $provider_id;
        $_SESSION['user_db_id'] = $user_db_id;

        $stmt = $db->prepare("INSERT IGNORE INTO user_pages (user_id, title, content) VALUES (?, ?, ?)");
        $title = "Страница " . ($display_name ?: $email);
        $content = "Привет! Это моя страница на Roosty";
        $stmt->bind_param("iss", $user_db_id, $title, $content);
        $stmt->execute();
        $stmt->close();

        echo json_encode([
            'status' => 'success',
            'user_id' => $_SESSION['user_id']
        ]);
    } else {
        throw new Exception('Ошибка сохранения пользователя: ' . $stmt->error);
    }
} catch (Exception $e) {
    error_log('Error in save_user_login: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    $db->close();
}
?>
