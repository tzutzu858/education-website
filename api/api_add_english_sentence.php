<?php
require_once "conn.php";
header('Content-type:application/json;charset=utf-8'); //要輸出 json 記得要加這行 header，瀏覽器才會知道他是 json 格式的資料
header('Access-Control-Allow-Origin: *');

if (empty($_POST['username']) ||
    empty($_POST['zh']) ||
    empty($_POST['en'])) {
    $json = array(
        "ok" => false,
        "message" => "Please input missing fields",
    );

    $response = json_encode($json); //變成 json 的格式
    echo $response;
    die();
}

$username = $_POST['username'];
$zh = $_POST['zh'];
$en = $_POST['en'];

$sql = "INSERT INTO daily_english(username, zh, en) VALUES (?, ?, ?) ";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $username, $zh, $en);
$result = $stmt->execute();

if (!$result) {
    $json = array(
        "ok" => false,
        "message" => $conn->error,
    );

    $response = json_encode($json);
    echo $response;
    die();
}

$sql = "SELECT * FROM daily_english WHERE id = @@IDENTITY";
$stmt = $conn->prepare($sql);
$result = $stmt->execute();
$result = $stmt->get_result();
$dailyEnglish = array();
$row = $result->fetch_assoc();
array_push($dailyEnglish, array(
    "id" => $row["id"],
    "zh" => $row["zh"],
    "en" => $row["en"],
));

if (!$result) {
    $json = array(
        "ok" => false,
        "message" => $conn->error,
    );

    $response = json_encode($json);
    echo $response;
    die();
}

$json = array(
    "ok" => true,
    "dailyEnglish" => $dailyEnglish,
);

$response = json_encode($json);
echo $response;
