<?php
if(!isset($_POST['from_id'])) die();
$from_id = intval($_POST['from_id']);
if(!$from_id) die;
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';
//printr($_POST);
if(!$user_id) die('user_id');

SelectZone($from_id);

?>