<?php
$from_id = $_POST['from_id'] ?? 0;
$from_id = intval($from_id);
if(!$from_id) die();


$to_id = $_POST['to_id'] ?? 0;
$to_id = intval($to_id);



require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

SelectZone($from_id,$to_id);

?>