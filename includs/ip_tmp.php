<?php
$ip = $_SERVER['REMOTE_ADDR'];

$myip = ($ip == '000.000.000.00');


if($myip)
{
	ini_set('display_errors',1);
	error_reporting(E_ALL);
};

$connects = 
[
	'test.dllib.ru'=>
	[
		['dbHost' => 'localhost'],
		['dbName' => 'xxxxx'],
		['dbUser' => 'xxxxx'],
		['dbPass' => 'xxxxx']
	],

	'dllib.ru'=>
	[
		['dbHost' => 'localhost'],
		['dbName' => 'xxxxx'],
		['dbUser' => 'xxxxx'],
		['dbPass' => 'xxxxx']
	]	
];
?>