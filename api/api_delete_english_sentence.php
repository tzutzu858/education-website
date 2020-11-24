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
if (empty($_POST['id']) || empty($_POST['username'])) {
    getJson('Please input missing fields');
}
$username = $_POST['username'];
$id = $_POST['id'];
$sql = "UPDATE daily_english SET is_deleted=1 WHERE username = ? AND id =?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $username, $id);
$result = $stmt->execute();
if (!$result) {
    die($conn->error);
}

//get the last data
$sql = "SELECT * FROM `daily_english` ORDER BY id DESC LIMIT 0 , 1";
$result = $conn->query($sql);
if (!$result) {
    die();
}
$row = $result->fetch_assoc();
$lastID = $row['id'];

//check_is_deleted
do {
    $sql = "SELECT * FROM daily_english WHERE username = ? AND id =? AND is_deleted IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $username, $id);
    $result = $stmt->execute();
    if (!$result) {
        getJson($conn->error);
    }
    $result = $stmt->get_result();
    $rows = $result->num_rows;
    if ($rows === 0) { //如果沒有這項資料
        if ($id == $lastID) {
            $id = $id - 1;
            do {
                $sql = "SELECT * FROM daily_english WHERE username = ? AND id =? AND is_deleted IS NULL";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('si', $username, $id);
                $result = $stmt->execute();
                if (!$result) {
                    getJson($conn->error);
                }
                $result = $stmt->get_result();
                $rows = $result->num_rows;
                if ($rows === 0) {
                    $id = $id - 1;
                }
            } while ($rows === 0);
            updateAndGetID($conn, $id, $username);
        }
        $id = $id + 1;
    }
} while ($rows === 0);
updateAndGetID($conn, $id, $username);

function updateAndGetID($conn, $id, $username)
{
    $sql = "UPDATE record_daily_id SET id =? WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $id, $username);
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
    die();
}
