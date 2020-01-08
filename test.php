<meta charset="utf-8">
<?php
require_once 'includs/ip.php';
if(!$myip) exit();
include_once 'functions/functions.php';
include_once 'includs/config.php';
//echo date('d.m.Y',time());
$token = md5($identy.date('d.m.Y',time()));
echo $token;
die;
function AccFamilyFinder($user_id)
{
	global $founded;
	if(!isset($founded))
		$founded = [0];
	//var_dump($founded);
	$fstr = implode(',',$founded);
	//echo $fstr;
	//die;
	$query = qwe("
	SELECT DISTINCT `sessmark` FROM `sessions` 
	WHERE user_id = '$user_id' and user_id not in (".$fstr.")
	");
	//var_dump($query);
	if(($query->num_rows)==0)
	{
		//$founded = [];
		return [];
	}
	foreach($query as $q)
	{
		extract($q);
		$query2 = qwe("
		SELECT DISTINCT `user_id` as `mail_id` FROM `sessions` 
		WHERE `sessmark` = '$sessmark' and user_id !='$user_id'
		");
		foreach($query2 as $q2)
		{
			extract($q2);
			$founded[] = $mail_id;
			AccFamilyFinder($mail_id);
			
		}
	}
	unset($founded[0]);
	return $founded;
}



$familes = [];
$query = qwe("
SELECT `mail_id` FROM `mailusers` WHERE `email` is NOT NULL
");
foreach($query as $q)
{
	extract($q);
	$find = AccFamilyFinder($mail_id);
	
	if(count($find)>0)
	$familes[$mail_id] = $find;
	
	unset($founded);
}
printr($familes);

die;
$array = ['gold'=>7890,'silver'=>99,'bronse'=>99];
echo phpinfo();
die;

function python($array,$path)
{
	global $myip;
	if($myip) 
		$debug = " 2>&1";
	else
		$debug = "";
	$jdata = escapeshellarg(json_encode($array));
	$path = 'python3.7 '.$path.' ';
	$result = shell_exec($path . $jdata . $debug);
	return $result;
	//return json_decode($result, true);
}
$result = python($array,'python/test.py');
echo '<meta charset="utf-8">';
var_dump($result);
?>

