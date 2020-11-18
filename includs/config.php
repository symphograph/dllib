<?php
$server_name = $_SERVER['SERVER_NAME'];

if(preg_match('/www./',$server_name))
{
	$server_name = str_replace('www.','',$server_name);
	$ref=$_SERVER["QUERY_STRING"];
	if ($ref!="") $ref="?".$ref;
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: https://".$server_name."/".$ref);
	exit();
}

function dbconnect() 
{
	global $dbLink, $myip, $connects;
	
	/*
	if(!isset($connects))
	$connects = 
	[
		'test.dllib.ru'=>
		[
			['dbHost' => 'localhost'],
			['dbName' => 'xxxxxx'],
			['dbUser' => 'xxxxxx'],
			['dbPass' => 'xxxxxx']
		],
		
		'dllib.ru'=>
		[
			['dbHost' => 'localhost'],
			['dbName' => 'xxxxxx'],
			['dbUser' => 'xxxxxx'],
			['dbPass' => 'xxxxxx']
		]	
	];
	*/
	$server_name = $_SERVER['SERVER_NAME'];
	
	foreach($connects[$server_name] as $db)
	{
		extract($db);
	}
	

	if (!isset($dbLink)) 
		$dbLink = mysqli_connect ($dbHost, $dbUser,$dbPass,$dbName)
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
?>
