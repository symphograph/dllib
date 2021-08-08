<?php
setcookie('path', 'items');
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/filefuncts.php';
if(!$cfg->myip){
    die();
}
$User = new User();
if (!$User->check()) {
    header("Refresh: 0");
    die();
}



$item_sgroup = $_GET['item_sgroup'] ?? 1;
$item_sgroup = intval($item_sgroup);
$item_sgroup = $item_sgroup ?? 1;

$item_id = $_GET['item_id'] ?? $_GET['query_id'] ?? 0;
$item_id = intval($item_id);
if($item_id  and !$User->isbot)
{
	$cooktime = time()+60*60*24*360;
	setcookie("item_id",$item_id,$cooktime);
	header("Location: catalog.php"); exit;
}

if(!empty($_GET['query']) and !$User->isbot)
	{header("Location: catalog.php"); exit;}

if(!empty($_COOKIE['item_id']))
{
	$item_id = intval($_COOKIE['item_id']);
}

$modeTooltips =
    [
        '<b>C Миру по нитке:</b><br>Режим для новичка.<br>Предпочитает Ваши цены или более новые из доверенных.<br>Если их нет, ищет у других.<br>Спрашивает только, если никто и никогда не указывал цену.',
        '<b>Доверие:</b><br> Не видит ничьих цен, кроме Ваших и тех, кому Вы доверяете.<br>Предпочитает более новые.<br>ОР, РР, Честь и прочие субъективные предпочитает Ваши независимо от их новизны.',
        '<b>Хардкор:</b><br>Видит только Ваши цены.<br>В любой непонятной ситуации будет спрашивать.'
    ];
$modeTooltips = implode('<br><br><br>',$modeTooltips);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
      <meta name = "keywords" content = "Умный калькулятор, крафкулятор, archeage, архейдж, крафт" />
      <meta name=“robots” content=“index, nofollow”>
    <title>Предметы</title>
        <?php CssMeta(['default.css','items.css','right_nav.css','catalog_area.css']);?>
    <meta name="viewport" content="width=device-width, initial-scale=0.7">
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="text/javascript" src="https://unpkg.com/vue@next/dist/vue.global<?php echo $cfg->vueprod?>.js"></script>
    <script src="https://unpkg.com/http-vue-loader"></script>
</head>

<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php';?>

