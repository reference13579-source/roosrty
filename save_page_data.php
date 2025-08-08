<?php
session_start();
header('Content-Type: application/json');

// Отладка
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Подготовка папок
if (!file_exists('uploads')) {
    mkdir('uploads', 0755, true);
}
if (!file_exists('images')) {
    mkdir('images', 0755, true);
    if (!file_exists('images/default-header.jpg')) {
        file_put_contents('images/default-header.jpg', file_get_contents('https://via.placeholder.com/1200x300'));
    }
    if (!file_exists('images/default-avatar.jpg')) {
        file_put_contents('images/default-avatar.jpg', file_get_contents('https://via.placeholder.com/200'));
    }
}

$db = new mysqli('localhost', 'u2977412_daniill8484', 'Dan13579', 'u2977412_roosty');
if ($db->connect_error) {
    error_log('DB Connection Error: ' . $db->connect_error);
    echo json_encode(['status' => 'error', 'message' => 'Ошибка подключения к БД']);
    exit;
}

// Функция обработки ID
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

// Функция загрузки изображения
function handleFileUpload($file, $type) {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) return null;

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $targetPath = 'uploads/' . $filename;

    return move_uploaded_file($file['tmp_name'], $targetPath) ? $targetPath : null;
}

try {
    $user_id = $_SESSION['user_id'] ?? '';
    if (empty($user_id)) throw new Exception('User not authenticated');

    $userData = parseUserId($user_id);
    $provider = $userData['provider'];
    $provider_id = $userData['provider_id'];

    // Получаем ID пользователя
    $stmt = $db->prepare("SELECT id FROM users WHERE provider_id = ? AND provider = ?");
    $stmt->bind_param("ss", $provider_id, $provider);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) throw new Exception('Пользователь не найден. Пожалуйста, войдите снова.');
    $userId = $result->fetch_assoc()['id'];
    $stmt->close();

    $headerImage = isset($_FILES['header_image']) ? handleFileUpload($_FILES['header_image'], 'header') : null;
    $avatarImage = isset($_FILES['avatar_image']) ? handleFileUpload($_FILES['avatar_image'], 'avatar') : null;

    $db->begin_transaction();

    // Страница пользователя
    $stmt = $db->prepare("SELECT id FROM user_pages WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $pageId = $result->num_rows > 0 ? $result->fetch_assoc()['id'] : null;
    $stmt->close();

    if (isset($_POST['page_id']) && intval($_POST['page_id']) !== (int)$pageId) {
        throw new Exception('Доступ к странице запрещен');
    }

    if ($pageId) {
        $stmt = $db->prepare("SELECT header_image, avatar_image FROM user_pages WHERE id = ?");
        $stmt->bind_param("i", $pageId);
        $stmt->execute();
        $currentImages = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $query = "UPDATE user_pages SET title = ?, content = ?";
        $params = [$_POST['title'], $_POST['content']];
        $types = "ss";

        if ($headerImage) {
            $query .= ", header_image = ?";
            $params[] = $headerImage;
            $types .= "s";
        }
        if ($avatarImage) {
            $query .= ", avatar_image = ?";
            $params[] = $avatarImage;
            $types .= "s";
        }

        $query .= " WHERE id = ?";
        $params[] = $pageId;
        $types .= "i";

        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->close();
    } else {
        // Создаём страницу
        $stmt = $db->prepare("INSERT INTO user_pages (user_id, title, content, header_image, avatar_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userId, $_POST['title'], $_POST['content'], $headerImage, $avatarImage);
        $stmt->execute();
        $pageId = $stmt->insert_id;
        $stmt->close();
    }

    // Удаляем старые тарифы
    $stmt = $db->prepare("DELETE FROM subscription_tiers WHERE page_id = ?");
    $stmt->bind_param("i", $pageId);
    $stmt->execute();
    $stmt->close();

    // Сохраняем тарифы
    if (!empty($_POST['tariffs'])) {
        $tariffs = json_decode($_POST['tariffs'], true);
        $stmt = $db->prepare("INSERT INTO subscription_tiers (page_id, name, description, price, benefits, position) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($tariffs as $index => $tariff) {
            $benefits = implode("\n", $tariff['benefits']);
            $stmt->bind_param("issdsi", $pageId, $tariff['name'], $tariff['description'], $tariff['price'], $benefits, $index);
            $stmt->execute();
        }
        $stmt->close();
    }

    $db->commit();
    echo json_encode(['status' => 'success', 'page_id' => $pageId]);
} catch (Exception $e) {
    $db->rollback();
    error_log('Exception: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'debug' => [
            'session_user_id' => $user_id ?? null,
            'parsed_provider' => $provider ?? null,
            'parsed_provider_id' => $provider_id ?? null
        ]
    ]);
} finally {
    $db->close();
}
?>
