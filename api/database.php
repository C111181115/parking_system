<?php
$host = getenv('DB_SERVER');; // 資料庫伺服器
$username = getenv('DB_USERNAME');;        // 資料庫用戶名
$password = getenv('DB_PASSWORD');;            // 資料庫密碼
$dbname = getenv('DB_NAME');;  // 資料庫名稱

// 創建資料庫連接
$conn = new mysqli($host, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 設定字符集
$conn->set_charset("utf8mb4");
?> 