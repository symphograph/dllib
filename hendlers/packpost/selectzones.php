<?php

if(!isset($_POST['from_id'])) die();

$from_id = intval($_POST['from_id']);
if(!$from_id) die();
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
SelectZone($from_id);

?>