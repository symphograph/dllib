<?php
$item_id = $_POST['item_id'] ?? 0;
$item_id = intval($item_id);
if(!$item_id) die();


$from_id = $_POST['from_id'] ?? 0;
$from_id = intval($from_id);
if(!$from_id) die();


if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$Pack = new Pack();
$Pack->getFromDB($item_id);
$Pack->fPerOptions();
//FreshTimeSelect($item_id, $from_id);
?>