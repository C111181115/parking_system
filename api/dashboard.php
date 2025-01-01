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
$query = "SELECT r.*, p.space_number, p.floor FROM reservations r JOIN parking_spaces p ON r.space_id = p.id WHERE r.license_plate = '$plate_number' AND r.end_time > NOW()";
$result = mysqli_query($conn, $query);
$existing_reservation = mysqli_fetch_assoc($result);

// 從數據庫中讀取停車位信息
$query = "SELECT * FROM parking_spaces";
$result = mysqli_query($conn, $query);
$parking_spaces = mysqli_fetch_all($result, MYSQLI_ASSOC);

// 將停車位按樓層分組
$spaces_by_floor = array();
foreach ($parking_spaces as $space) {
    $floor = $space['floor'];
    if (!isset($spaces_by_floor[$floor])) {
        $spaces_by_floor[$floor] = array();
    }
    $spaces_by_floor[$floor][] = $space;
}

// 獲取所有樓層
$floors = array_keys($spaces_by_floor);

// 獲取當前選擇的樓層,默認為第一個樓層
$current_floor = isset($_GET['floor']) ? intval($_GET['floor']) : $floors[0];
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>智慧停車場系統 - 儀表板</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 flex flex-col">
    <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-4 shadow-md">
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

    <div class="flex flex-grow">
        <aside class="w-1/4 bg-white shadow-lg p-4 rounded-lg">
            <h2 class="text-xl font-bold mb-4">用戶資訊</h2>
            <div class="bg-blue-100 rounded-lg p-4 mb-4 shadow-md">
                <div class="flex items-center mb-2">
                    <div class="bg-blue-200 rounded-full p-2 mr-2">
                        <i class="fas fa-car text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($plate_number); ?></h3>
                </div>
                <?php if ($existing_reservation): ?>
                    <p class="text-green-600">✔ 目前預約</p>
                    <p>第 <?php echo $existing_reservation['floor']; ?> 層 <?php echo $existing_reservation['space_number']; ?> 號車位</p>
                <?php else: ?>
                    <p class="text-red-600">✖ 尚未預約</p>
                <?php endif; ?>
            </div>
            <div class="flex flex-col space-y-2">
                <?php if ($existing_reservation): ?>
                    <a href="parking_management.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded transition duration-200 ease-in-out text-center shadow-md">停車管理</a>
                <?php endif; ?>
                <?php if ($existing_reservation): ?>
                    <form action="cancel_reservation.php" method="post">
                        <input type="hidden" name="reservation_id" value="<?php echo $existing_reservation['id']; ?>">
                        <input type="hidden" name="current_floor" value="<?php echo $current_floor; ?>">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded transition duration-200 ease-in-out w-full shadow-md">取消預約</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- 停車位狀態顯示 -->
            <h2 class="text-xl font-bold mt-4">停車位狀態</h2>
            <div class="flex flex-col space-y-2 mt-2">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-2 text-2xl"></i>
                    <span>可預約</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-times-circle text-red-600 mr-2 text-2xl"></i>
                    <span>已預約</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-wrench text-yellow-600 mr-2 text-2xl"></i>
                    <span>維修中</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-ban text-gray-600 mr-2 text-2xl"></i>
                    <span>停用中</span>
                </div>
            </div>
        </aside>

        <main class="flex-grow container mx-auto p-4">
            <h2 class="text-2xl font-bold mb-4">選擇樓層:</h2>
            <div class="mb-4">
                <select id="floor" name="floor" class="w-full p-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="location = this.value;">
                    <?php foreach ($floors as $floor): ?>
                        <option value="?floor=<?php echo $floor; ?>" <?php if ($floor === $current_floor) echo 'selected'; ?>>
                            樓層 <?php echo $floor; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <h2 class="text-2xl font-bold mb-4">可用車位:</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($spaces_by_floor[$current_floor] as $space): ?>
                    <?php
                    // 設置車位顏色
                    $status_color = $space['status'] === 'available' ? 'bg-green-500' : 'bg-gray-400';
                    if ($existing_reservation && $existing_reservation['space_number'] === $space['space_number']) {
                        $status_color = 'bg-blue-500'; // 用戶預約的車位顏色
                    }
                    ?>
                    <div class="<?php echo $status_color; ?> rounded-lg p-4 text-center text-white shadow-lg transition-transform transform hover:scale-105">
                        <i class="fas fa-car text-4xl"></i>
                        <p class="mt-2">車位 <?php echo $space['space_number']; ?></p>
                        <form action="reserve.php" method="post">
                            <input type="hidden" name="space_id" value="<?php echo $space['id']; ?>">
                            <input type="hidden" name="current_floor" value="<?php echo $current_floor; ?>">
                            <?php if ($existing_reservation): ?>
                                <button type="button" class="mt-4 text-white font-bold py-2 px-4 rounded" disabled>已預約</button>
                            <?php else: ?>
                                <button type="submit" class="mt-4 text-white font-bold py-2 px-4 rounded <?php echo $status_color; ?>" <?php if ($space['status'] !== 'available') echo 'disabled'; ?>>
                                    預約
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
</body>
</html>