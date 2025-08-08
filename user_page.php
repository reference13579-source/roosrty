<?php
header('Content-Type: text/html; charset=utf-8');

// Подключение к БД
$db = new mysqli('localhost', 'u2977412_daniill8484', 'Dan13579', 'u2977412_roosty');
if ($db->connect_error) {
    die("Ошибка подключения к базе данных: " . $db->connect_error);
}

$user_id = $_GET['user_id'] ?? '';
if (empty($user_id)) {
    die("Не указан идентификатор пользователя");
}

$provider = strpos($user_id, 'ya-') === 0 ? 'yandex' : 'google';
$provider_id = $provider === 'yandex' ? str_replace('ya-', '', $user_id) : $user_id;

// Получаем данные страницы
$pageData = [
    'title' => 'Личная страница',
    'content' => 'Добро пожаловать на мою страницу!',
    'header_image' => 'images/default-header.jpg',
    'avatar_image' => 'images/default-avatar.jpg'
];

$stmt = $db->prepare("
    SELECT up.title, up.content, up.header_image, up.avatar_image 
    FROM user_pages up
    JOIN users u ON up.user_id = u.id
    WHERE u.provider_id = ? AND u.provider = ?
");
$stmt->bind_param("ss", $provider_id, $provider);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $pageData = $result->fetch_assoc();
}
$stmt->close();

// Получаем тарифные планы
$tariffs = [];
$stmt = $db->prepare("
    SELECT name, description, price, benefits 
    FROM subscription_tiers 
    WHERE page_id = (
        SELECT up.id 
        FROM user_pages up
        JOIN users u ON up.user_id = u.id
        WHERE u.provider_id = ? AND u.provider = ?
    )
    ORDER BY position ASC
");
$stmt->bind_param("ss", $provider_id, $provider);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['benefits'] = array_filter(explode("\n", $row['benefits']));
    $tariffs[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageData['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content-wrapper {
            flex: 1 0 auto;
        }
        .header-menu{
            border-bottom: 1px solid;
        }
        .footer {
            flex-shrink: 0;
            width: 100%;
            border-top: 1px solid;
        }
        .header-container {
            position: relative;
            height: 300px;
            overflow: hidden;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #dee2e6;
        }
        .header-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .logo{
            font-size: 32px;
            font-weight: bold;
            color: black;
            text-decoration: none;
        }
        .header-placeholder {
            color: #6c757d;
            font-size: 1.5rem;
            text-align: center;
            padding: 20px;
        }
        .circle-container {
            position: relative;
            margin-top: -75px;
            text-align: center;
            z-index: 1;
        }
        .circle-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
            background-color: #f8f9fa;
            display: block;
        }
        .avatar-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            margin: 0 auto;
        }
        .tariff-card {
            transition: all 0.3s;
            height: 100%;
            margin-bottom: 20px;
        }
        .tariff-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .tariff-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .benefit-item {
            position: relative;
            padding-left: 1.5rem;
        }
        .benefit-item:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #198754;
        }
        .tariffs-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .tariff-item {
            flex: 0 0 calc(20% - 20px);
            max-width: calc(20% - 20px);
        }
        @media (max-width: 1199.98px) {
            .tariff-item {
                flex: 0 0 calc(25% - 20px);
                max-width: calc(25% - 20px);
            }
        }
        @media (max-width: 991.98px) {
            .tariff-item {
                flex: 0 0 calc(33.333% - 20px);
                max-width: calc(33.333% - 20px);
            }
        }
        @media (max-width: 767.98px) {
            .tariff-item {
                flex: 0 0 calc(50% - 20px);
                max-width: calc(50% - 20px);
            }
        }
        @media (max-width: 575.98px) {
            .tariff-item {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        
        /* Обновленные стили для адаптивного меню */
        .header-buttons .regular-buttons {
            display: flex;
        }
        .dropdown-menu-mobile {
            display: none;
        }
        @media (max-width: 500px) {
            .header-buttons .regular-buttons {
                display: none !important;
            }
            .dropdown-menu-mobile {
                display: block !important;
            }
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <header class="header-menu">
            <div class="container-fluid d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center">
                    <a href="/" class="logo">Roosty</a>
                </div>
                <div class="header-buttons d-flex align-items-center">
                    <!-- Обычные кнопки (видны на больших экранах) -->
                    <div class="regular-buttons">
                        <a href="https://roosty.ru/void.html" class="btn btn-outline-primary me-2">
                            На главную
                        </a>
                        <a href="personal_cabinet.php?user_id=<?= urlencode($user_id) ?>" class="btn btn-outline-primary">
                            Личный кабинет
                        </a>
                    </div>
                    
                    <!-- Выпадающее меню для мобильных устройств -->
                    <div class="dropdown dropdown-menu-mobile ms-2">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="https://roosty.ru/void.html">На главную</a></li>
                            <li><a class="dropdown-item" href="personal_cabinet.php?user_id=<?= urlencode($user_id) ?>">Личный кабинет</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>
        
        <div class="header-container">
            <?php if ($pageData['header_image'] === 'images/default-header.jpg'): ?>
                <div class="header-placeholder">Изображение шапки страницы</div>
            <?php else: ?>
                <img id="header-image" src="<?= htmlspecialchars($pageData['header_image']) ?>" alt="">
            <?php endif; ?>
        </div>
        
        <div class="circle-container">
            <?php if ($pageData['avatar_image'] === 'images/default-avatar.jpg'): ?>
                <div class="avatar-placeholder">Аватар</div>
            <?php else: ?>
                <img id="avatar-image" src="<?= htmlspecialchars($pageData['avatar_image']) ?>" alt="">
            <?php endif; ?>
        </div>
        
        <div class="container my-5">
            <div class="text-center mb-5">
                <h1><?= htmlspecialchars($pageData['title']) ?></h1>
                <p class="lead"><?= htmlspecialchars($pageData['content']) ?></p>
            </div>
            
            <?php if (!empty($tariffs)): ?>
            <div class="mb-5">
                <h2 class="text-center mb-4">Тарифные планы</h2>
                <div class="tariffs-container">
                    <?php foreach ($tariffs as $tariff): ?>
                    <div class="tariff-item">
                        <div class="card tariff-card">
                            <div class="card-body">
                                <h3 class="card-title text-center"><?= htmlspecialchars($tariff['name']) ?></h3>
                                <p class="card-text text-center text-muted mb-4"><?= htmlspecialchars($tariff['description']) ?></p>
                                <div class="text-center mb-4">
                                    <span class="tariff-price"><?= htmlspecialchars($tariff['price']) ?> ₽</span>
                                    <span class="text-muted">/ месяц</span>
                                </div>
                                <ul class="list-unstyled">
                                    <?php foreach ($tariff['benefits'] as $benefit): ?>
                                        <li class="benefit-item mb-2"><?= htmlspecialchars($benefit) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <button class="btn btn-primary w-100">Подписаться</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer class="footer py-4">
        <div class="container text-center">
            <div class="row">
                <div class="col">
                    <a href="/" class="btn btn-outline-secondary">О нас</a>
                </div>
                <div class="col">
                    <a href="#" class="btn btn-outline-secondary">Правовые документы</a>
                </div>
                <div class="col">
                    <a href="#" class="btn btn-outline-secondary">Поддержка</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $db->close(); ?>