<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST')
    die();
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';


$itemId = $_POST['itemId'] ?? 0;
$itemId = intval($itemId);
if(!$itemId)
    die('itemId');


$arr = [];
$qwe = qwe("
SELECT * FROM items
WHERE item_id = :itemId
AND on_off
LIMIT 0, 50
",['itemId' => $itemId]);
if (!$qwe or !$qwe->rowCount()){
    die('no results');
}
$q = $qwe->fetchObject();

$Item = new Item();
$Item->byId($itemId);
echo json_encode($Item);