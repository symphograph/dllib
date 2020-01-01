<?php
include 'includs/ip.php';
if(!$myip) exit();

include 'includs/config.php';
include 'functions/pars_functs.php';
include 'functions/filefuncts.php';
include 'functions/functs.php';
include 'functions/functions.php';

$qwe = qwe("
SELECT item_id, icon FROM `items`
/*LIMIT 1000*/
");
foreach($qwe as $q)
{
	extract($q);
	$file = 'img/icons/50/'.$icon.'.png';
	$im = imagecreatefrompng($file);
	if(!$im) continue;
	$hash = hash_file('md5',$file);
	qwe("
	UPDATE `items` SET `md5_icon` = '$hash' WHERE item_id = '$item_id'
	");
}

?>