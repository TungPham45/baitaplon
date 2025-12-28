<?php
session_start();

// 1. Kết nối CSDL (Đảm bảo ConnectDB.php định nghĩa cả $conn_pdo và $conn_mysqli)
require_once __DIR__ . '/app/config/ConnectDB.php';

// 2. Lấy URL từ .htaccess truyền vào
$url = isset($_GET['url']) ? $_GET['url'] : 'home';

// 3. Xử lý chuỗi URL
$url = rtrim($url, '/');
$urlArr = explode('/', filter_var($url, FILTER_SANITIZE_URL));

// --- BƯỚC A: XÁC ĐỊNH CONTROLLER ---
$controllerName = 'Home'; 
if (!empty($urlArr[0])) {
    $controllerName = ucfirst($urlArr[0]);
}

$controllerFile = __DIR__ . '/app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Danh sách các Controller do bạn viết và sử dụng chuẩn PDO
    $myControllers = ['Auth', 'Admin', 'User', 'Home'];
    
    if (class_exists($controllerName)) {
        // --- SỬA TẠI ĐÂY: XỬ LÝ ĐA KẾT NỐI CHO HOME ---
        if ($controllerName === 'Home') {
            // Truyền cả 2 loại kết nối vào Home để xử lý cả Admin và User
            $controller = new Home($conn_pdo, $conn_mysqli);
        } elseif (in_array($controllerName, $myControllers)) {
            // Các controller khác của bạn dùng PDO
            $controller = new $controllerName($conn_pdo); 
        } else {
            // Controller của thành viên khác dùng MySQLi
            $controller = new $controllerName($conn_mysqli);
        }
    } else {
        die("Lỗi: Không tìm thấy class '$controllerName' trong file.");
    }
} else {
    die("Lỗi 404: Không tìm thấy trang (Controller '$controllerName' not found).");
}

// --- BƯỚC B: XÁC ĐỊNH ACTION (HÀM) ---
$actionName = 'index'; 
if (!empty($urlArr[1])) {
    $actionName = $urlArr[1];
}

// --- BƯỚC C: XÁC ĐỊNH THAM SỐ (PARAMS) ---
$params = array_slice($urlArr, 2);

// --- BƯỚC D: GỌI HÀM ---
if (method_exists($controller, $actionName)) {
    call_user_func_array([$controller, $actionName], $params);
} else {
    // Hỗ trợ logic cũ nếu hàm index không tồn tại nhưng có Get_data
    if ($actionName == 'index' && method_exists($controller, 'Get_data')) {
        call_user_func_array([$controller, 'Get_data'], $params);
    } else {
        die("Lỗi: Chức năng '$actionName' không tồn tại trong '$controllerName'.");
    }
}
?>