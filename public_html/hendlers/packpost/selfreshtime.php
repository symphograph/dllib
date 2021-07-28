<?php
$item_id = $_POST['item_id'] ?? 0;
$item_id = intval($item_id);
if(!$item_id) die();


$from_id = $_POST['from_id'] ?? 0;
$from_id = intval($from_id);
if(!$from_id) die();

$to_id = $_POST['to_id'] ?? 0;
$to_id = intval($to_id);
if(!$to_id) die('to_id');


require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$Pack = new Pack();
$Pack->getFromDB($item_id,$from_id,$to_id);
$Pack->fPerOptions();

?>