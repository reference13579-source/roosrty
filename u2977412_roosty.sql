-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Май 20 2025 г., 12:27
-- Версия сервера: 8.0.25-15
-- Версия PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `u2977412_roosty`
--

-- --------------------------------------------------------

--
-- Структура таблицы `content_blocks`
--

CREATE TABLE `content_blocks` (
  `id` int NOT NULL,
  `page_id` int NOT NULL,
  `type` enum('text','image','video','subscription') COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int NOT NULL DEFAULT '0' COMMENT 'Позиция в списке',
  `is_public` tinyint(1) DEFAULT '1' COMMENT 'Виден ли всем',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int NOT NULL,
  `creator_id` int NOT NULL COMMENT 'ID создателя контента',
  `subscriber_id` int NOT NULL COMMENT 'ID подписчика',
  `tier_id` int DEFAULT NULL COMMENT 'Уровень подписки',
  `start_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date` timestamp NULL DEFAULT NULL,
  `status` enum('active','cancelled','expired') COLLATE utf8mb4_unicode_ci DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `subscription_tiers`
--

CREATE TABLE `subscription_tiers` (
  `id` int NOT NULL,
  `page_id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT 'RUB',
  `benefits` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON с описанием преимуществ',
  `position` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `subscription_tiers`
--

INSERT INTO `subscription_tiers` (`id`, `page_id`, `name`, `description`, `price`, `currency`, `benefits`, `position`, `is_active`) VALUES
(40, 14, '', '', '0.00', 'RUB', '', 0, 1),
(41, 18, '', '', '0.00', 'RUB', '', 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `provider` enum('google','yandex') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Способ авторизации',
  `provider_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID пользователя в системе провайдера',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Имя для отображения',
  `avatar_url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ссылка на аватар',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `provider`, `provider_id`, `email`, `display_name`, `avatar_url`, `created_at`) VALUES
(4, 'google', '106374758910488355870', 'daniill8484@gmail.com', 'Tenebris 003', 'https://lh3.googleusercontent.com/a/ACg8ocIMvThH2ewxmSGWHk3wB2hEELZe91gz2MjjVPGjqfpvpmSAgg=s96-c', '2025-05-10 09:12:56'),
(5, 'yandex', '1153387777', 'daniill8484@yandex.ru', 'Даниил Зайцев', '', '2025-05-10 09:16:22'),
(6, 'google', '116956838856548072773', 'lizabuvailik.3@gmail.com', 'Елизавета Бувайлик', 'https://lh3.googleusercontent.com/a/ACg8ocKAvte0fNJaKpIASKi4MeRqcEUDydWjdz7SwHrnOIbDQt8LcA=s96-c', '2025-05-10 09:19:43'),
(7, 'yandex', '2182034032', 'daniil8484steve@yandex.ru', 'Даниил Зайцев', '', '2025-05-10 09:24:52'),
(8, 'yandex', '1731892737', 'satoru21-S@yandex.com', 'Елизавета', '', '2025-05-10 09:28:24');

-- --------------------------------------------------------

--
-- Структура таблицы `user_pages`
--

CREATE TABLE `user_pages` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Моя страница',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT 'Основной контент страницы',
  `header_image` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT 'images/default-header.jpg',
  `avatar_image` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT 'images/default-avatar.jpg',
  `custom_css` text COLLATE utf8mb4_unicode_ci COMMENT 'Пользовательские стили',
  `custom_js` text COLLATE utf8mb4_unicode_ci COMMENT 'Пользовательские скрипты',
  `is_published` tinyint(1) DEFAULT '0' COMMENT 'Опубликована ли страница',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `user_pages`
--

INSERT INTO `user_pages` (`id`, `user_id`, `title`, `content`, `header_image`, `avatar_image`, `custom_css`, `custom_js`, `is_published`, `created_at`) VALUES
(12, 4, 'Страница Tenebris 003', 'Привет! Это моя страница на Roosty', 'uploads/681f1e0b7f682.jpg', 'uploads/681f1e0b7f7e5.jpg', NULL, NULL, 0, '2025-05-10 09:12:56'),
(13, 5, 'Страница Даниил Зайцев', 'Привет! Это моя страница на Roosty', 'uploads/681f33d1390c7.png', 'images/default-avatar.jpg', NULL, NULL, 0, '2025-05-10 09:16:22'),
(14, 6, 'Страница Елизавета Бувайлик', 'Привет! Это моя страница на Roosty', 'images/default-header.jpg', 'images/default-avatar.jpg', NULL, NULL, 0, '2025-05-10 09:19:43'),
(17, 7, 'Страница Даниил Зайцев', 'Привет! Это моя страница на Roosty', 'images/default-header.jpg', 'images/default-avatar.jpg', NULL, NULL, 0, '2025-05-10 09:24:52'),
(18, 8, 'Страница Елизавета', 'Привет! Это моя страница на Roosty', 'uploads/681f1c867a55b.jpeg', 'uploads/681f1c867b4bb.jpeg', NULL, NULL, 0, '2025-05-10 09:28:24');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `content_blocks`
--
ALTER TABLE `content_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`);

--
-- Индексы таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `subscriber_id` (`subscriber_id`);

--
-- Индексы таблицы `subscription_tiers`
--
ALTER TABLE `subscription_tiers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_provider` (`provider`,`provider_id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Индексы таблицы `user_pages`
--
ALTER TABLE `user_pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `content_blocks`
--
ALTER TABLE `content_blocks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `subscription_tiers`
--
ALTER TABLE `subscription_tiers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `user_pages`
--
ALTER TABLE `user_pages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `content_blocks`
--
ALTER TABLE `content_blocks`
  ADD CONSTRAINT `content_blocks_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `user_pages` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`subscriber_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `subscription_tiers`
--
ALTER TABLE `subscription_tiers`
  ADD CONSTRAINT `subscription_tiers_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `user_pages` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_pages`
--
ALTER TABLE `user_pages`
  ADD CONSTRAINT `user_pages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
