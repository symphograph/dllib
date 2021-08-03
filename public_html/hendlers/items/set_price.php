<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST')
    die();

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$User = new User();
if(!$User->byIdenty()){
    die('user');
}

$itemId = $_POST['itemId'] ?? 0;
$itemId = intval($itemId);
if(!$itemId)
    die('itemId');

$value = $_POST['Price'] ?? 0;
$value = intval($value);
if(!$value)
    die('Price');

$Price = new Price($itemId);
if(!$Price->insert($value)){
    die('insertErr');
}

echo json_encode([]);