<?php /*?><div class="top"></div><?php */?>
<input type="hidden" id="server" value="<?php echo $User->server?>">
<input type="hidden" id="mode" value="<?php echo $User->mode?>">
<main>
    <div id="rent">

        <div class="navcustoms">
            <div v-for="sGroup in subCategs" @click="setSGroupId(sGroup.sgr_id)">
                <div :data-tooltip="sGroup.description" class="navicon" :style="{ backgroundImage: 'url(img/icons/50/'+sGroup.icon+')'}"></div>
                <div class="navname">{{sGroup.sgr_name}}</div>
            </div>
        </div>

        <div class="searcharea">
            <div class="search">
                <div id="snav"><div id="searchbtn"></div></div>
                <div>
                    <input type="search" id="search_box" name="squery" v-model="search" autocomplete="off" @keydown="navgatItem"/>
                    <div id="search_advice_wrapper"></div>
                </div>
            </div>
        </div>
        <div id="tiptop">{{ tiptop }}</div>

        <div id="tiptop"></div>

        <div id="rent_in" class="rent_in">

            <div class="all_info_area" id="all_info_area">

                <div class="all_info" id="all_info">

                    <input type="hidden" id="current" name="current" value="<?php echo $item_id?>">
                    <input type="checkbox" id="nav-toggle" hidden="" checked>

                    <div v-if="Object.keys(catList).length" class="nav categories" id="categories">
                        <template v-for="cats in catList">
                            <br>
                            <details open="open">
                                <summary>{{cats.name}}</summary>
                                <ul>
                                   <li v-for="cat in cats.categs">
                                       <label>
                                           {{cat.cat_name}}
                                           <input name="cat" type="radio" v-model="curCat" :value="cat.cat_id">
                                       </label>

                                   </li>
                                </ul>
                            </details>
                        </template>
                    </div>
                    <div>{{catList.length}}</div>



                    <div id="right">

                        <div v-if="itemId === 0" class="items_head" id="items_head">
                            <h3 id="categ_name">
                                <span v-if="subCategs[sGrId]">{{subCategs[sGrId].sgr_name}}</span>
                            </h3>
                            <div class="little_buttons">
                                <input type="radio" id="bar" name="view" value="0">
                                <label class="bar" for="bar"></label>
                                <input type="radio" id="list" name="view" value="1">
                                <label class="list" for="list"></label>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div id="items">
                            <div v-if="Object.keys(searched).length" class="items_list" >
                                <template v-for="(item,idx) in searched">
                                    <div class="item_row">
                                        <label class="nicon" :class="{ active: (idx === focusRow)}">

                                            <input type="radio" name="item" :value="item.item_id" v-model="itemId">
                                            <div class="itim" :style="{ backgroundImage: 'url(img/icons/50/'+item.icon+'.png)'}">
                                                <div
                                                        class="grade"
                                                        :style="{ backgroundImage: 'url(img/grade/icon_grade'+item.basic_grade+'.png)'}"
                                                        :data-tooltip="itemTooltip(item.item_name,item.craftable,item.personal)"
                                                >
                                                </div>
                                            </div>
                                            <div class="advice_variant">
                                                <div class="itemname">{{item.item_name}}</div>
                                                <div class="saw_notes" v-if="item.personal">Персональный</div>
                                                <div class="saw_notes" v-if="item.craftable">Крафтабельный</div>
                                            </div>
                                        </label>
                                    </div>
                                </template>
                            </div>

                            <div v-if="Object.keys(items).length" class="items_bar">
                                <template v-for="item in items">
                                    <label>
                                    <div class="itim" :style="{ backgroundImage: 'url(img/icons/50/'+item.icon+'.png)'}">
                                        <div
                                                class="grade"
                                                :style="{ backgroundImage: 'url(img/grade/icon_grade'+item.basic_grade+'.png)'}"
                                                :data-tooltip="itemTooltip(item.item_name,item.craftable,item.personal)"
                                        >
                                        </div>
                                    </div>
                                        <input type="radio" name="item" :value="item.item_id" v-model="itemId">
                                    </label>
                                </template>
                            </div>

                            <div v-if="itemId != 0 && !searched.length" id="catalog_area">
                                <div class="item_descr_area">
                                    {{curItem.item_id}}
                                    <div class="nicon" >
                                        <div class="itim" @click="reloadItem" :style="{ backgroundImage: 'url(img/icons/50/'+curItem.icon+'.png)'}">
                                            <div class="grade" :style="{ backgroundImage: 'url(img/grade/icon_grade'+curItem.basic_grade+'.png)'}"></div>
                                        </div>
                                        <div class="itemname">
                                            <div id="mitemname" @click="copy(curItem.item_name)"><b>{{ curItem.item_name }}</b></div>
                                            <div v-if="curItem.personal" class="comdate">Персональный</div>
                                            <div class="mcateg">{{curItem.category}}</div>
                                        </div>
                                    </div>
                                    <br><br>
                                    <details><summary>Описание</summary>
                                        <div class="item_descr" v-html="curItem.description"></div>
                                    </details><br>

                                    <a :href="'https://archeagecodex.com/ru/item/' + curItem.item_id" target="_blank">
                                        <div class="aacodex_logo" data-tooltip="Смотреть на archeagecodex"></div>
                                    </a>
                                    <br><hr><br>


                                    <div v-if="curItem.is_trade_npc">
                                        Продается у NPC:<br>
                                        <div v-if="curItem.valut_id === 500" v-html="valutImager(curItem.price_buy,500)"></div>
                                        <div v-else v-html="valutImager(curItem.price_buy,curItem.valut_id)"></div>
                                    </div>

                                    <template v-if="curItem.isGoldable">
                                        <div :style="{color: curItem.priceData.tcolor}">{{ curItem.priceData.date }} - {{curItem.priceData.how }}</div>
                                        <div class="money_area_down">
                                            <div class="money-line">
                                                <div class="money-line">
                                                    <input
                                                            inputmode="numeric"
                                                            v-model="pricez"
                                                            :style="{backgroundColor: curItem.priceData.color}"
                                                    >
                                                    <img src="img/bronze.png" alt="b"/>
                                                </div>
                                                <button
                                                        class="def_button"
                                                        style="width: 2em"
                                                        @click="setPrice(itemId,price)"
                                                >ok</button>
                                            </div>
                                            <input v-if="curItem.priceData.how === 'Ваша цена'"
                                                    type="button"
                                                    name="del"
                                                    class="small_del"
                                                    value="del"
                                                    data-tooltip="Удалить свою цену"
                                                   @click="delMainPrice"
                                            >
                                        </div>
                                        <div v-html="valutImager(price,500)"></div>

                                    </template>
                                    <br>
                                    <div style="height: 2em">
                                        <select v-model="server">
                                            <option v-for="(ser,sid) in servers" :value="sid">{{ ser }}</option>
                                        </select>
                                    </div><br>

                                    <div style="height: 2em">
                                        <select data-tooltip="<?php echo $modeTooltips?>"
                                                v-model="mode">
                                            <option v-for="(m,mid) in modes" :value="mid">{{ m }}</option>
                                        </select>
                                    </div><br>

                                    <a href="user_customs.php"><button class="def_button">Настройки</button></a><br><br>
                                    <a href="user_prices.php"><button class="def_button">Мои цены</button></a>
                                </div>
                                <div id="catalog_right">
                                    <p><b>{{curItem.ismat ? 'Используется в рецептах:' : 'Не используется в рецептах'}}</b></p>
                                    <div class="up_craft_area">
                                        <template v-if="curItem.ismat">
                                            <template v-for="item in curItem.craftResults">
                                                <label class="cubik" @click="pchId(item.item_id)">
                                                    <div class="itim" :style="{ backgroundImage: 'url(img/icons/50/'+item.icon+'.png)'}">
                                                        <div
                                                                class="grade"
                                                                :style="{ backgroundImage: 'url(img/grade/icon_grade'+item.basic_grade+'.png)'}"
                                                                :data-tooltip="itemTooltip(item.item_name,0,item.personal)"
                                                        >
                                                        </div>
                                                    </div>
                                                    <input class="hide" type="radio" name="item">
                                                </label>
                                            </template>
                                        </template>
                                    </div>

                                    <div v-if="itemId == 41488" class="craftCard">
                                        Дополнительно о векселях можно узнать на
                                        <a href="https://gisaa.ru/veksel"><b>gisaa.ru</b></a>
                                    </div>

                                    <template v-if="curItem.ValutInfo !== undefined">
                                        <br>
                                        <div class="craftCard">
                                            <h3>Передаваемые предметы за эту валюту:</h3>
                                            <div class="cyItems">
                                                <template v-for="cyItem in curItem.ValutInfo.transItems">
                                                    <label class="cubik" @click="pchId(cyItem.id)">
                                                        <div class="itim" :style="{ backgroundImage: 'url(img/icons/50/'+cyItem.icon+'.png)'}">
                                                            <div class="grade"
                                                                 :data-tooltip="cyItem.tooltip"
                                                                 :style="{ backgroundImage: 'url(img/grade/icon_grade'+cyItem.grade+'.png)'}">
                                                            </div>
                                                        </div>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <div v-if="curItem.craftable" class="dcraft_craft_area" id="dcraft_craft_area">
                                        <template v-if="Object.keys(curItem.lost).length">
                                            <div class="lost-area">
                                                <div>
                                                    <br><b>Расчет не получился.</b>
                                                    <br>В дочерних рецептах есть неизвестные цены.
                                                    <br>Без них я не могу посчитать и сравнить.
                                                </div>
                                                <div style="display: flex; flex-wrap: wrap; padding-bottom: 2em">
                                                    <div v-for="(los,idx) in curItem.lost" class="price_cell">
                                                        <div class="price_row" style="height: 1em">
                                                            <div v-if="curItem.lost[idx].auc_price"
                                                                 v-html="valutImager(curItem.lost[idx].auc_price,500)"
                                                            >
                                                            </div>
                                                        </div>
                                                        <div class="price_row">

                                                            <div class="itim"
                                                                 @click="pchId(los.item_id)"
                                                                 :style="{ backgroundImage: 'url(img/icons/50/'+los.icon+'.png)'}"
                                                            >
                                                                <div
                                                                        class="grade"
                                                                        :style="{ backgroundImage: 'url(img/grade/icon_grade'+los.basic_grade+'.png)'}"
                                                                        :data-tooltip="itemTooltip(los.item_name,0,los.personal)"
                                                                >
                                                                </div>
                                                            </div>
                                                            <div class="price_pharams">
                                                                <div>
                                                                    <span class="item_name" @click="copy(los.item_name)">{{ los.item_name }}</span>
                                                                    <div class="money_area_down">
                                                                        <div class="money-line">
                                                                            <div class="money-line">
                                                                                <input
                                                                                        inputmode="numeric"
                                                                                        :value="curItem.lost[idx].auc_price"
                                                                                        @input="lostPrice(idx,$event.target.value)"
                                                                                >
                                                                                <img src="img/bronze.png" alt="b"/>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="price_cell">
                                                        <div class="price_row">
                                                            <button
                                                                    class="def_button"
                                                                    @click="setLostPrices"
                                                            >Сохранить</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <template v-else>
                                            <div id="isbuy" v-if="!curItem.personal">
                                                <div class="isby">
                                                    <input type="radio"  id="ibcr" name="isbuy" value="1" v-model="isbuy"/>
                                                    <label class="navicon"
                                                           for="ibcr"
                                                           data-tooltip="Использовать для расчетов себестоимость<br>Считать по крафту"
                                                           style="background-image: url(../img/profs/Обработка_камня.png);">
                                                    </label>
                                                    <span>Крафтить</span>

                                                </div>
                                                <div>x
                                                    <input
                                                            inputmode="numeric"
                                                            name="u_amount"
                                                            id="u_amount"
                                                            min="1"
                                                            v-model.number="Uamount"
                                                            autocomplete="off"
                                                    >
                                                </div>
                                                <div class="isby">
                                                    <input type="radio" id="isb" name="isbuy" value="3" v-model="isbuy"/>
                                                    <label class="navicon"
                                                           for="isb"
                                                           data-tooltip="Использовать мою цену для расчетов"
                                                           style="background-image: url(../img/perdaru2.png);">
                                                    </label>
                                                    <span>Покупать</span>
                                                </div>
                                            </div>

                                            <div class="prefType"><b>{{prefText[curItem.bestCraft.isbest]}}</b></div>

                                            <div v-if="Object.keys(curItem.crafts.best).length">

                                                <craft-info :arr-props="curItem.crafts.best"
                                                            :uamount="Uamount"
                                                            :icon="curItem.icon"
                                                            :grade="curItem.basic_grade"
                                                            :pref-text="'Сбросить'"
                                                            @chid="pchId"
                                                            @reset="resetBest"
                                                ></craft-info>
                                            </div>

                                            <details v-if="curItem.allMats.length">
                                                <summary>Все материалы для {{ Uamount  *  curItem.crafts.best.result_amount }} шт</summary>
                                                <div class="craftCard">
                                                    <div class="all_res_area">
                                                        <template v-for="mat in curItem.allMats">
                                                            <label class="cubik" @click="pchId(mat.id)">
                                                                <div class="itim" :style="{ backgroundImage: 'url(img/icons/50/'+mat.icon+'.png)'}">
                                                                    <div class="grade"
                                                                         :data-tooltip="mat.tooltip"
                                                                         :style="{ backgroundImage: 'url(img/grade/icon_grade'+mat.grade+'.png)'}">
                                                                        <div class="matneed">{{ round(mat.value * Uamount  *  curItem.crafts.best.result_amount) }}</div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </template>
                                                    </div>
                                                </div>
                                            </details>

                                            <template v-if="curItem.allTrash.length">
                                                <br><hr><br>
                                                <details>
                                                    <summary>Полученные отходы с {{ Uamount  *  curItem.crafts.best.result_amount }} шт</summary>
                                                    <div class="craftCard">
                                                        <div class="all_res_area">
                                                            <template v-for="mat in curItem.allTrash">
                                                                <label class="cubik" @click="pchId(mat.id)">
                                                                    <div class="itim" :style="{ backgroundImage: 'url(img/icons/50/'+mat.icon+'.png)'}">
                                                                        <div class="grade"
                                                                             :data-tooltip="mat.tooltip"
                                                                             :style="{ backgroundImage: 'url(img/grade/icon_grade'+mat.grade+'.png)'}">
                                                                            <div class="matneed">{{ round(mat.value * Uamount  *  curItem.crafts.best.result_amount)  }}</div>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </details>
                                            </template>



                                            <template v-if="curItem.crafts.other.length">
                                                <br><hr><br>
                                                <details><summary>Другие рецепты</summary>
                                                    <br>
                                                        <transition-group name="flip-list" tag="ul">
                                                        <div v-for="craft in sortedCrafts" :key="craft.craft_id">

                                                            <craft-info :arr-props="craft"
                                                                        :uamount="Uamount"
                                                                        :icon="curItem.icon"
                                                                        :grade="curItem.basic_grade"
                                                                        :pref-text="'Предпочитать этот'"
                                                                        @chid="pchId"
                                                                        @setbest="setBest"
                                                            ></craft-info>

                                                        </div>
                                                    </transition-group>
                                                </details>
                                            </template>
                                        </template>


                                    </div>
                                </div>
                                <br>
                                <div v-if="curItem.ValutInfo !== undefined" class="craftCard">
                                    <h3>Конвертация в золото</h3>
                                    <br>
                                    <div class="valut_median">
                                        Медиана:
                                        <img :src="'img/icons/50/'+curItem.ValutInfo.icon+'.png'"
                                             class="smallIcon"
                                             alt="Ремесленная репутация">
                                        =
                                        <span v-html="valutImager(curItem.ValutInfo.median,500)"></span>
                                    </div>
                                    <br>
                                    <div id="medianList">
                                        <template v-for="cyItem in curItem.ValutInfo.monetisationData" :key="cyItem.item_id">
                                            <div class="price_cell">
                                                <div class="price_row">
                                                    <label class="cubik" @click="pchId(cyItem.id)">
                                                        <div class="itim" :style="{ backgroundImage: 'url(img/icons/50/'+cyItem.icon+'.png)'}">
                                                            <div class="grade"
                                                                 :data-tooltip="cyItem.tooltip"
                                                                 :style="{ backgroundImage: 'url(img/grade/icon_grade'+cyItem.grade+'.png)'}">
                                                            </div>
                                                        </div>
                                                    </label>
                                                    <div class="price_pharams">
                                                        <div>
                                                            <div>{{cyItem.tooltip}}</div>
                                                            <div>
                                                                <img :src="'img/icons/50/'+curItem.ValutInfo.icon+'.png'"
                                                                     class="smallIcon"
                                                                     alt="Ремесленная репутация">
                                                                =
                                                                <span v-html="valutImager(cyItem.value,500)"></span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div style="width: 100%">

                                </div>

                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
    include_once 'pageb/footer.php';
    //jsFile('Catalog.js');
    //jsFile('search.js');

jsFile('items/items.js');
//jsFile('items/components/price-form2.vue');
?>



<?php
    if(!$User->ismobiledevice)
        jsFile('tooltips.js');
?>

</body>
</html>