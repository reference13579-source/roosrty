-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Май 20 2025 г., 12:26
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
-- Индексы таблицы `user_pages`
--
ALTER TABLE `user_pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `user_pages`
--
ALTER TABLE `user_pages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `user_pages`
--
ALTER TABLE `user_pages`
  ADD CONSTRAINT `user_pages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
