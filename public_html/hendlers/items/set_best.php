<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

$craft_id = $_POST['craft_id'] ?? 0;
$craft_id = intval($craft_id);
if(!$craft_id) exit();

$User = new User;
if(!$User->byIdenty())
	die();

$Craft = new Craft($craft_id);
$Item = new Item();
$Item->byId($Craft->result_item_id);
$Item->setUserBestCraft($Craft->craft_id);

echo json_encode([]);
?>