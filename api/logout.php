<?php
session_start();

// 清除所有會話變量
$_SESSION = [];

// 如果需要，銷毀會話
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 銷毀會話
session_destroy();

// 重定向到登錄頁面
header("Location: index.php");
exit();
?> 