<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

// Подключение к БД
$db = new mysqli('localhost', 'u2977412_daniill8484', 'Dan13579', 'u2977412_roosty');
if ($db->connect_error) die("Ошибка подключения: " . $db->connect_error);

$user_id = $_SESSION['user_id'] ?? '';
if (empty($user_id)) die("User not authenticated");

$provider = strpos($user_id, 'ya-') === 0 ? 'yandex' : 'google';
$provider_id = $provider === 'yandex' ? str_replace('ya-', '', $user_id) : $user_id;

// Получаем ID пользователя
$stmt = $db->prepare("SELECT id FROM users WHERE provider_id = ? AND provider = ?");
$stmt->bind_param("ss", $provider_id, $provider);
$stmt->execute();
$userResult = $stmt->get_result();
if ($userResult->num_rows === 0) die('User not found');
$currentUserId = $userResult->fetch_assoc()['id'];
$stmt->close();

// Получаем данные страницы пользователя
$pageData = [
    'title' => 'Моя страница',
    'content' => 'Привет! Это моя страница на Roosty',
    'header_image' => 'images/default-header.jpg',
    'avatar_image' => 'images/default-avatar.jpg'
];
$pageId = null;
$stmt = $db->prepare("SELECT id, title, content, header_image, avatar_image FROM user_pages WHERE user_id = ?");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $pageId = $row['id'];
    $pageData = [
        'title' => $row['title'],
        'content' => $row['content'],
        'header_image' => $row['header_image'],
        'avatar_image' => $row['avatar_image']
    ];
}
$stmt->close();

// Получаем тарифы, привязанные к этой странице
$tariffBlocks = [];
if ($pageId) {
    $stmt = $db->prepare("SELECT id, name, description, price, benefits FROM subscription_tiers WHERE page_id = ? ORDER BY position ASC");
    $stmt->bind_param("i", $pageId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tariffBlocks[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .image-preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
        }
        .upload-area {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        .upload-area:hover {
            border-color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- ... HTML-шаблон формы и данных страницы ... -->

    <div class="fixed-save-btn">
        <div class="container">
            <button id="save-all-btn" class="btn btn-primary btn-lg w-100 py-3">
                <i class="fas fa-save"></i> Сохранить все изменения
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let tariffCounter = <?= count($tariffBlocks) ?>;
        let isSaving = false;

        document.getElementById('header-upload-area').addEventListener('click', function() {
            document.getElementById('header-image-file').click();
        });
        document.getElementById('avatar-upload-area').addEventListener('click', function() {
            document.getElementById('avatar-image-file').click();
        });

        document.getElementById('header-image-file').addEventListener('change', function(e) {
            if (e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('header-preview');
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        document.getElementById('avatar-image-file').addEventListener('change', function(e) {
            if (e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('avatar-preview');
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        document.getElementById('save-all-btn').addEventListener('click', function() {
            if (isSaving) return;
            isSaving = true;

            const saveBtn = document.getElementById('save-all-btn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Сохранение...';

            const formData = new FormData();
            formData.append('title', document.getElementById('title-text').value);
            formData.append('content', document.getElementById('content-text').value);

            const headerFile = document.getElementById('header-image-file').files[0];
            const avatarFile = document.getElementById('avatar-image-file').files[0];

            if (headerFile) formData.append('header_image', headerFile);
            if (avatarFile) formData.append('avatar_image', avatarFile);

            const tariffs = [];
            document.querySelectorAll('.tariff-block').forEach((block, index) => {
                const benefitsText = block.querySelector('.tariff-benefits').value;
                tariffs.push({
                    id: block.dataset.id,
                    name: block.querySelector('.tariff-name').value,
                    description: block.querySelector('.tariff-description').value,
                    price: parseFloat(block.querySelector('.tariff-price').value) || 0,
                    benefits: benefitsText.split('\n').filter(b => b.trim()),
                    position: index
                });
            });
            formData.append('tariffs', JSON.stringify(tariffs));

            fetch('save_page_data.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error(text || 'Неизвестная ошибка');
                        }
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    showAlert('success', 'Все изменения успешно сохранены!');
                    document.querySelectorAll('.tariff-block').forEach(block => {
                        if (block.dataset.id.startsWith('new-') && data.page_id) {
                            block.dataset.id = data.page_id + '-' + block.dataset.id.split('-')[1];
                        }
                    });
                } else {
                    throw new Error(data.message || 'Неизвестная ошибка');
                }
            })
            .catch(error => {
                console.error('Error details:', error);
                showAlert('danger', 'Ошибка сохранения: ' + error.message);

                fetch('auth_check.php')
                    .then(res => res.json())
                    .then(data => console.log('Auth check:', data))
                    .catch(e => console.error('Auth check failed:', e));
            })
            .finally(() => {
                isSaving = false;
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save"></i> Сохранить все изменения';
            });
        });

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
            alertDiv.style.zIndex = '1100';
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <strong>${type === 'success' ? 'Успешно!' : 'Ошибка!'}</strong>
                    <span class="ms-2">${message}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.body.appendChild(alertDiv);
        }
    });
    </script>
</body>
</html>
