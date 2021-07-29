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

$ver = random_str(8);

$item_sgroup = $_GET['item_sgroup'] ?? 1;
$item_sgroup = intval($item_sgroup);
$item_sgroup = $item_sgroup ?? 1;

$item_id = $_GET['item_id'] ?? $_GET['query_id'] ?? 0;
$item_id = intval($item_id);
if($item_id  and !$User->isbot)
{
	$cooktime = time()+60*60*24*360;
	setcookie("item_id",$item_id,$cooktime);
	header("Location: items.php"); exit;
}

if(!empty($_GET['query']) and !$User->isbot)
	{header("Location: items.php"); exit;}

if(!empty($_COOKIE['item_id']))
{
	$item_id = intval($_COOKIE['item_id']);
}
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
</head>

<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php';?>

<?php /*?><div class="top"></div><?php */?>
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

        <div id="tiptop"></div>

        <div id="rent_in" class="rent_in">


            <div class="all_info_area" id="all_info_area">

                <div class="all_info" id="all_info">

                    <input type="hidden" id="current" name="current" value="<?php echo $item_id?>">
                    <input type="checkbox" id="nav-toggle" hidden="" checked>

                    <div v-if="Object.keys(catList).length" class="nav categories" id="categories">
                        <template v-for="cats in catList">
                            <br>
                            <details>
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
                            <div v-if="Object.keys(searched).length" class="items_list">
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


                            <div v-if="Object.keys(curItem).length" id="catalog_area">
                                <div class="item_descr_area">
                                    <div class="nicon">
                                        <div class="itim" style="background-image: url('png')">
                                            <div class="grade" style="background-image: url('/img/grade/icon_grade.png')"></div>
                                        </div>
                                        <div class="itemname">
                                            <div id="mitemname"><b>{{curItem.item_name}}</b></div>
                                            <div class="comdate"></div>
                                            <div class="mcateg">{{curItem.category}}</div>
                                        </div>
                                    </div>




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



    if(!$User->ismobiledevice)
        jsFile('tooltips.js');
?>

</body>
</html>