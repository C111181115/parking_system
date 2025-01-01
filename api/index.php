<?php
session_start();
require_once 'database.php';

// 檢查是否已提交表單
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plate_number = strtoupper(trim($_POST['plate_number'])); // 轉換為大寫並移除空格
    
    // 檢查車牌格式（可依據您的需求修改）
    if (!empty($plate_number)) {
        $stmt = $conn->prepare("SELECT * FROM vehicle_users WHERE plate_number = ?");
        $stmt->bind_param("s", $plate_number);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // 現有用戶
            $user = $result->fetch_assoc();
            // 更新最後訪問時間和訪問次數
            $updateStmt = $conn->prepare("UPDATE vehicle_users SET last_visit = NOW(), visit_count = visit_count + 1 WHERE id = ?");
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();
        } else {
            // 新用戶，創建記錄
            $insertStmt = $conn->prepare("INSERT INTO vehicle_users (plate_number) VALUES (?)");
            $insertStmt->bind_param("s", $plate_number);
            $insertStmt->execute();
        }
        
        // 設置 session
        $_SESSION['plate_number'] = $plate_number;
        $_SESSION['logged_in'] = true;
        
        // 重定向到客戶端首頁
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>停車場系統 - 登入</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full border border-gray-300">
        <div class="text-center mb-6">
            <i class="fas fa-parking text-blue-500 text-6xl"></i>
            <h2 class="text-3xl font-bold mt-2">智慧停車場系統</h2>
            <p class="text-gray-600">請輸入您的車牌號碼進入系統</p>
        </div>
        <form method="POST" action="" class="space-y-4">
            <div>
                <label for="plate_number" class="block text-sm font-medium text-gray-700">車牌號碼</label>
                <input type="text" 
                       id="plate_number" 
                       name="plate_number" 
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2" 
                       placeholder="例如：ABC-1234" 
                       pattern="[A-Za-z0-9-]+" 
                       required>
                <div class="text-red-500 text-sm mt-1 hidden" id="invalid-feedback">請輸入有效的車牌號碼</div>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fas fa-sign-in-alt mr-2"></i>進入系統
            </button>
        </form>
        <div class="mt-6 text-center">
            <p class="text-gray-600">如需協助，請聯絡管理員：</p>
            <p class="text-blue-600">admin@example.com</p>
            <p class="text-blue-600">電話：123-456-7890</p>
        </div>
    </div>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        // 表單驗證
        document.querySelector('form').addEventListener('submit', function (event) {
            const plateInput = document.getElementById('plate_number');
            const invalidFeedback = document.getElementById('invalid-feedback');
            if (!plateInput.checkValidity()) {
                event.preventDefault();
                invalidFeedback.classList.remove('hidden');
            } else {
                invalidFeedback.classList.add('hidden');
            }
        });

        // 自動轉換大寫
        document.getElementById('plate_number').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html> 