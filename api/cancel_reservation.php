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

// 獲取預約ID
$reservation_id = $_POST['reservation_id'] ?? '';

// 取消預約邏輯
$query = "SELECT space_id FROM reservations WHERE id = '$reservation_id'";
$result = mysqli_query($conn, $query);
$space = mysqli_fetch_assoc($result);

// 更新停車位狀態為可用
if ($space) {
    $space_id = $space['space_id'];
    $query = "UPDATE parking_spaces SET status = 'available' WHERE id = '$space_id'";
    mysqli_query($conn, $query);
}

// 刪除預約記錄
$query = "DELETE FROM reservations WHERE id = '$reservation_id'";
mysqli_query($conn, $query);

// 獲取當前樓層
$current_floor = $_POST['current_floor'] ?? '1'; // 默認樓層為1

// 重定向回儀表板
header("Location: dashboard.php?floor=" . $current_floor);
exit();
?> 