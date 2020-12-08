<?php
if(!isset($_POST['from_id'])) die();
$from_id = intval($_POST['from_id']);
if(!$from_id) die;
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User;
$User->byIdenty();

if(!$User->byIdenty()) die('user_id');

SelectZone($from_id);

?>