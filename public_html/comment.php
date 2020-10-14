<?php 
//require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
//require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';
if(empty($_POST['send']) or $user_id == 1) exit('send');
//require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functs.php';
$uri_from = 'index.php';

$uri_from = $_SERVER['HTTP_REFERER'];
$item_id = intval($_POST['item_id']);
if(!$item_id) exit('item_id');
$text = $_POST['text'];
//var_dump($text);
//$text = strip_tags($text);
$text = mysqli_real_escape_string($dbLink,$text);

if($text != '')
qwe("INSERT INTO `reports` 
(`user_id`, `item_id`, `mess`, `time`, `dtime`)
VALUES 
('$user_id', '$item_id', '$text', now(), now())
");

echo '<meta http-equiv="refresh" content="0; url=catalog.php">';
?>