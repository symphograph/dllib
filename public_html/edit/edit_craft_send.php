<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Документ без названия</title>
</head>

<body>
<?php
if (!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/config.php';
}

if (!$cfg->myip) exit();

if (isset($_POST['cancel'])) {
    echo 'Отменено<br>';
    exit();
};

if (!empty($_POST['craft_id'])) {
    $needs = $_POST['mater_need'];
    print_r($needs);
} else exit();
$result_item_name = $_POST['result_item_name'];
$craft_id = $_POST['craft_id'];

$result_amount = $_POST['result_amount'];
$prof          = $_POST['prof'];
$dood_name     = $_POST['dood_name'];
$rec_name      = $_POST['rec_name'];
$labor_need    = $_POST['labor_need'];
$mins          = intval($_POST['mins']);





if (!empty($_POST['del'])) {

    $del = $_POST['del'];

    $dels = implode(', ', $del);

    qwe("
	DELETE FROM `craft_materials` 
	WHERE `craft_id`='$craft_id' 
	AND `item_id` in ($dels)
	");

    $q_maters = qwe("
	SELECT * FROM `craft_materials` 
	WHERE `item_id` IN ($dels)");

    qwe("
	UPDATE `items` 
	SET `ismat` = 0 
	WHERE `item_id` in ($dels) 
	AND `item_id` 
	NOT in (SELECT `item_id` FROM `craft_materials` where `item_id` in ($dels))
	");
};

if (!empty($_POST['newmat']) and !empty($_POST['newmatneed'])) {
    $newmats = $_POST['newmat'];
    $newmatneed = $_POST['newmatneed'];
    $result_item_id = $_POST['result_item_id'];

    $c = array_combine($newmats, $newmatneed);
};

if (!empty($_POST['prof']) and intval($craft_id) > 0) {
    $prof_id = intval($_POST['prof']);
    qwe("
		UPDATE `crafts` 
		SET `prof_id` = '$prof_id', 
		`profession` = 
		(
			SELECT `profession` 
			FROM `profs` WHERE `prof_id` = '$prof_id'
		) 
		WHERE `craft_id` = '$craft_id'");
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/../functions/craftable.php';
echo '<meta http-equiv="refresh" content="0; url=recedit.php?query=' . $craft_id . '"">';
?>
</body>
</html>