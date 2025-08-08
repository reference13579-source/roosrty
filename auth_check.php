<?php
session_start();
header('Content-Type: application/json');

$db = new mysqli('localhost', 'u2977412_daniill8484', 'Dan13579', 'u2977412_roosty');
if ($db->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'DB connection failed']));
}

$user_id = $_SESSION['user_id'] ?? '';
if (empty($user_id)) {
    die(json_encode(['status' => 'error', 'message' => 'Not authenticated']));
}

// Та же функция парсинга, что и в save_page_data.php
function parseUserId($user_id) {
    if (strpos($user_id, 'ya-') === 0) {
        return [
            'provider' => 'yandex',
            'provider_id' => str_replace('ya-', '', $user_id)
        ];
    }
    return [
        'provider' => 'google',
        'provider_id' => $user_id
    ];
}

$parsed = parseUserId($user_id);

// Проверяем существование пользователя
$stmt = $db->prepare("SELECT id, email FROM users WHERE provider_id = ? AND provider = ?");
$stmt->bind_param("ss", $parsed['provider_id'], $parsed['provider']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        'status' => 'success',
        'user' => $user,
        'provider_info' => $parsed
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not found',
        'debug' => [
            'session_user_id' => $user_id,
            'parsed' => $parsed,
            'query' => "SELECT id FROM users WHERE provider_id = '{$parsed['provider_id']}' AND provider = '{$parsed['provider']}'"
        ]
    ]);
}

$db->close();
?>
