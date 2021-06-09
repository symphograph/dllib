<?php
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if (!$cfg->myip) die();

$craft_id = $_POST['craft_id'] ?? 0;
$craft_id = intval($craft_id);
if(!$craft_id) die('craft_id');

$User = new User;
if(!$User->byIdenty())
    die('<span style="color: red">Oh!<span>');

$Craft = new CraftUpdater(
    craft_id: intval($_POST['craft_id'] ?? 0),
    result_amount: intval($_POST['result_amount'] ?? 0),
    prof_id: intval($_POST['prof_id'] ?? 27),
    prof_need: intval($_POST['prof_need'] ?? 0),
    dood_id: intval($_POST['dood_id'] ?? 0),
    rec_name: $_POST['rec_name'] ?? '',
    labor_need: intval($_POST['labor_need'] ?? 0),
    mins: intval($_POST['mins'] ?? 0)
);


if(!$Craft->valid){
    die('invalid args');
}

if(!$Craft->upIndb()){
    die('invalid sql');
}
$Craft->recountSPM();
$Craft->upMatNeeds(needs: $_POST['mater_need'] ?? []);
$Craft->delSomeMats(mats: $_POST['del'] ?? []);


$mats = matCombine($_POST['newmat'] ?? [],$_POST['newmatneed'] ?? []);
$Craft->addMats($mats);

function matCombine(array $newmats,array $newmatneed) : array
{
    if(count($newmats) !== count($newmatneed))
        return [];

   return array_combine($newmats, $newmatneed);
}

echo 'ok';