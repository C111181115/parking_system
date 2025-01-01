<?php
session_start();
require_once 'database.php';

// 檢查用戶是否已登錄
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// 獲取當前用戶的車牌號
$plate_number = $_SESSION['plate_number'] ?? '';

// 獲取用戶的預約信息
$query = "SELECT r.*, p.space_number, p.rate_per_minute FROM reservations r JOIN parking_spaces p ON r.space_id = p.id WHERE r.license_plate = '$plate_number' AND r.end_time > NOW()";
$result = mysqli_query($conn, $query);
$user_reservations = mysqli_fetch_all($result, MYSQLI_ASSOC);

// 處理停車和離場請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['park'])) {
        // 開始計時
        $space_id = $_POST['space_id'];
        $start_time = date('Y-m-d H:i:s');
        $query = "UPDATE reservations SET start_time = '$start_time' WHERE space_id = '$space_id' AND license_plate = '$plate_number'";
        mysqli_query($conn, $query);
        // 重新加載頁面以顯示更新的按鈕
        header("Location: parking_management.php");
        exit();
    } elseif (isset($_POST['leave'])) {
        // 離場邏輯
        $space_id = $_POST['space_id'];
        $query = "SELECT start_time, rate_per_minute FROM reservations r JOIN parking_spaces p ON r.space_id = p.id WHERE r.space_id = '$space_id' AND r.license_plate = '$plate_number'";
        $result = mysqli_query($conn, $query);
        $reservation = mysqli_fetch_assoc($result);
        
        $start_time = new DateTime($reservation['start_time']);
        $end_time = new DateTime();
        $interval = $start_time->diff($end_time);
        $duration = $interval->h * 60 + $interval->i; // 總分鐘數

        // 使用從數據庫中獲取的費用
        $rate_per_minute = $reservation['rate_per_minute'];
        $fee = $duration * $rate_per_minute;

        // 更新預約狀態，將開始時間設置為NULL
        $query = "UPDATE reservations SET start_time = NULL WHERE space_id = '$space_id' AND license_plate = '$plate_number'";
        mysqli_query($conn, $query);

        // 將費用信息存儲到會話中
        $_SESSION['parking_fee'] = [
            'total_minutes' => $duration,
            'fee' => $fee,
            'floor' => $reservation['floor'],
            'space_id' => $space_id,
            'space_number' => $reservation['space_number']
        ];

        // 重定向到費用明細頁面
        header("Location: fee_details.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>停車管理</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen">
    <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-4 shadow-md w-full">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold">智慧停車場系統</h1>
            <nav class="mt-2">
                <ul class="flex space-x-6">
                    <li><a href="edit_profile.php" class="hover:underline"><i class="fas fa-user-edit"></i> 修改用戶資料</a></li>
                    <li><a href="logout.php" class="hover:underline"><i class="fas fa-sign-out-alt"></i> 登出</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="flex-grow container mx-auto p-6 flex items-center justify-center">
        <div class="grid grid-cols-1 gap-6">
            <?php foreach ($user_reservations as $reservation): ?>
                <?php
                // 獲取車位信息
                $space_id = $reservation['space_id'];
                $space_query = "SELECT * FROM parking_spaces WHERE id = '$space_id'";
                $space_result = mysqli_query($conn, $space_query);
                $space = mysqli_fetch_assoc($space_result);
                ?>
                <div class="bg-white rounded-lg shadow-lg p-6 transition-transform transform hover:scale-105 w-full max-w-md">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-car text-4xl text-blue-500 mb-4"></i>
                        <p class="text-lg font-semibold mb-4 text-center">車位 <?php echo $space['space_number']; ?></p>
                        <form action="" method="post" class="w-full">
                            <input type="hidden" name="space_id" value="<?php echo $space['id']; ?>">
                            <?php if (empty($reservation['start_time'])): ?>
                                <button type="submit" name="park" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition duration-200">停車</button>
                            <?php else: ?>
                                <button type="submit" name="leave" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition duration-200">離場</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer 已刪除 -->
</body>
</html> 