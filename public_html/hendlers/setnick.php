<?php
//var_dump($_POST);
$nick = $_POST['nick'] ?? 0;
//$nick = intval($nick);
if(!$nick)
	die();

$reports = ['<span style="color: red">ой!<span>','ок'];
	
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

$User = new User();
if(!$User->byIdenty())
	die();
$user_id = $User->id;


$ptoken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? 0;
$ptoken = OnlyText($ptoken);
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
WHERE `user_nick` = :nick",['nick'=> $nick]);
if(!$qwe or $qwe->rowCount() >0)
 die('Ник занят');

$qwe = qwe("
UPDATE `mailusers`
SET `user_nick` = :nick
WHERE `mail_id` = :userId
",['nick' => $nick, 'userId' => $User->id]);
if(!$qwe) die('error');

echo 'ok';
?>
