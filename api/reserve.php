<?php
session_start();
require_once 'config/database.php';

// 檢查用戶是否已登錄
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// 獲取當前用戶的車牌號
$plate_number = $_SESSION['plate_number'] ?? '';

// 檢查用戶是否已經有預約
$query = "SELECT * FROM reservations WHERE license_plate = '$plate_number' AND end_time > NOW()";
$result = mysqli_query($conn, $query);
$existing_reservation = mysqli_fetch_assoc($result);

if (!$existing_reservation) {
    // 獲取車位ID
    $space_id = $_POST['space_id'] ?? '';

    // 停車邏輯
    $query = "UPDATE parking_spaces SET status = 'occupied' WHERE id = '$space_id'";
    mysqli_query($conn, $query);

    // 記錄預約
    $query = "INSERT INTO reservations (license_plate, space_id, start_time, end_time) VALUES ('$plate_number', '$space_id', NOW(), DATE_ADD(NOW(), INTERVAL 2 HOUR))";
    mysqli_query($conn, $query);
}

// 獲取當前樓層
$current_floor = $_POST['current_floor'] ?? '1'; // 默認樓層為1

// 重定向回儀表板
header("Location: dashboard.php?floor=" . $current_floor);
exit();
?> 