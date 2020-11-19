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
$id = $_GET['id'];
$sql = "SELECT * FROM calculation WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$result = $stmt->execute();
$result = $stmt->get_result();
$calculation = array();
$row = $result->fetch_assoc();
array_push($calculation, array(
    "id" => $row["id"],
    "title" => $row["title"],
    "percent" => $row["percent"],
));

if (!$result) {
    getJson($conn->error);
}

$json = array(
    "ok" => true,
    "calculation" => $calculation,
);

$response = json_encode($json);
echo $response;
