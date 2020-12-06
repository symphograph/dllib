<?php

if(!isset($_POST['from_id'])) die();

$from_id = intval($_POST['from_id']);
if(!$from_id) die();
$to_id = intval($_POST['to_id']);
//var_dump($to_id );
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}

SelectZone($from_id,$to_id);

?>