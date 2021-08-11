<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/config.php';

$User = new User();
if(!$User->byIdenty()){
    die('user');
}

$side = $_POST['side'] ?? 0;
$side = intval($side);
if(!$side)
    die('side');

$zones[1] = (new Side(1))->getZonesForSelect();
$zones[2] = (new Side(2))->getZonesForSelect();
$zones[3] = (new Side(3))->getZonesForSelect();
echo json_encode($zones);

