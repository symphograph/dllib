<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

if(!$cfg->transferIp){
    die('permis');
}
use Transfer\Price;

$userId = intval($_POST['userId'] ?? 0)
    or die('err');
$lastDatetime = $_POST['$lastDatetime'] ?? '';
if(empty($lastDatetime)){
    $lastDatetime = date('1970-01-01 00:00:00',);
}
$List = Price::getAllUserPrices($userId, $lastDatetime)
    or die(http_response_code(300));

echo json_encode($List);