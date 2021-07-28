<meta charset="utf-8">
<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
if(!$cfg->myip) exit();



$plink = 'https://ru.wikipedia.org/w/api.php/';
$params = [
    "action" => "query",
    "list" => "categorymembers",
    "cmpageid" => "155531",
    "cmlimit" => "500",
    "cmcontinue" => "page|270c0696e82803060427060e10ac9ca24678011301eec0ee0b|7865329",
    "cmsort" => "sortkey",
    "format" => "json"
];

$url = $plink . "?" . http_build_query( $params );
$somepage = curl($url);
$somepage = json_decode($somepage);
printr($somepage);



?>
