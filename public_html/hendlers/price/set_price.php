<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/config.php';

$User = new User();
if(!$User->byIdenty()){
    die('user');
}

$itemId = $_POST['item_id'] ?? 0;
$itemId = intval($itemId);
if(!$itemId)
    die('itemId');

$value = $_POST['price'] ?? 0;
$value = intval($value);
if(!$value)
    die('Price');

$Price = new Price($itemId);
if(!$Price->insert($value)){
    die('insertErr');
}

echo json_encode([]);

