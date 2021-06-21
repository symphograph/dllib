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

$ps = [];
if(!empty($_COOKIE['pack_settings']))
    $ps = unserialize($_COOKIE['pack_settings']);


function perselect($per)
{

    ?><select name="perc" class="perc" v-model="packForm.per"><?php

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
            <form method="post" action="packtable.php" name="packsettings" id="packsettings">
                <div class="navcustoms top">
                    <h2 class="p_title"><span>Паки <?php echo $aa_ver?> при</span><?php perselect(130)?><span>%</span></h2>
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
                            <input v-model="packForm.siol" type="checkbox" id="siol" name="siol" value="5">
                            <label class="navicon" for="siol" style="background-image: url('img/icons/50/icon_item_3368.png');"></label>
                            <div class="navname">Сиоль</div>
                        </div>
                    </div>
                </div>

                <div class="select_area">
                    <?php
                        selectRow($ps);
                        sortrow();
                    ?>
                </div>
            </form>
            <div class="clear"></div>
            <hr>
            <div id="tiptop" :style="{color: isred}"></div>

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
                    <?php packList()?>
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
?>

<?php
if(!$User->ismobiledevice)
    addScript('js/tooltips.js');


?>
</body>
</html>

<?php
function PackTypeButton(int $type, string $navname, string $img, array $ps)
{
    $chk = '';
    if(isset($ps['type'][$type])){
        $chk = ' checked ';
    }

    ?>
    <div class="nicon_out">
        <input type="checkbox" v-model="packForm.type" id="type_<?php echo $type ?>" name="type[<?php echo $type ?>]" <?php echo $chk ?> value="<?php echo $type ?>">
        <label class="navicon" for="type_<?php echo $type ?>" style="background-image: url(<?php echo $img ?>);"></label>
        <div class="navname"><?php echo $navname ?></div>
    </div>
    <?php
}

function selectRow($ps)
{
    ?>
    <div class="select_row">

        <div class="navcustoms">
            <div style="display: flex;
            justify-content: space-between;
            box-shadow: #5c7b2c 0px 0px 10px 0px; padding: 0.5em 1em; border-radius: 0.5em; width: 10em"

            >
                <div class="nicon_out">
                    <input v-model="packForm.side" type="radio" id="side_1" name="side" value="1">
                    <label class="navicon" for="side_1" style="background-image: url(img/westhouse.png);"></label>
                    <div class="navname">Запад</div>
                </div>

                <div class="nicon_out">
                    <input v-model="packForm.side" type="radio" id="side_3" name="side" value="3">
                    <label for="side_3" class="navicon" style="background-image: url(img/icons/50/icon_item_0013.png);"></label>
                    <div class="navname">Север</div>
                </div>

                <div class="nicon_out">
                    <input v-model="packForm.side" type="radio" id="side_2" name="side" value="2">
                    <label for="side_2" class="navicon" style="background-image: url(img/icons/50/icon_house_029.png);"></label>
                    <div class="navname">Восток</div>
                </div>


            </div>
            <?php
            $buttons = [
                1 => ['Обычные', 'img/icons/50/icon_item_0863.png'],
                8 => ['За ДЗ', 'img/icons/50/icon_item_0476.png'],
                2 => ['Компост', 'img/icons/50/icon_item_2504.png'],
                3 => ['С навеса', 'img/icons/50/icon_item_1336.png'],
                4 => ['Растворы', 'img/icons/50/icon_item_3869.png'],
                6 => ['Общинные', 'img/icons/50/icon_item_0864.png'],
                7 => ['Трофейные', 'img/icons/50/icon_item_4295.png']
            ];
            foreach ($buttons as $type => $v) {
                PackTypeButton($type, $v[0], $v[1], $ps);
            }
            ?>
        </div>
        <hr>
    </div>

    <?php
}

function sortrow(){
    ?>
    <div class="sortrow">

        <div class="freguency" title="Свежесть">
            <?php
            /*

            <select v-model="packForm.pack_age" name="pack_age" class="select_input" autocomplete="off" onchange="">
                <?php FreshTimeSelect() ?>
            </select>
            */
            ?>
            <select v-model="packForm.condition" name="condition" class="select_input" autocomplete="off" onchange="">
                <option value="0" selected>Зрелые</option>
                <option value="1">Протухшие</option>
            </select>

        </div>

        <div class="sortmenu">
            <div class="nicon_out" @click="sortParam='ZoneFrom'">
                <input type="radio" id="sort_1" name="sort" value="1">
                <label class="navicon" for="sort_1" style="background-image: url(img/packmaker.png?ver=2);"></label>
                <div class="navname">Откуда</div>
            </div>
            <div class="nicon_out" @click="sortParam='ZoneTo'">
                <input type="radio" id="sort_2" name="sort" value="2">
                <label class="navicon" for="sort_2" style="background-image: url(img/perdaru2.png);"></label>
                <div class="navname">Куда</div>
            </div>
            <div class="monsort">
                <div class="nicon_out" @click="sortParam='salary'">
                    <input type="radio" id="sort_3" name="sort" value="3">
                    <label class="navicon" for="sort_3" style="background-image: url(img/icons/50/quest/icon_item_quest023.png);"></label>
                    <div class="navname">Выручка</div>
                </div>
                <div class="profit_m">
                    <div class="nicon_out" @click="sortParam='profit'" data-tooltip="По прибыли">
                        <input type="radio" id="sort_0" name="sort" value="0" checked>
                        <label class="navicon" for="sort_0" style="background-image: url(img/icons/50/icon_item_3229.png);"></label>
                    </div>
                    <div class="nicon_out" @click="sortParam='profitor'" data-tooltip="По прибыли на 1 ор.<br>С учетом всех ОР на все этапы крафта.">
                        <input type="radio" id="sort_4" name="sort" value="4" checked>
                        <label class="navicon" for="sort_4" style="background-image: url(img/icons/50/2.png);"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}

function packList()
{
    ?>
    <div v-if="lost.length > 0">
        <br><b>Расчет не получился.</b>
        <br>В дочерних рецептах есть неизвестные цены.
        <br>Без них я не могу посчитать и сравнить.
    </div>


    <template v-if="uPrices.length > 0" >
        <div class="price_cell" v-for="(item,idx) in uPrices">
            <div class="price_row">
                <span class="comdate"></span>
            </div>
            <div class="price_row">

                <div class="itim" :id="'itim_'+item.item_id" :style="{backgroundImage: 'url(img/icons/50/'+item.icon+'.png)'}">
                    <div class="grade" :data-tooltip="item.item_name" style="background-image: url(/img/grade/icon_grade1.png)">
                    </div>
                </div>
                <div class="price_pharams">
                    <div><span class="item_name" :id="'itname_'+item.item_name">{{ item.item_name }}</span>
                        <form :id="'pr_'+item.item_id">
                            <div class="money_area_down">

                                <div class="money-line">
                                    <input
                                            v-model.number="uPrices[idx].gold"
                                            type="number"
                                            name="setgold"
                                            class="pr_inputs"
                                            :min=0
                                            max="999999999"
                                            :id="'gol_'+item.item_id"
                                            autocomplete="off"
                                            :style="{backgroundColor: item.color}"
                                    >
                                    <img src="img/gold.png" width="15" height="15" alt="g"/>        </div>

                                <div class="money-line">
                                    <input
                                            v-model.number="uPrices[idx].silver"
                                            type="number"
                                            name="setsilver"
                                            class="pr_inputs"
                                            value= ""
                                            min=0 max=99
                                            :id="'sil_'+item.item_id"
                                            autocomplete="off"
                                            :style="{backgroundColor: item.color}"
                                    >
                                    <img src="img/silver.png" width="15" height="15" alt="s"/>        </div>

                                <div class="money-line">
                                    <input
                                            v-model.number="uPrices[idx].bronze"
                                            type="number"
                                            name="setbronze"
                                            class="pr_inputs"
                                            value= "0"
                                            min=0 max=99
                                            :id="'bro_'+item.item_id"
                                            autocomplete="off"
                                            :style="{backgroundColor: item.color}"
                                    >
                                    <img src="img/bronze.png" width="15" height="15" alt="b"/>        </div>
                                <input type="hidden" :name="item.item_id" :value="item.item_id">
                                <input type="button" @click="cleanUPrice(idx)" :id="'prdel_'+item.item_id" name="del" class="small_del" value="del" data-tooltip="Очистить">
                            </div>
                            <input type="hidden" name="item_id" :value="item.item_id"/>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <button class="def_button" type="button" @click="sendPrices">Готово</button>
    </template>

    <template v-for="(row,idx) in sortedList">
        <div :class="(idx % 2 === 0 ? 'pack_row0' : 'pack_row1')">
            <div class="piconandpname">
                <div :id="row.Pack.item_id+'_'+row.Pack.zone_to"
                     class="pack_icon"
                     :style="{backgroundImage: 'url(img/icons/50/'+row.Pack.icon+'.png)'}"
                >
                    <div class="itdigp">{{row.Pack.Fresh.fresh_per}}%</div>
                </div>

                <div :id="row.Pack.item_id" class="pkmats_area">
                    <div class="pack_mats">

                        <div v-for="mat in row.Pack.bestCraft.mats" class="maticon" :style="{backgroundImage: 'url(img/icons/50/'+mat.icon+'.png)'}">
                            <div class="itdigit">{{ mat.mater_need }}</div>
                        </div>
                        <a :href="'catalog.php?item_id='+row.Pack.item_id">
                            <div class="maticon" style="background-image: url(img/icons/50/icon_item_4069.png)"></div>
                        </a>
                    </div>
                </div>

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
                <div class="pprice" :data-tooltip="row.Pack.PackPrice.salaryLetter">
                    <div v-html="valutImager(row.Pack.PackPrice.finalSalary,row.Pack.valuta_id)"></div>
                    <a :href="'/packpost.php?item_id='+row.Pack.item_id">
                        <img style="width: 15px" src="/img/icons/50/quest/icon_item_quest023.png"/>
                    </a>
                </div>
                <div class="pprice">
                    <div v-html="valutImager(row.Pack.PackPrice.profit)"></div>
                    <br>
                    <div class="profitLabor">
                        <div v-html="valutImager(row.Pack.PackPrice.profitOr)"></div>/
                        <img src="/img/icons/50/2.png" style="width: 15px" alt="imgor"/>
                    </div>
                </div>
            </div>
        </div>
        <hr class="phr">
    </template>
    <?php
}
?>