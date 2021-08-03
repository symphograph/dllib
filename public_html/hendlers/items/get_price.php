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

$Price = new Price($itemId);
$Price->initData();
echo json_encode($Price);

