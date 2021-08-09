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
    <script type="text/javascript" src="https://unpkg.com/vue@next/dist/vue.global<?php echo $cfg->vueprod?>.js"></script>
</head>

<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php'; ?>
<input type="hidden" id="userId" value="<?php echo $User->id?>">
<input type="hidden" id="pUserId" value="<?php echo $Puser->id?>">
<input type="hidden" id="server" value="<?php echo $User->server?>">
<main>
    <div id="rent">
        <div class="navcustoms">
            <h2 v-if="uself">{{ 'Ваши цены' }}</h2>
            <h2 v-else>{{ 'Цены пользователя ' + puserData.user_nick }}</h2>
            <br>
            <div class="prmenu">
                <div style="height: 2em">
                    <select v-model="server">
                        <option v-for="(ser,sid) in servers" :value="sid">{{ ser }}</option>
                    </select>
                </div>
                <?php
                $intimStr = implode(',',IntimItems());
                if($puser_id != $User->id)
                {
                    $valutignor = 'AND `prices`.`item_id` NOT in ('.$intimStr.')';
                    $chks = ['','checked'];
                    $chk = intval($User->isFolow($puser_id));
                    ?>
                    <label for="folw" data-tooltip="Если цена этого пользователя новее Вашей, она будет использована в расчетах.">
                    Доверять этим ценам
                        <input type="checkbox" v-model="isFolow" name="sfolow" id="folw" :value="pUserId">
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

                <select id="sort" v-model.number="sortParam">
                    <option value="date">По дате</option>
                    <option value="name">По имени</option>
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
                    <div v-if="prices.length" id="items">
                        <div class="prices">
                            <template v-for="(price,idx) in sortedPrices" :key="price.item_id">
                                <div class="parea">
                                    <div class="price_cell">
                                        <div class="price_row">
                                            <span class="comdate">{{ price.date }}</span>
                                            <label v-if="uself && price.havingChekbox" class="comdate">
                                                <input type="checkbox" :value="price.item_id" v-model="checked">
                                                Покупаемый
                                            </label>

                                        </div>
                                        <div class="price_row">
                                            <div class="itim"
                                                 @click="goToItem(price.item_id)"
                                                 :style="{ backgroundImage: 'url(/img/icons/50/'+ price.icon + '.png)' }">
                                                <div class="grade"
                                                     :style="{ backgroundImage: 'url(/img/grade/icon_grade'+ price.grade + '.png)' }">

                                                </div>
                                            </div>
                                            <div class="price_pharams">
                                                <div @click="copy(price.item_name)">
                                                    <span class="item_name">{{ price.item_name }}</span>
                                                </div>
                                                <div v-if="uself" class="money-line">
                                                    <input inputmode="numeric"
                                                           :value="priceStringer(prices[idx].price)"
                                                           @input="setPrice(idx,$event.target.value)"
                                                           class="pinput">
                                                    <img src="img/bronze.png" class="smallIcon" alt="b">
                                                </div>
                                                <div v-else v-html="valutImager(prices[idx].price)"></div>
                                            </div>
                                            <input v-if="uself"
                                                   @click="delPrice(price.item_id)"
                                                   type="button"
                                                   name="del"
                                                   class="small_del"
                                                   value="del"
                                                   data-tooltip="Удалить свою цену"
                                                   style="display: block;">
                                        </div>


                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>
<?php
include_once 'pageb/footer.php';


if(!$User->ismobiledevice)
    addScript('js/tooltips.js');
addScript('js/user_prices/prices.js');
?>
</body>
</html>
<?php
