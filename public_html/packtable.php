<?php
setcookie('path', 'packtable');
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';

$userinfo_arr = UserInfo();
if (!$userinfo_arr) {
    header("Refresh: 0");
    die();
}

$ver = random_str(8);
$aa_ver = '7.0';

function perselect($per)
{

    ?><select name="perc" class="perc"><?php

    $f = 135; $sel_per = '';
    for ($i = 1; $i <= 17; $i++)
    {
        $f = $f-5;
        if($f==$per) $sel_per = 'selected';
        echo '<option value="'.$f.'" '.$sel_per.'>'.$f.'</option>';
        $sel_per = '';
    };

    ?></select><?php
}
$ssiol = 5;
$x_siol = '<b>X</b>';
$siol_on_off = 0;
$siol_title = 'Включить Сиоль';



if($siol == 5)
{
    $ssiol = 0;
    $x_siol = ' ';
    $siol_on_off = 1;
    $siol_title = 'Отключить Сиоль';
}
?>


<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="yandex-verification" content="<?php echo $yandex_key?>" />
<meta name = "description" content = "Таблица цен на паки в Archeage <?php echo $aa_ver?>"/>
  <meta name = "keywords" content = "товары фактории, паки <?php echo $aa_ver?>, archeage, архейдж, аркейдж, региональные товары, таблица паков, сколько стоят паки, цена паков" />
<title>Таблица цен на паки <?php echo $aa_ver?></title>

<link href="css/default.css?ver=<?php echo md5_file('css/default.css')?>" rel="stylesheet">
<link href="css/packtable.css?ver=<?php echo md5_file('css/packtable.css')?>" rel="stylesheet">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
<?php if(!$ismobiledevice)
{
	?><script type="text/javascript" src="js/tooltips.js?ver=<?php echo md5_file('js/tooltips.js')?>"></script><?php
}
?>
</head>

<body>

<?php
include $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php';

?>
<main>
    <div class="input1" id="rent">
        <div class="menu_area">
            <form method="post" action="packtable.php" name="packsettings" id="packsettings">
                <div class="navcustoms top">
                    <h2 class="p_title"><span>Паки <?php echo $aa_ver?> при</span><?php perselect(130);?><span>%</span></h2>
                    <div class="siol">
                        <div class="nicon_out ">
                            <a href="/user_customs.php">
                            <label class="navicon" for="usercustoms" style="background-image: url('img/icons/50/icon_item_0060.png');"></label>
                            </a>
                            <div class="navname">Настройки</div>
                        </div>
                    </div>

                    <div class="siol">
                        <div class="nicon_out ">
                            <a href="/packres.php">
                            <label class="navicon" style="background-image: url('img/icons/50/icon_item_1314.png');"></label>
                            </a>
                            <div class="navname">Ресурсы для паков</div>
                        </div>
                    </div>
                    <div class="siol">
                        <div class="nicon_out ">
                            <input type="checkbox" id="siol" name="siol" value="5">
                            <label class="navicon" for="siol" style="background-image: url('img/icons/50/icon_item_3368.png');"></label>
                            <div class="navname">Сиоль</div>
                        </div>
                    </div>
                </div>

                <div class="select_area">
                    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/packs_menu_t1.php';?>
                </div>

                <div class="clear"></div>
                <hr>
                <div id="tiptop"></div>
            </form>
        </div>


        <div class="jdunarea">
            <div id="jdun" class="jdun">
                <h2>Считаю...</h2>
                <div class="rot" style="background-image: url(../img/perdaru.png)"></div>
            </div>
        </div>


        <div class="rent_in"><div class="all_info_area">
            <div class="all_info" id="all_info">
                <div id="input_data"></div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</main>

<?php include_once 'pageb/footer.php'; ?>

<script type="text/javascript" src="js/packtable.js?ver=<?php echo md5_file('js/packtable.js')?>"></script>
</body>
</html>