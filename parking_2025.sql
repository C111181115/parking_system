-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-01-01 17:57:19
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `parking_2025`
--

DELIMITER $$
--
-- 程序
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertParkingSpots` ()   BEGIN
    DECLARE floor INT DEFAULT 1;
    DECLARE spot INT DEFAULT 1;
    
    WHILE floor <= @floor_count DO
        SET spot = 1;
        WHILE spot <= @spot_per_floor DO
            SET @status = IF(RAND() < 0.5, 'available', 'occupied'); -- 50% 機率為可用或已佔用
            INSERT INTO parking_spots (floor_number, spot_number, status) 
            VALUES (floor, CONCAT('Spot ', spot), @status);
            SET spot = spot + 1;
        END WHILE;
        SET floor = floor + 1;
    END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 資料表結構 `parking_records`
--

CREATE TABLE `parking_records` (
  `id` int(11) NOT NULL,
  `license_plate` varchar(20) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `fee` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `parking_records`
--

INSERT INTO `parking_records` (`id`, `license_plate`, `start_time`, `end_time`, `fee`) VALUES
(1, 'BCD-5566', '2024-12-30 16:46:39', '2024-12-30 16:47:32', 50.00),
(2, 'BCD-5566', '2024-12-30 16:47:47', '2024-12-30 16:47:50', 50.00),
(3, 'BCD-5566', '2024-12-30 16:50:27', '2024-12-30 16:50:29', 50.00),
(4, 'BCD-5566', '2024-12-30 16:51:32', '2024-12-30 16:51:36', 50.00),
(5, 'BCD-5566', '2024-12-30 16:53:33', NULL, NULL),
(6, 'ABC-1234', '2024-12-30 16:53:40', '2024-12-30 16:55:13', 50.00),
(7, 'ABC-1234', '2024-12-30 17:13:51', '2024-12-30 17:13:53', 50.00),
(8, 'ABC-1234', '2024-12-30 18:02:07', '2024-12-30 18:02:22', 50.00),
(9, 'ABC-1234', '2024-12-30 18:03:27', '2024-12-30 18:04:32', 50.00),
(10, 'ABC-1234', '2024-12-30 18:04:36', '2024-12-30 18:04:40', 50.00),
(11, 'ABC-1234', '2024-12-30 18:08:07', '2024-12-30 18:09:05', 50.00),
(12, 'ABC-1234', '2024-12-30 18:09:08', '2024-12-30 18:09:14', 50.00),
(13, 'ABC-1234', '2024-12-30 18:09:18', '2024-12-30 18:28:41', 50.00),
(14, 'ABC-1234', '2024-12-30 18:28:44', '2024-12-30 18:29:28', 50.00),
(15, 'ABC-1234', '2024-12-30 18:40:06', '2024-12-30 18:40:08', 50.00);

-- --------------------------------------------------------

--
-- 資料表結構 `parking_spaces`
--

CREATE TABLE `parking_spaces` (
  `id` int(11) NOT NULL,
  `floor` int(11) DEFAULT NULL,
  `space_number` varchar(10) DEFAULT NULL,
  `status` enum('available','reserved','occupied') DEFAULT 'available',
  `rate_per_minute` decimal(10,2) DEFAULT 10.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `parking_spaces`
--

INSERT INTO `parking_spaces` (`id`, `floor`, `space_number`, `status`, `rate_per_minute`) VALUES
(1, 1, 'A1', 'available', 10.00),
(2, 1, 'A2', 'available', 10.00),
(3, 1, 'A3', 'available', 10.00),
(4, 1, 'A4', 'available', 10.00),
(5, 1, 'A5', 'available', 10.00),
(6, 1, 'A6', 'available', 10.00),
(7, 1, 'A7', 'available', 10.00),
(8, 1, 'A8', 'available', 10.00),
(9, 1, 'A9', 'available', 10.00),
(10, 1, 'A10', 'available', 10.00),
(11, 2, 'B1', 'available', 10.00),
(12, 2, 'B2', 'available', 10.00),
(13, 2, 'B3', 'available', 10.00),
(14, 2, 'B4', 'available', 10.00),
(15, 2, 'B5', 'available', 10.00),
(16, 2, 'B6', 'available', 10.00),
(17, 2, 'B7', 'available', 10.00),
(18, 2, 'B8', 'available', 10.00),
(19, 2, 'B9', 'available', 10.00),
(20, 2, 'B10', 'available', 10.00),
(21, 3, 'C1', 'available', 10.00),
(22, 3, 'C2', 'available', 10.00),
(23, 3, 'C3', 'available', 10.00),
(24, 3, 'C4', 'available', 10.00),
(25, 3, 'C5', 'available', 10.00),
(26, 3, 'C6', 'available', 10.00),
(27, 3, 'C7', 'available', 10.00),
(28, 3, 'C8', 'available', 10.00),
(29, 3, 'C9', 'available', 10.00),
(30, 3, 'C10', 'available', 10.00);

-- --------------------------------------------------------

--
-- 資料表結構 `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `vehicle_user_id` int(11) DEFAULT NULL,
  `space_id` int(11) DEFAULT NULL,
  `license_plate` varchar(20) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `reservations`
