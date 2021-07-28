<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

$User = new User;
$User->check();
$user_id = $User->id;
if(empty($_POST['send']) or $User->id == 1) exit('send');


$item_id = intval($_POST['item_id']);
if(!$item_id) exit('item_id');
$text = $_POST['text'];
if(empty($text)){
    die('text is empty');
}

qwe("INSERT INTO `reports` 
(`user_id`, `item_id`, `mess`, `time`, `dtime`)
VALUES 
(:user_id, :item_id, :text, now(), now())
",['user_id'=>$user_id, 'item_id'=> $item_id, 'text'=> $text]);

echo '<meta http-equiv="refresh" content="0; url=catalog.php">';
?>