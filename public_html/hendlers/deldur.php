<?php
$dur_id = $_POST['dur_id'] ?? 0;
$dur_id = intval($dur_id);
if(!$dur_id) die();

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User;
$User->byIdenty();
if(!$user_id) die('user_id');

$ptoken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? 0;
$ptoken = OnlyText($ptoken);
$token = AskToken();
if((!$token) or (!$ptoken) or $ptoken != $token)
    die('token');

qwe("
delete from user_routimes 
where dur_id = $dur_id 
and user_id = $user_id");

echo 'ok';
?>