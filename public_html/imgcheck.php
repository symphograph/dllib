<?php
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if(!$cfg->myip) exit();

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

