<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

if(!$cfg->transferIp){
    die('permis');
}
use Transfer\Price;

$userId = intval($_POST['userId'] ?? 0)
    or die('err');
$List = Price::getAllUserPrices($userId)
    or die(http_response_code(300));

echo json_encode($List);