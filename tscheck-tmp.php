<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once 'includs/config.php';
 $query = qwe("SELECT `nickname` FROM `ts_users`");
 foreach($query as $key){
//$nick = '[Капернаум] Деспина (Дима)';

	$nick = mysqli_real_escape_string($dbLink, $key['nickname']);
	$nickorig = $nick;
	
	$nick = preg_replace('/\\(.*?\\)|\\[.*?\\]|[0-9]{1,2}[:][0-9]{1,2}/s', '', $nick);
	if(preg_match('/ /', $nick) and preg_match('/[a-zа-яё]+/iu',substr($nick,0,strpos(trim($nick),' '))))
	$nick = substr($nick,0,strpos(trim($nick),' '));
	$nick = preg_replace('/\\s*\\([^()]*\\)\\s*/', '',$nick);
    $nick = str_replace(array(")","("),'',$nick);
    if(preg_match('/-/', $nick))
    $nick = substr($nick,0,strpos(trim($nick),'-'));
    $nick = preg_replace('/[^a-zA-ZА-Яа-я0-9\s]/iu','',$nick);

echo 'Исходник: | '.$nickorig.'<br>';
echo 'Результат: |'.$nick.'<hr>';
 };
?>