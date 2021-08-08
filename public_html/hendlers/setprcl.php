<?php

$item_id = $_POST['item_id'] ?? $_GET['item_id'] ?? 0;
$item_id = intval($item_id);
if(!$item_id)
	die();

$reports = ['<span style="color: red">ой!<span>','ок'];
$report = 1;

	
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

$User = new User;
if(!$User->byIdenty())
	die($reports[0]);


if(!empty($_POST['del']) and $_POST['del'] == 'del')
{
	$Price = new Price($item_id);
	if(!$Price->del()){
		$report = 0;
	}

}else
{
	$setprise = PriceValidator([$_POST['setgold'],$_POST['setsilver'],$_POST['setbronze']]);

	if(!$setprise)
		die($reports[0]);

	$Price = new Price($item_id);
	if(!$Price->insert($setprise)){
		$report = 0;
	}
}
echo $reports[$report];
