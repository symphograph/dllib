<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';


$item_id = $_POST['item_id'] ?? 0;
$item_id = intval($item_id);
if(!$item_id) exit();

$isbuy = $_POST['isbuy'] ?? 0;
$isbuy = intval($isbuy);
if(!in_array($isbuy,[1,3])) exit();


$User = new User;
if(!$User->byIdenty())
	die('<span style="color: red">Oh!<span>');

$Item = new Item();
$Item->byId($item_id);

if($isbuy == 3) {

    $Item->initPrice();
    $try = $Item->setAsBuy();
    if($try != 'ok'){
        die($try);
    }
}

if($isbuy == 1) {
    $Item->unsetAsBuy();
}

echo json_encode([]);