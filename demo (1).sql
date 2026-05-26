-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Май 26 2026 г., 12:39
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `demo`
--

-- --------------------------------------------------------

--
-- Структура таблицы `manufacturers`
--

CREATE TABLE `manufacturers` (
  `id_manufacturer` int(11) NOT NULL,
  `name_manufacturer` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `manufacturers`
--

INSERT INTO `manufacturers` (`id_manufacturer`, `name_manufacturer`) VALUES
(1, 'Knauf'),
(2, 'Ceresit'),
(3, 'Волма');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id_order` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `date_order` date DEFAULT NULL,
  `date_delivery` date DEFAULT NULL,
  `id_point` int(11) DEFAULT NULL,
  `code` int(11) DEFAULT NULL,
  `status_order` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id_order`, `id_user`, `date_order`, `date_delivery`, `id_point`, `code`, `status_order`) VALUES
(1, 3, '2026-05-20', '2026-05-25', 1, 901, 'Завершён'),
(2, 3, '2026-05-22', '2026-05-27', 2, 902, 'Новый');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id_prod_ord` int(11) NOT NULL,
  `id_order` int(11) DEFAULT NULL,
  `id_product` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id_prod_ord`, `id_order`, `id_product`, `quantity`) VALUES
(1, 1, 1, 2),
(2, 1, 3, 1),
(3, 2, 5, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `points`
--

CREATE TABLE `points` (
  `id_point` int(11) NOT NULL,
  `index` int(11) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `street` varchar(100) NOT NULL,
  `house` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `points`
--

INSERT INTO `points` (`id_point`, `index`, `city`, `street`, `house`) VALUES
(1, 420151, 'Лесной', 'Вишневая', 32),
(2, 125061, 'Лесной', 'Подгорная', 8);

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id_product` int(11) NOT NULL,
  `article_number` varchar(50) DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `measurement_unit` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `id_manufacturer` int(11) DEFAULT NULL,
  `id_product_category` int(11) DEFAULT NULL,
  `current_discount` int(11) DEFAULT 0,
  `stock_quantity` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id_product`, `article_number`, `product_name`, `measurement_unit`, `price`, `id_supplier`, `id_manufacturer`, `id_product_category`, `current_discount`, `stock_quantity`, `description`, `image`) VALUES
(1, 'А112Т4', 'Шпатлёвка финишная', 'кг', 499.00, 1, 1, 1, 3, 6, 'Готовая к применению', 'product1.jpg'),
(2, 'F635R4', 'Грунтовка глуб. проник.', 'л', 324.00, 2, 1, 1, 2, 13, 'Для бетонных стен', 'product2.jpg'),
(3, 'H782T5', 'Дюбель 6x40', 'шт', 4.50, 1, 2, 2, 4, 500, 'Нейлоновый', NULL),
(4, 'G783F5', 'Анкер клиновой M10', 'шт', 59.00, 1, 2, 2, 2, 80, 'Оцинкованный', NULL),
(5, 'J384T6', 'Пена монтажная', 'баллон', 380.00, 2, 3, 1, 2, 16, 'Профессиональная', NULL),
(6, 'D572U8', 'Клей для плитки', 'кг', 410.00, 2, 1, 1, 3, 6, 'Морозостойкий', NULL),
(7, 'F572H7', 'Саморез по бетону', 'шт', 2.70, 1, 2, 2, 2, 140, 'Потайная головка', NULL),
(8, 'D329H3', 'Лента малярная', 'рул', 189.00, 2, 3, 1, 4, 4, '50мм х 50м', NULL),
(9, 'B320R5', 'Гипсокартон влагостойкий', 'лист', 430.00, 1, 1, 1, 2, 6, '2500х1200х12.5', NULL),
(10, 'G432E4', 'Уголок перфорированный', 'шт', 28.00, 1, 2, 2, 3, 150, '20х20х2000мм', NULL),
(11, 'S213E3', 'Шуруповёрт аккумуляторный', 'шт', 2156.00, 2, 1, 1, 3, 6, '12V, 2 аккумулятора', NULL),
(12, 'E482R4', 'Валик малярный', 'шт', 180.00, 1, 2, 1, 2, 14, '250мм, велюр', NULL),
(13, 'S634B5', 'Сетка штукатурная', 'рул', 550.00, 2, 3, 1, 3, 0, '2х20м, ячейка 5мм', NULL),
(14, 'K345R4', 'Правило алюминиевое', 'шт', 2100.00, 2, 2, 1, 2, 3, '2.5 метра', NULL),
(15, 'O754F4', 'Краска интерьерная', 'л', 540.00, 2, 1, 1, 4, 18, 'Матовая, белая', NULL),
(16, 'G531F4', 'Перфоратор', 'шт', 6600.00, 1, 1, 1, 12, 9, '800 Вт, SDS+', NULL),
(17, 'J542F5', 'Затирка для швов', 'кг', 500.00, 1, 2, 1, 13, 0, 'Эпоксидная', NULL),
(18, 'B431R5', 'Уровень пузырьковый', 'шт', 270.00, 2, 3, 1, 2, 5, '60 см', NULL),
(19, 'P764G4', 'Плитка керамогранит', 'м²', 680.00, 1, 1, 1, 15, 15, '600х600, матовый', NULL),
(20, 'C436G5', 'Утеплитель Rockwool', 'упак', 1020.00, 1, 1, 1, 15, 9, '50мм, 4м²', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `product_categories`
--

CREATE TABLE `product_categories` (
  `id_product_category` int(11) NOT NULL,
  `name_category` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `product_categories`
--

INSERT INTO `product_categories` (`id_product_category`, `name_category`) VALUES
(1, 'Сухие смеси'),
(2, 'Крепёж');

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id_role`, `role`) VALUES
(1, 'Администратор'),
(2, 'Менеджер'),
(3, 'Клиент');

-- --------------------------------------------------------

--
-- Структура таблицы `suppliers`
--

CREATE TABLE `suppliers` (
  `id_supplier` int(11) NOT NULL,
  `name_supplier` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `suppliers`
--

INSERT INTO `suppliers` (`id_supplier`, `name_supplier`) VALUES
(1, 'ООО \"СтройКомплект\"'),
(2, 'ИП Иванов А.С.');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `id_role` int(11) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `login` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id_user`, `id_role`, `last_name`, `first_name`, `surname`, `login`, `password`) VALUES
(1, 1, 'Никифорова', 'Весения', 'Николаевна', 'admin@example.com', 'admin123'),
(2, 2, 'Степанов', 'Михаил', 'Артёмович', 'manager@example.com', 'manager123'),
(3, 3, 'Михайлюк', 'Анна', 'Вячеславовна', 'client@example.com', 'client123'),
(4, 1, 'мыфпавыф', 'апвыфпаыв', 'павыпавы', 'admin', 'admin');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `manufacturers`
--
ALTER TABLE `manufacturers`
  ADD PRIMARY KEY (`id_manufacturer`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_point` (`id_point`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id_prod_ord`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_product` (`id_product`);

--
-- Индексы таблицы `points`
--
ALTER TABLE `points`
  ADD PRIMARY KEY (`id_point`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `id_supplier` (`id_supplier`),
  ADD KEY `id_manufacturer` (`id_manufacturer`),
  ADD KEY `id_product_category` (`id_product_category`);

--
-- Индексы таблицы `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id_product_category`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`);

--
-- Индексы таблицы `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id_supplier`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `manufacturers`
--
ALTER TABLE `manufacturers`
  MODIFY `id_manufacturer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id_prod_ord` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `points`
--
ALTER TABLE `points`
  MODIFY `id_point` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id_product_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`id_point`) REFERENCES `points` (`id_point`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`id_supplier`) REFERENCES `suppliers` (`id_supplier`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`id_manufacturer`) REFERENCES `manufacturers` (`id_manufacturer`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`id_product_category`) REFERENCES `product_categories` (`id_product_category`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
