<?php
$path = $_SERVER['PHP_SELF'];
$path = str_replace('/','',$path);
$path = str_replace('.php','',$path);
//var_dump($path);
$exit = '<br>
<a href="exit.php?exit=1&path='.$path.'" style="color: #6C3F00; text-decoration: none;">Выйти</a>';
$avaUrl = $avatar;
$avatar = '<img src="'.$avaUrl.'" width="50" height="50" alt="avatar"/>'.$exit;

if($email == '')
{
	$exit = ''; 
	$avatar = '';
	$fname  = 'Здравствуйте, '.$fname.'!<br><a href="oauth/mailru.php?path='.$path.'" style="color: #6C3F00; text-decoration: none;">
<br><b>Войдите</b></a>, чтобы Ваши 
<br>настройки сохранялись.<br>
';
}else
{
	$fname = 'Привет, '.$fname.'! ';
}
?>