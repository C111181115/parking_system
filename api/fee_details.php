<?php
session_start();
require_once 'config/database.php';

// 檢查是否有費用信息
if (!isset($_SESSION['parking_fee'])) {
    header("Location: dashboard.php");
    exit();
}

$fee_info = $_SESSION['parking_fee'];

// 計算總分鐘數
$total_minutes = $fee_info['total_minutes'];

// 獲取車位信息
$space_id = $fee_info['space_id'];
$query = "SELECT * FROM parking_spaces WHERE id = '$space_id'";
$result = mysqli_query($conn, $query);
$space_info = mysqli_fetch_assoc($result);

// 檢查是否成功獲取車位信息
if (!$space_info) {
    echo "無法獲取車位信息。";
    exit();
}

// 更新預約狀態為已結束
$license_plate = $_SESSION['plate_number'] ?? '';
$query = "UPDATE reservations SET end_time = NOW() WHERE space_id = '$space_id' AND license_plate = '$license_plate'";
mysqli_query($conn, $query);

// 更新停車格狀態為可預約
$query = "UPDATE parking_spaces SET status = 'available' WHERE id = '$space_id'";
mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>停車費用明細</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-8">
                <i class="fas fa-receipt text-blue-500 text-4xl mb-4"></i>
                <h1 class="text-2xl font-bold text-gray-800">停車費用明細</h1>
            </div>

            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">停車位置</span>
                    <span class="font-medium">第 <?= $space_info['floor'] ?> 樓 <?= $space_info['space_number'] ?> 號車位</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">停車時間</span>
                    <span class="font-medium duration-display">
                        <?php 
                        if ($total_minutes < 60) {
                            echo "{$total_minutes}分鐘";
                        } else {
                            $hours = floor($total_minutes / 60);
                            $minutes = $total_minutes % 60;
                            if ($minutes > 0) {
                                echo "{$hours}小時{$minutes}分鐘";
                            } else {
                                echo "{$hours}小時";
                            }
                        }
                        ?>
                    </span>
                </div>

                <div class="flex justify-between items-center text-lg font-bold">
                    <span class="text-gray-800">應付金額</span>
                    <span class="text-blue-600">NT$ <?= $fee_info['fee'] ?></span>
                </div>
            </div>

            <div class="mt-8">
                <a href="dashboard.php" 
                   class="block w-full bg-blue-500 hover:bg-blue-600 text-white text-center font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                    <i class="fas fa-home mr-2"></i>
                    返回主頁
                </a>
            </div>
        </div>
    </div>

    <?php
    // 清除費用信息，避免重複顯示
    unset($_SESSION['parking_fee']);
    ?>
</body>
</html>