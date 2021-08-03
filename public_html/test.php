<meta charset="utf-8">
<?php
//$tstart = microtime(true);

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
if (!$cfg->myip)
    die('rrr');
$User = new User();
$User->byIdenty();
$Item = new Item();
$Item->byId(3685);

$Craft = new Craft(7571);
$CraftInfo = new CraftInfo(
        Craft: $Craft,
        Item: $Item
);
$Mat = new Mat();
printr($CraftInfo->mats);
printr(json_encode($CraftInfo));
?>


