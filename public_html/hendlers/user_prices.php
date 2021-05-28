<?php
$puser_id = $_POST['puser_id'] ?? 0;
$puser_id = intval($puser_id);
if(!$puser_id)
    die('puser');

$sort = $_POST['sort'] ?? 0;
$sort = intval($sort);
if(!$sort)
    die('sort');

$sorts = [
        'ORDER BY `isbased` DESC, ismybuy DESC, `prices`.`time` DESC',
    'ORDER BY `prices`.`time` DESC',
    'ORDER BY `item_name`',
    ];

$based_prices = '32103,32106,2,3,4,23633,32038,8007,32039,3712,27545,41488';
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User();
$User->check();

?>
<div id="items">
    <div class="prices">
        <?php
        $intimStr = implode(',',IntimItems());
        $valutignor = '';
        if($puser_id != $User->id) {
            $valutignor = 'AND `prices`.`item_id` NOT in (' . $intimStr . ')';
        }
        $qwe = qwe("
                        SELECT 
                        `prices`.`item_id`, 
                        `prices`.`auc_price`, 
                        `prices`.`time`,
                        `items`.`item_name`,
                        `items`.`icon`,
                        `items`.`basic_grade`,
                        `items`.`item_id` IN ( $based_prices ) as `isbased`,
                        user_crafts.isbest,
                        user_crafts.isbest = 3 as ismybuy,
                        items.craftable
                        FROM `prices`
                        INNER JOIN `items` ON `items`.`item_id` = `prices`.`item_id`
                        AND `prices`.`user_id` = '$puser_id'
                        AND `prices`.`server_group` = '$User->server_group'
                        " . $valutignor . "
                        LEFT JOIN user_crafts ON user_crafts.user_id = '$User->id' AND user_crafts.item_id = `prices`.`item_id`
                        AND user_crafts.isbest > 0
                        ".$sorts[$sort]);

        UserPriceList2($qwe);
        ?>
    </div>
</div>