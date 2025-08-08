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

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_provider` (`provider`,`provider_id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