--

INSERT INTO `reservations` (`id`, `vehicle_user_id`, `space_id`, `license_plate`, `start_time`, `end_time`) VALUES
(14, NULL, 4, 'BCD-5566', '2024-12-30 16:36:48', '2024-12-30 17:36:48'),
(15, NULL, 14, 'BCD-5566', '2024-12-30 16:36:52', '2024-12-30 17:36:52'),
(16, NULL, 15, 'BCD-5566', '2024-12-30 16:36:55', '2024-12-30 17:36:55'),
(17, NULL, 11, 'BCD-5566', '2024-12-30 16:37:53', '2024-12-30 17:37:53'),
(18, NULL, 18, 'BCD-5566', '2024-12-30 16:40:20', '2024-12-30 17:40:20'),
(19, NULL, 26, 'BCD-5566', '2024-12-30 18:58:13', '2024-12-30 19:58:13'),
(20, NULL, 22, 'ABC-1234', '2025-01-01 12:53:02', '2025-01-01 13:53:02'),
(21, NULL, 1, 'ABC-1234', NULL, '2025-01-02 00:56:56'),
(22, NULL, 2, 'ABC-1234', NULL, '2025-01-01 23:20:22'),
(23, NULL, 5, 'ABC-1234', '2025-01-01 12:56:01', '2025-01-01 13:56:01'),
(24, NULL, 6, 'ABC-1234', '2025-01-01 12:56:01', '2025-01-01 13:56:01'),
(72, NULL, 11, 'ABC-1234', NULL, '2025-01-01 23:19:51'),
(76, NULL, 1, 'ABC-1234', NULL, '2025-01-02 00:56:56'),
(77, NULL, 2, 'ABC-1234', NULL, '2025-01-01 23:20:22'),
(78, NULL, 3, 'ABC-1234', NULL, '2025-01-01 23:21:22'),
(79, NULL, 4, 'ABC-1234', NULL, '2025-01-01 23:21:58'),
(80, NULL, 1, 'ABC-1234', NULL, '2025-01-02 00:56:56'),
(81, NULL, 1, 'ABC-1234', NULL, '2025-01-02 00:56:56'),
(82, NULL, 1, 'ABC-1234', NULL, '2025-01-02 00:56:56'),
(83, NULL, 1, 'ABC-1234', NULL, '2025-01-02 00:56:56'),
(84, NULL, 1, 'ABC-1234', NULL, '2025-01-02 00:56:56'),
(89, NULL, 1, 'ABC-1234', NULL, '2025-01-02 00:56:56'),
(95, NULL, 1, 'ABC-1234', NULL, '2025-01-02 00:56:56');

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`id`, `plate_number`, `phone`, `email`, `created_at`) VALUES
(1, '<br /><b>Warning</b>', '0958122684', '', '2024-12-30 17:19:49');

-- --------------------------------------------------------

--
-- 資料表結構 `vehicle_users`
--

CREATE TABLE `vehicle_users` (
  `id` int(11) NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `first_visit` datetime DEFAULT current_timestamp(),
  `last_visit` datetime DEFAULT current_timestamp(),
  `visit_count` int(11) DEFAULT 1,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `vehicle_users`
--

INSERT INTO `vehicle_users` (`id`, `plate_number`, `first_visit`, `last_visit`, `visit_count`, `phone`, `email`) VALUES
(1, 'ABC-1234', '2024-12-30 10:37:14', '2025-01-02 00:57:03', 52, NULL, NULL),
(2, 'XYZ-5678', '2024-12-30 10:37:14', '2024-12-30 10:37:14', 1, NULL, NULL),
(3, 'LMN-9012', '2024-12-30 10:37:14', '2024-12-30 10:37:14', 1, NULL, NULL),
(4, 'BCD-5566', '2024-12-30 17:43:11', '2025-01-01 20:11:59', 8, NULL, NULL),
(5, '5566', '2024-12-31 01:25:52', '2024-12-31 01:25:52', 1, '0958122684', '');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `parking_records`
--
ALTER TABLE `parking_records`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `parking_spaces`
--
ALTER TABLE `parking_spaces`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_user_id` (`vehicle_user_id`),
  ADD KEY `space_id` (`space_id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plate_number` (`plate_number`);

--
-- 資料表索引 `vehicle_users`
--
ALTER TABLE `vehicle_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plate_number` (`plate_number`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `parking_records`
--
ALTER TABLE `parking_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `parking_spaces`
--
ALTER TABLE `parking_spaces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `vehicle_users`
--
ALTER TABLE `vehicle_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`vehicle_user_id`) REFERENCES `vehicle_users` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`space_id`) REFERENCES `parking_spaces` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
