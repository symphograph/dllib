<?php
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User;
$User->check();
$user_id = $User->id;
if(empty($_POST['send']) or $user_id == 1) exit('send');


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