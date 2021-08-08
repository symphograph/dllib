<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

$item_id = $_POST['item_id'] ?? 0;
$item_id = intval($item_id);
if(!$item_id) exit();

$User = new User;
if(!$User->byIdenty())
	die();


$Item = new Item();
$Item->byId($item_id);
$Item->unsetBestCraft();

echo json_encode([]);
?>