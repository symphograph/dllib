<?php
require_once 'includs/ip.php';
if(!$myip) exit();
include_once 'functions/functions.php';
include_once 'includs/config.php';

function CheckLostImages()
{
	$qwe = qwe("
	SELECT `item_id`, `icon` 
	FROM `items`
	WHERE `on_off` = 1
	");
	$lost=[];
	foreach($qwe as $q)
	{
		extract($q);
		if(!file_exists('img/icons/50/'.$icon.'.png'))
			$lost[$icon] = $item_id;
	}
	sort($lost);
	return $lost;
}
$lost = CheckLostImages();
$lost = implode('<br>',$lost);
printr($lost);
?>

