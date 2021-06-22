<?php
setcookie('path', 'packpost');
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/filefuncts.php';
$User = new User();
$User->check();
$user_id = $User->id;

$ver = random_str(8);

$pp = [];
$from_id = 100;
$item_id = 32094;
$to_id = 0;
$freshtime = 0;
$per = 130;
$psiol = false;



if(!empty($_COOKIE['packpost']))
{
    $pp = unserialize($_COOKIE['packpost']);
    //printr($pp);
    foreach ($pp as $k=> $v)
    {
        $packpost[$k] = intval($v);
    }
    extract($packpost);
}

if(isset($_GET['item_id']))
{
    $item_id = intval($_GET['item_id']);
    $from_ar = PackZoneFromId($item_id);
    if(!in_array($from_id ,$from_ar))
        $from_id = $from_ar[0];
    $cook_settings =
        [
            'per' => $per,
            'psiol' => $psiol,
            'from_id' => $from_id,
            'to_id' => $to_id,
            'freshtime' => $freshtime,
            'item_id' => $item_id
        ];
    $cooktime = time()+60*60*24*360;
    setcookie("packpost",serialize($cook_settings),$cooktime,'/');
    header("Location: packpost.php");
    exit();
}
$Pack = new Pack();
$Pack->getFromDB($item_id);

?>
<!doctype html>
<html lang="ru">
<head>

    <meta charset="utf-8">
    <meta name = "description" content = "Калькулятор паков Archeage." />
    <meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, калькулятор паков" />
    <meta name=“robots” content=“index”>
    <meta name="token" content="<?php echo SetToken()?>">
    <title>Пак-Инфо</title>
    <link href="css/default.css?ver=<?php echo md5_file('css/default.css')?>" rel="stylesheet">

    <link href="css/user_prices.css?ver=<?php echo md5_file('css/user_prices.css')?>" rel="stylesheet">


    <link href="css/packpost.css?ver=<?php echo md5_file('css/packpost.css')?>" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="js/packpost/packpost.js?ver=<?php echo md5_file('js/packpost/packpost.js')?>"></script>
    <script type="text/javascript" src="js/packpost/times.js?ver=<?php echo md5_file('js/packpost/times.js')?>"></script>
    <script type="text/javascript" src="js/packpost/prices.js?ver=<?php echo md5_file('js/packpost/prices.js')?>"></script>
    <script type="text/javascript" src="js/packpost/setbuy.js?ver=<?php echo md5_file('js/packpost/setbuy.js')?>"></script>
    <?php if(!$User->ismobiledevice)
    {
        ?><script type="text/javascript" src="js/tooltips.js?ver=<?php echo md5_file('js/tooltips.js')?>"></script><?php
    }
    ?>
</head>

<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php';


?>
<main>

<?php
/*
    $sql = "
    select
    packs.item_id,
    items.item_name
    FROM items
    INNER JOIN packs ON packs.item_id = items.item_id
    GROUP BY packs.item_id
    order by item_name
    ";
*/


