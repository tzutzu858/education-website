<?php
require_once 'conn.php';
header('Content-type:application/json;charset=utf-8'); //要輸出 json 記得要加這行 header，瀏覽器才會知道他是 json 格式的資料
header('Access-Control-Allow-Origin: *');

$username = $_POST['username'];
$password = $_POST['password'];
function getJson($message) {
    $json = array(
        "ok" => false,
        "message" => $message,
    );

    $response = json_encode($json); //變成 json 的格式
    echo $response;
    die();
}

if (empty($_POST['username']) || empty($_POST['password'])) {
    getJson("Please enter your username and password");
}

$sql = "SELECT * FROM tzu_users WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$result = $stmt->execute();

if (!$result) {
    getJson($conn->error);
}
$result = $stmt->get_result();
if ($result->num_rows === 0) { //如果沒有這項資料
    getJson("Account error");
}

$row = $result->fetch_assoc();
if (password_verify($password, $row['password'])) {
    $json = array(
        "ok" => true,
        "username" => $_POST['username'],
    );
    
    $response = json_encode($json);
    echo $response;
} else {
    getJson("Password error");
}

