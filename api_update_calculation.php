<?php
require_once 'conn.php';
header('Content-type:application/json;charset=utf-8'); //要輸出 json 記得要加這行 header，瀏覽器才會知道他是 json 格式的資料
header('Access-Control-Allow-Origin: *');

if (empty($_POST['id']) ||
    empty($_POST['username']) ||
    empty($_POST['title']) ||
    empty($_POST['percent'])) {
    getJson('Please input missing fields');
}
$id = $_POST['id'];
$username = $_POST['username'];
$title = $_POST['title'];
$percent = $_POST['percent'];
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

$sql = "UPDATE calculation SET title=? , percent=? WHERE id=? AND username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('siis', $title, $percent, $id, $username);
$result = $stmt->execute();

