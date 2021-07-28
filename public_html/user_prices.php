<?php
setcookie('path', 'user_prices');
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
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
    <?php CssMeta(['default.css','user_prices.css']);?>
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
                <select id="sort">
                    <option value="1">По дате</option>
                    <option value="2">По имени</option>
                </select>
                <input type="hidden" id="puser_id" value="<?php echo $puser_id?>">
            </div>
            <br><hr>
            <div class = "responses"><div id = "responses"></div></div>
        </div>

        <div id="rent_in" class="rent_in">
            <div class="clear"></div>

            <div class="all_info_area">
                <div class="all_info" id="all_info">

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
