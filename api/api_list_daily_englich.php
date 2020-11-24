<?php
require_once 'conn.php';
header('Content-type:application/json;charset=utf-8'); //要輸出 json 記得要加這行 header，瀏覽器才會知道他是 json 格式的資料
header('Access-Control-Allow-Origin: *');

function getJson($message)
{
    $json = array(
        "ok" => false,
        "message" => $message,
    );

    $response = json_encode($json); //變成 json 的格式
    echo $response;
    die();
}

// get id
$sql = "SELECT * FROM record_daily_id WHERE username = 'gushanvic'";
$result = $conn->query($sql);
if (!$result) {
    die($conn->error);
}
$row = $result->fetch_assoc();
$id = $row['id'];

//get daily english sentence
$sql = "SELECT * FROM daily_english WHERE id = ? AND is_deleted IS NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
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
    getJson($conn->error);
}

$json = array(
    "ok" => true,
    "dailyEnglish" => $dailyEnglish,
);

$response = json_encode($json);
echo $response;
