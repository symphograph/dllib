<meta charset="utf-8">
<?php

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if(!$cfg->myip) exit();
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';


printr($_GET);
printr($_COOKIE);


?>

