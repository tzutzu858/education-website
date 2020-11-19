<?php
require_once 'conn.php';
header('Content-type:application/json;charset=utf-8'); //要輸出 json 記得要加這行 header，瀏覽器才會知道他是 json 格式的資料
header('Access-Control-Allow-Origin: *');

$sql = "SELECT * FROM calculation";
$stmt = $conn->prepare($sql);
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

$result = $stmt->get_result();
$calculation = array();
while ($row = $result->fetch_assoc()) {
    array_push($calculation, array(  
        "id" => $row["id"], 
        "title" => $row["title"],
        "percent" => $row["percent"],
    ));
}

$json = array(
    "ok" => true,
    "calculation" => $calculation
);

$response = json_encode($json);
echo $response;
