<?php
require_once "conn.php";
header('Content-type:application/json;charset=utf-8'); //要輸出 json 記得要加這行 header，瀏覽器才會知道他是 json 格式的資料
header('Access-Control-Allow-Origin: *');

if (empty($_POST['content']) ||
    empty($_POST['nickname']) ||
    empty($_POST['site_key'])) {
    $json = array(
        "ok" => false,
        "message" => "Please input missing fields",
    );

    $response = json_encode($json); //變成 json 的格式
    echo $response;
    die();
}

$site_key = $_POST['site_key'];
$nickname = $_POST['nickname'];
$content = $_POST['content'];

$sql = "INSERT INTO discussions(site_Key, nickname, content) VALUES (?, ?, ?) ";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $site_key, $nickname, $content);
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

$sql = "SELECT * FROM discussions WHERE id = @@IDENTITY";
$stmt = $conn->prepare($sql);
$result = $stmt->execute();
$result = $stmt->get_result();
$discussions = array();
$row = $result->fetch_assoc();
array_push($discussions, array(
    "id" => $row["id"],
    "nickname" => $row["nickname"],
    "content" => $row["content"],
    "created_at" => $row["created_at"],
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
    "discussions" => $discussions
);

$response = json_encode($json);
echo $response;
