<?php

if(!isset($_POST['item_id'])) die();

$item_id = intval($_POST['item_id']);
if(!$item_id) die();
$from_id = intval($_POST['from_id']);
if(!$from_id) die();
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functs.php';

FreshTimeSelect($item_id, $from_id);
?>