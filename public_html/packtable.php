<?php
setcookie('path', 'packtable');
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User();
if (!$User->check()) {
    header("Refresh: 0");
    die();
}

$ver = random_str(8);
$aa_ver = '7.5';

function perselect($per)
{

    ?><select name="perc" class="perc" v-model="per"><?php

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



if($User->siol == 5)
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
<meta name="viewport" content="width=device-width, initial-scale=0.6">
<meta name="yandex-verification" content="<?php echo $cfg->yandex_key?>" />
<meta name = "description" content = "Таблица цен на паки в Archeage <?php echo $aa_ver?>"/>
  <meta name = "keywords" content = "товары фактории, паки <?php echo $aa_ver?>, archeage, архейдж, аркейдж, региональные товары, таблица паков, сколько стоят паки, цена паков" />
<title>Таблица цен на паки <?php echo $aa_ver?></title>
    <?php CssMeta(['default.css','packtable.css']); ?>
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
    <script src="https://unpkg.com/vue@next"></script>
</head>

<body>

<?php
include $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php';

?>
<main>
    <div class="input1" id="rent">
        <div class="menu_area">
            <form method="post" action="packtable.php" name="packsettings" id="packsettings" @change="test">
                <div class="navcustoms top">
                    <h2 class="p_title"><span>Паки <?php echo $aa_ver?> при</span><?php perselect(130)?><span>{{per}}%</span></h2>
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
                <div class="rot" style="background-image: url(img/perdaru.png)"></div>
            </div>
        </div>


        <div class="rent_in"><div class="all_info_area">
            <div class="all_info" id="all_info">
                <div id="input_data" class="packs_area_t1">


                        <div v-for="row in packData" class="pack_row0" >
                            <div class="piconandpname">
                                <div :id="row.Pack.item_id+'_'+row.Pack.zone_to"
                                     class="pack_icon"
                                     :style="{backgroundImage: 'url(img/icons/50/'+row.Pack.icon+'.png)'}"
                                >
                                    <div class="itdigp">{{row.Pack.Fresh.fresh_per}}%</div>
                                </div>

                                <div :id="row.Pack.item_id" class="pkmats_area"></div>

                                <div class="pack_name">
                                    <div class="pack_mname"><b>{{ row.Pack.item_name }}</b></div>

                                    <div class="znames">
                                        <div class="znamesrows">
                                            <div class="zname"></div>
                                            <div class="zname">{{ row.Pack.z_from_name }}</div>
                                            <div class="zname"></div>
                                        </div>
                                        <div class="znamesrows">
                                            <div class="zname2"></div>
                                            <div class="zname2"></div>
                                            <div class="zname2">{{ row.Pack.z_to_name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pprices">
                                <div class="pprice" data-tooltip="toolip">
                                    <div v-html="valutImager(row.Pack.PackPrice.finalSalary,row.Pack.valuta_id)">
                                    </div>
                                    <div>{{row.Pack.PackPrice.valuta_id}}</div>
                                    <a :href="'/packpost.php?item_id='+row.Pack.item_id">
                                        <img style="width: 15px" src="/img/icons/50/quest/icon_item_quest023.png"/>
                                    </a>
                                </div>
                                <div class="pprice">
                                    <template v-for="(d,idx) in row.Pack.PackPrice.profit+''">
                                        {{d}}<img v-if="(row.Pack.PackPrice.profit+'').length - idx === 5 && (row.Pack.PackPrice.profit+'').length >2" src="img/gold.png" style="width: 0.9em; height: 0.9em" alt="g"/>
                                        <img v-if="(row.Pack.PackPrice.profit+'').length - idx === 3 && (row.Pack.PackPrice.profit+'').length >2" src="img/silver.png" style="width: 0.9em; height: 0.9em" alt="s"/>
                                    </template>
                                    <img v-if="row.Pack.PackPrice.profit" src="img/bronze.png" style="width: 0.9em; height: 0.9em" alt="b"/>
                                    <br>
                                    {{ row.Pack.PackPrice.profitOr + '/'  }}
                                    <img src="/img/icons/50/2.png" style="width: 15px" alt="imgor"/>
                                </div>
                            </div>
                        </div><hr>

                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</main>

<?php
include_once 'pageb/footer.php';

addScript('js/packtable.js');
addScript('js/packtable/app.js');
if(!$User->ismobiledevice)
    addScript('js/tooltips.js');


?>
</body>
</html>