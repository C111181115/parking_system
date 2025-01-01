<?php
$host = '13.54.4.168'; // 資料庫主機
$username = 'root'; // 資料庫用戶名
$password = 'root'; // 資料庫密碼
$dbname = 'parking_2025'; // 資料庫名稱

// 創建資料庫連接
$conn = new mysqli($host, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 設定字符集
$conn->set_charset("utf8mb4");
?> 