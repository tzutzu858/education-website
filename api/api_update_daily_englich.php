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

    $response = json_encode($json);
    echo $response;
    die();
}

if (empty($_POST['num']) || empty($_POST['username'])) {
    getJson('Please input missing fields');
}
$num = $_POST['num'];
$username = $_POST['username'];
// get id
$sql = "SELECT * FROM record_daily_id WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$result = $stmt->execute();
if (!$result) {
    die();
}
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$currentID = $row['id'];

//get the last data
$sql = "SELECT * FROM `daily_english` ORDER BY id DESC LIMIT 0 , 1";
$result = $conn->query($sql);
if (!$result) {
    die();
}
$row = $result->fetch_assoc();
$lastID = $row['id'];

//change id
$sum = $currentID + $num;
if ($sum < 1 || $sum > $lastID) {
    die();
}
//change id_check_deleted
do {
    $sql = "SELECT * FROM daily_english WHERE username = ? AND id =? AND is_deleted IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $username, $sum);
    $result = $stmt->execute();
    if (!$result) {
        getJson($conn->error);
    }
    $result = $stmt->get_result();
    $rows = $result->num_rows;
    if ($rows === 0) { //如果沒有這項資料
        if ($sum > $currentID) {
            $sum = $sum + 1;
        } else {
            $sum = $sum - 1;
        }
    }
} while ($rows === 0);


$sql = "UPDATE record_daily_id SET id =? WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $sum, $username);
$result = $stmt->execute();
if (!$result) {
    getJson($conn->error);
}

//get id again
$sql = "SELECT * FROM record_daily_id WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$result = $stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$dailyEnglishID = array();
array_push($dailyEnglishID, array(
    "id" => $row["id"],
));
$json = array(
    "ok" => true,
    "dailyEnglishID" => $dailyEnglishID,
);
$response = json_encode($json);
echo $response;
