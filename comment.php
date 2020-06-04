<?php 
//require_once 'includs/ip.php';
//include_once 'includs/config.php';
include_once 'includs/usercheck.php';
if(empty($_POST['send']) or $user_id == 1) exit('send');
include_once 'functions/functs.php';
$uri_from = 'index.php';

$uri_from = $_SERVER['HTTP_REFERER'];
$item_id = intval($_POST['item_id']);
if(!$item_id) exit('item_id');
$text = $_POST['text'];
var_dump($text);
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