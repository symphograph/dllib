<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functs.php';

$userinfo_arr = Metka($dbLink,$ip);
$mailuserid = $userinfo_arr['muser'];
//var_dump($identy);
?>