$qwe = qwe("
select * from zones
where fresh_type
order by side, zone_name
");

    ?>
<div id="rent">
    <?php
    //printr($Pack);
    ?>
<h1>Пак-Инфо</h1>
    <div id="rent_in" class="rent_in">
        <div class="clear"></div>
        <div class="all_info_area">
            <div class="" id="all_info">
                <div class="contentblock">
                    <form id="form">
                        <label class="selab">
                            <img class="selicon" src="img/packmaker.png?ver=2" data-tooltip="Откуда">
                            <select id='zfrom' name="from_id" autocomplete="off">
                                <option value="100">Все локации</option>
                                <?php SelectOpts($qwe,'zone_id','zone_name',$from_id); ?>

                            </select>
                        </label>

                        <label class="selab">
                            <img class="selicon" src="/img/icons/50/icon_item_1338.png" data-tooltip="Что">
                            <select id="packselect" name="item_id" autocomplete="off"></select>
                        </label>

                        <label class="selab">
                            <img class="selicon" src="img/perdaru2.png" data-tooltip="Куда">
                            <select id="zto" name="to_id" autocomplete="off"></select>
                        </label>
                        <div class="selab2">
                            <div>
                            <label>
                                <img class="selicon" src="/img/icons/50/icon_item_1405.png" data-tooltip="Возраст пака">
                                <select id="freshlvl" name = "freshtime" autocomplete="off">
                                  <?php $Pack->fPerOptions();?>
                                </select>
                            </label>
                            </div>
                            <div class="perok"><label>
                                    <input type="number" id="per" name="per" value="<?php echo $per?>" min="1" max="130"/><span>%</span></label>
                                <div class="siol">
                                    <div class="nicon_out ">
                                        <input type="checkbox" id="siol" name="psiol" value="<?php echo $psiol?>" <?php if($psiol) echo ' checked ';?>>
                                        <label class="navicon" for="siol" style="background-image: url(img/icons/50/icon_item_3368.png);"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <br><br><hr><br>
                    <div id="bill"></div>

                    <?php TimeRouteForm(); ?>
                    <div id="table_div"></div>
                </div>
                <div class="contentblock">
                    <div class="PrColorsInfo">
                        <span style="background-color:#f35454">Чужая цена</span>
                        <span style="background-color:#dcde4f">Цена друга</span>
                        <span style="background-color:#79f148">Ваша цена</span>
                        <div class = "responses">
                            <div id = "responses"></div>
                        </div>
                    </div>

                    <div id="maters" class="prices" ></div>
                    <br>
                    <div class="buttons">
                        <a href="/packres.php"><button type="button" class="def_button">Ресурсы для паков</button></a>
                        <a href="/user_customs.php"><button type="button" class="def_button">Настройки</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

</main>

<?php
function TimeRouteForm()
{
        ?><hr><br>
        <div id="wayarea">
            <form id="timeform">
                <div class="transportbuffs">
                    <div>
                    <label>Болид
                        <input autocomplete="off" id="trans_2" required type="radio" name="transport" value="2">
                    </label>
                    <label>Трактор
                        <input autocomplete="off" id="trans_1" checked required type="radio" name="transport" value="1">
                    </label>
                </div>
                <div>
                    <label id="buff_1" data-tooltip="Экологичное топливо">
                        <img width="20px" src="../img/icons/50/quest/icon_item_quest142.png">
                        <input autocomplete="off"  type="checkbox" name="buff[1]" value="26548">
                    </label>
                    <label id="buff_2" style="display: none" data-tooltip="Органическая добавка">
                        <img  width="20px" src="../img/icons/50/icon_skill_catapult03.png">
                        <input autocomplete="off"  type="checkbox" name="buff[2]" value="42314">
                    </label>

                    <label id="buff_3" style="display: none" data-tooltip="Костюм гитанского торговца">
                        <img width="20px" src="../img/icons/50/costume_set/nu_f_sk_hippie001.png">
                        <input autocomplete="off"  type="checkbox" name="buff[3]" value="46705">
                    </label>
                </div>
                </div><br>
                <div class="selab2">
                    <div class="timerow">
                    <label data-tooltip="Время в пути">

                        <img class="selicon" height="100%" src="../img/icons/50/icon_item_0474.png">
                        <input name="time" id="time" required autocomplete="off"  type="number" max="60" min="1">мин.
                    </label>

                    <button type="button" id="sendtime" class="def_button">Добавить</button>
                    </div>
                </div>
            </form>
        </div>
        <?php
}
include_once 'pageb/footer.php'; ?>
</body>

<script type='text/javascript'>

//TODO Этот скрипт надо переделать и вынести в файл

    $(document).keypress(
        function(event){
            if (event.which === '13') {
                event.preventDefault();
                $('input').blur();
            }
    });

    $('#all_info').on('click','.itim',function(){
        var item_id = $(this).attr('id').slice(5);
        window.location.href = 'catalog.php?item_id=' + item_id;
    });

    window.onload = function() {

        ZtoLoad(<?php echo $from_id;?>,<?php echo $to_id;?>);
        PackLoad(<?php echo $from_id;?>,<?php echo $item_id;?>);


    };

<?php
if($cfg->myip)
{
?>
    $(document).ready( function() {
        $('#all_info').on('click', '#sendprice', function () {
            SendPrice();
        });
    });

    function SendPrice(){
        $.ajax
        ({
            url: "edit/packprice.php", // путь к ajax файлу
            type: "POST",      // тип запроса

            data: $('#editprice').serialize(),
            dataType: "html",
            cache: false,
            // Данные пришли
            success: function(data )
            {
                //$("#bill").html(data);
                console.log(data);
                InfoLoad();

            }
        });
    }
<?php
}
?>

</script>
</html>