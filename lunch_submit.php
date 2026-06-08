<?php
// 1. 設定資料庫連線參數（XAMPP 預設密碼為空）
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_db"; // 請確保與你 phpMyAdmin 中的資料庫名稱一致

// 建立資料庫連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線是否成功
if ($conn->connect_error) {
    die("資料庫連線失敗: " . $conn->connect_error);
}

// 設定編碼為 utf8mb4，確保中文寫入不會變成亂碼
$conn->set_charset("utf8mb4");

// 2. 接收來自 HTML 表單的基本欄位資料
$order_date = isset($_POST['text1']) ? trim($_POST['text1']) : '';
$user_name = isset($_POST['text2']) ? trim($_POST['text2']) : '';

// 3. 核心處理：接收多選的餐點陣列
// 如果使用者什麼都沒勾，$_POST['lunch_items'] 會不存在，這時我們給它一個空陣列
$chosen_items = isset($_POST['lunch_items']) ? $_POST['lunch_items'] : [];

// 4. 資料驗證
if (empty($order_date) || empty($user_name)) {
    echo "<script>alert('錯誤：訂購日期與姓名為必填欄位！'); history.back();</script>";
    exit;
}

if (empty($chosen_items)) {
    echo "<script>alert('錯誤：您還沒有選擇任何餐點喔！'); history.back();</script>";
    exit;
}

// 5. 陣列大變身：用 implode() 把打勾的項目用「逗號 + 空格」串成一條字串
// 例如：陣列 ['麵', '綜合湯', '滷蛋'] 會變成 "麵, 綜合湯, 滷蛋"
$order_details = implode(", ", $chosen_items);

// 6. 撰寫 SQL 語法（對應你的新資料表 form_lunch）
// 這裡我們使用「安全過濾」，防止中文或特殊符號導致 SQL 語法崩潰
$order_date_clean = $conn->real_escape_string($order_date);
$user_name_clean = $conn->real_escape_string($user_name);
$order_details_clean = $conn->real_escape_string($order_details);

$sql = "INSERT INTO form_lunch (order_date, user_name, order_details) 
        VALUES ('$order_date_clean', '$user_name_clean', '$order_details_clean')";

// 7. 執行並即時回報結果給前端
if ($conn->query($sql) === TRUE) {
    // 成功後跳出提示視窗，並自動跳轉回原本的填單頁面
    echo "<script>
            alert('【午餐代訂成功】\\n您的餐點（$order_details）已成功送出！'); 
            location.href='HW0317.html';
          </script>";
} else {
    echo "系統發生錯誤，無法寫入資料庫： " . $conn->error;
}

// 關閉連線
$conn->close();
?>
