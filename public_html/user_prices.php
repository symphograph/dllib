<?php
setcookie('path', 'user_prices');
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User();
$User->check();
$user_id = $User->id;
$ver = random_str(8);


$puser_id = $_GET['puser_id'] ?? $User->id;
$puser_id = intval($puser_id);
if(!$puser_id)
    die();

$Puser = new User();
$Puser->byId($puser_id);

$based_prices = '32103,32106,2,3,4,23633,32038,8007,32039,3712,27545,41488';

?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
  <meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, крафт" />
  <meta name=“robots” content=“index, nofollow”>
<title>Цены пользователя</title>
<link href="css/default.css?ver=<?php echo md5_file('css/default.css')?>" rel="stylesheet">
<link href="css/user_prices.css?ver=<?php echo md5_file('css/user_prices.css')?>" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>

</head>

<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php'; ?>

<main>
    <div id="rent">

        <div class="navcustoms">
            <h2>
                <?php
                    if($User->id == $Puser->id)
                        echo 'Ваши цены';
                    else
                        echo 'Цены пользователя ', $Puser->user_nick;
               ?>
            </h2><br>
            <div class="prmenu">
                <?php $User->ServerSelect();
                $intimStr = implode(',',IntimItems());
                if($puser_id != $User->id)
                {
                    $valutignor = 'AND `prices`.`item_id` NOT in ('.$intimStr.')';
                    $chks = ['','checked'];
                    $chk = intval($User->IsFolow($puser_id));
                    ?>
                    <label for="folw" data-tooltip="Если цена этого пользователя новее Вашей, она будет использована в расчетах.">
                    Доверять этим ценам
                        <input type="checkbox" <?php echo $chks[$chk]?> name="sfolow" id="folw" value="<?php echo $puser_id?>">
                    </label>

                    <?php
                    if($User->ismobiledevice)
                    {
                        echo '<p>Если цена этого пользователя новее Вашей, она будет использована в расчетах.</p>';
                    }
                    ?>
                    <br>
                    <a href="user_prices.php"><button class="def_button">Мои цены</button></a>
                    <?php
                }else
                {
                    $valutignor = '';
                }
                ?>

                <a href="user_customs.php"><button class="def_button">Настройки</button></a>
            </div>
            <br><hr>
            <div class = "responses"><div id = "responses"></div></div>
        </div>

        <div id="rent_in" class="rent_in">
            <div class="clear"></div>

            <div class="all_info_area">
                <div class="all_info" id="all_info">
                    <div id="items">
                        <div class="prices">
                        <?php
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
                        ".$valutignor."
                        LEFT JOIN user_crafts ON user_crafts.user_id = '$User->id' AND user_crafts.item_id = `prices`.`item_id`
                        AND user_crafts.isbest > 0
                        ORDER BY `isbased` DESC, ismybuy DESC, `prices`.`time` DESC
                        ");

                        UserPriceList2($qwe);
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
include_once 'pageb/footer.php';

addScript('js/setbuy.js');
if(!$User->ismobiledevice)
    addScript('js/tooltips.js');
addScript('js/user-prices.js');
?>
</body>
</html>
<?php
function UserPriceList2($qwe)
{
    global $puser_id, $User;

    if(!$qwe or !$qwe->num_rows){
        ?>
        <div>
            Похоже, что записей о ценах нет.<br>
            Их Можно сделать здесь:<br><br>
            <a href="catalog.php"><button class="def_button">Крафкулятор</button></a><br><br>
            <a href="user_customs.php"><button class="def_button">Настройки</button></a><br><br>
            <a href="packres.php"><button class="def_button">Ресурсы для паков</button></a>
        </div>
        <?php
        return false;
    }


    foreach($qwe as $q)
    {
        $q = (object) $q;
        ?><div><?php

        $chk = $isby = '';

        if($q->craftable)
            $isby = intval($q->isbest)+1;

        $basic_grade = $q->basic_grade ?? 1;

        if($puser_id == $User->id)
            PriceCell($q->item_id,$q->item_name,$q->icon,$basic_grade,$q->time,$isby);
        else
            PriceCell2($q->item_id,$q->auc_price,$q->item_name,$q->icon,$basic_grade,$q->time);
        ?>
        </div><?php
    }
}