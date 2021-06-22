<?php

if(preg_match('/www./',$_SERVER['SERVER_NAME']))
{
	$server_name = str_replace('www.','',$_SERVER['SERVER_NAME']);
	$ref=$_SERVER["QUERY_STRING"];
	if ($ref!="") $ref="?".$ref;
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: https://".$server_name."/".$ref);
	exit();
}

if(!isset($cfg))
	$cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';

if($cfg->myip)
{
	ini_set('display_errors',1);
	error_reporting(E_ALL);
};

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/functions/functions.php';
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/functions/functs.php';


function dbconnect() 
{
	global $dbLink, $cfg;

	if(!isset($cfg))
		$cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';

	$db = (object) $cfg->connects[$_SERVER['SERVER_NAME']];

	if (!isset($dbLink)) 
		$dbLink = mysqli_connect ($db->Host, $db->User,$db->Pass,$db->Name)
		or die("<center><h1>Don't connect with database!!!</h1></center>");
}

// Функция выполнения запросов с логированием ошибок
function qwe($sql)
{
	//var_dump($sql);
	global $dbLink;
	
	if (!isset($dbLink))
		dbconnect();
	
	$return = mysqli_query($dbLink,$sql);
	$error = mysqli_error($dbLink) ?? '';
	if (empty($error))
		return $return;
	
	
	$backtrace = debug_backtrace();
	$file = $backtrace[0]['file'];
	$file = explode($_SERVER['SERVER_NAME'],$file)[1];
	$file = $file.' (line '.$backtrace[0]['line'].')';
	writelog('sql_error', date("Y-m-d H:i:s")."\t".$error."\t".$file."\r\n".$sql);
	return false;
	
}

// Процедура записи в лог фаил для записи ошибок
function writelog($typelog, $log_text) 
{
	$log = fopen($_SERVER['DOCUMENT_ROOT'].'/../logs/'.$typelog.'.txt','a+');
	fwrite($log, "$log_text\r\n");
	fclose($log);
}

dbconnect();

spl_autoload_register(function ($class_name) {
	require_once $_SERVER['DOCUMENT_ROOT'].'/../classes/'.$class_name . '.php';
});

$cfg->vueprod = '.prod';
if(str_starts_with($_SERVER['SERVER_NAME'],'test.')){
	$cfg->vueprod = '';
}
?>
