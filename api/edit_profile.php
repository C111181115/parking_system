<?php
session_start();
require_once 'config/database.php';

// 检查用户是否已登录
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// 获取当前用户的资料
$plate_number = $_SESSION['plate_number'] ?? ''; // 确保车牌号存在
$query = "SELECT plate_number, phone, email FROM vehicle_users WHERE plate_number = '$plate_number'";
$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

// 检查用户数据是否存在
if (!$user_data) {
    // 如果没有找到用户数据，初始化为空
    $user_data = [
        'plate_number' => $plate_number,
        'phone' => '',
        'email' => ''
    ];
}

// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_phone = $_POST['phone'] ?? ''; // 如果没有填写，默认为空
    $new_email = $_POST['email'] ?? ''; // 如果没有填写，默认为空

    // 更新用户资料
    $update_query = "UPDATE vehicle_users SET phone = '$new_phone', email = '$new_email' WHERE plate_number = '$plate_number'";
    mysqli_query($conn, $update_query);

    // 重定向到仪表板
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改用戶資料</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
        <h1 class="text-3xl font-bold mb-6 text-center">修改用戶資料</h1>
        <form action="edit_profile.php" method="post">
            <div class="mb-4">
                <label for="plate_number" class="block text-gray-700 font-bold mb-2">車牌號碼:</label>
                <input type="text" id="plate_number" name="plate_number" value="<?php echo htmlspecialchars($user_data['plate_number']); ?>" class="w-full p-2 border border-gray-300 rounded" disabled>
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-gray-700 font-bold mb-2">電話 (可選):</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>" class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">電子郵件 (可選):</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200 ease-in-out w-full">保存修改</button>
        </form>
        <a href="dashboard.php" class="mt-4 inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition duration-200 ease-in-out text-center">返回</a>
    </div>
</body>
</html> 