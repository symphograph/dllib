<?php
$tiptop = $_POST['tiptop'] ?? 0;
$tiptop = intval($tiptop);
if($tiptop != 1)
	die();
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

$qwe = qwe("
SELECT * FROM tiptops  
");
if(!$qwe or !$qwe->rowCount())
	die();
echo json_encode($qwe->fetchAll());
