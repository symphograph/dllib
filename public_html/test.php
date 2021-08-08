<meta charset="utf-8">
<?php
//$tstart = microtime(true);

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
if (!$cfg->myip)
    die('rrr');
$allMats = [
        1=>44,
        45=>56,
        'tyu'=>34
];
$allMats = json_encode($allMats);
qwe("
REPLACE INTO user_crafts 
    (user_id, craft_id, item_id, isbest, allMats) 
VALUES 
    (1, 1, 1, 2, :allMats)
",['allMats'=>$allMats]);

$qwe = qwe("SELECT * FROM user_crafts
WHERE user_id = 1 AND craft_id = 1
");
$q = $qwe->fetchObject();
printr(json_decode($q->allMats));
?>


