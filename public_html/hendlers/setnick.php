<?php
//var_dump($_POST);
$nick = $_POST['nick'] ?? 0;
//$nick = intval($nick);
if(!$nick)
	die();

$reports = ['<span style="color: red">ой!<span>','ок'];
	
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functs.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';
//printr($_SERVER);
$ptoken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? 0;
$ptoken = OnlyText($ptoken);

$userinfo_arr = UserInfo();

$report = 1;
if(!$userinfo_arr)
	die();
extract($userinfo_arr);

$user_id = $muser;
$token = AskToken();

if((!$token) or (!$ptoken) or $ptoken != $token)
	die('reload');
if(OnlyText($nick) != $nick) 
	die('invalid');

$nick = OnlyText($nick);
$len = mb_strlen($nick);
if($len<3 or $len>20) 
	die('От 3 до 20 символов');

$qwe = qwe("
SELECT * FROM `mailusers` 
WHERE `user_nick` = '$nick'");
if(!$qwe or $qwe->num_rows >0)
 die('Ник занят');

$qwe = qwe("
UPDATE `mailusers`
SET `user_nick` = '$nick'
WHERE `mail_id` = '$user_id'
");
if(!$qwe) die('error');

echo 'ok';
?>
