<meta charset="utf-8">
<?php
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if(!$cfg->myip) exit();



$plink = 'https://ru.wikipedia.org/w/api.php/';
$params = [
    "action" => "query",
    /*"list" => "categorymembers",*/
    "pageids" => "20331",
    "prop" => "pageterms",
    /*"cmlimit" => "500",*/
    "format" => "json"
];

$url = $plink . "?" . http_build_query( $params );
$somepage = curl($url);
$somepage = json_decode($somepage);
printr($somepage);



?>
