<?php
if (!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/config.php';
}
if (!$cfg->myip) exit();

?>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Редактор рецепта</title>
    <?php CssMeta(['default.css', 'items.css', 'catalog_area.css', 'right_nav.css','recedit.css']); ?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
</head>

<body>
<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/../includs/header.php';
?>
<main><?php
    if (!empty($_GET['off'])) {
        $craft_id = intval($_GET['off']);
        $url_from = $_SERVER['HTTP_REFERER'];
        qwe("UPDATE `crafts` SET `on_off` = 0 WHERE `craft_id` = '$craft_id'");
        require_once $_SERVER['DOCUMENT_ROOT'] . '/../functions/craftable.php';
        echo '<meta http-equiv="refresh" content="0; url=' . $url_from . '">';
        exit();

    }

    if (empty($_GET['query']) and empty($_GET['addrec']))
        exit();


    if (!empty($_GET['addrec'])) {
        $item_id = intval($_GET['addrec']);
        $query = qwe("SELECT * FROM `crafts` where `my_craft` = 1 ORDER BY `craft_id` DESC LIMIT 1");
        foreach ($query as $v) {
            $new_craft_id = $v['craft_id'] + 1;
        }
        qwe("
         INSERT INTO `crafts` 
         (`craft_id`,
         `result_item_id`, 
         `result_item_name`, 
         `my_craft`) 
         VALUES 
         ('$new_craft_id',
         '$item_id', 
         (SELECT DISTINCT `item_name` AS `col1` FROM `items` WHERE `item_id`='$item_id'), '1')
         ");

        qwe("UPDATE `items` set `craftable`=1 where `item_id`='$item_id'");
        $q_craft = qwe("SELECT `craft_id` FROM `crafts` where `result_item_id` = '$item_id' order by `craft_id` DESC LIMIT 1");
        $arrcraft = mysqli_fetch_assoc($q_craft);
        $craft_id = $arrcraft['craft_id'];
        echo '<meta http-equiv="refresh" content="0; url=recedit.php?query=' . $craft_id . '"">';
        exit();
    }

    if (!empty($_GET['query'])) {
        $craft_id = $_GET['query'] ?? 0;
        $craft_id = intval($craft_id);
        $Craft = new Craft($craft_id);
    };


    ?>
    <div class="top"></div>
    <div id="rent">
        <div id="rent_in" class="rent_in">
            <form id="recdata" action="" method="POST">
                <div class="line">

                    <?php $icon = ItemAny($Craft->result_item_id, 'icon')[$Craft->result_item_id] ?? '';?>

                    <div class="top_itimset"
                         style="width: 40px; height: 40px; background-image: url(/img/icons/50/<?php echo $icon ?>.png)">
                    </div>
                    <?php echo $Craft->result_item_name; ?>

                </div>
                <br><br>
                <hr style="width: 320px">
                <div class="confirm">
                    <br>Результат:<br>
                        <input type="text" name="result_item_name" value="<?php
                        echo $Craft->result_item_name;
                        ?>" autocomplete="off">
                    <br>
                    <br>Количество:<br>
                        <input type="number" name="result_amount" value="<?php
                        echo $Craft->result_amount;
                        ?>" autocomplete="off">
                    <br>

                    <br><label for="price_type">Профессия</label><br>
                    <select name="prof_id" id="prof" autocomplete="off">
                        <?php
                        $query = qwe("SELECT * FROM `profs` ORDER BY `profession`");
                        SelectOpts($query, 'prof_id', 'profession', $Craft->prof_id, 'Не выбрана');
                        ?>
                    </select><br>
                    <br>Требует прокачки:<br>
                    <input type="number" name="prof_need" value="<?php
                    echo $Craft->prof_need;
                    ?>" autocomplete="off">
                    <br>

                    <br><label for="dood_id">Приспособление</label><br>
                    <select name="dood_id" id="dood_id" autocomplete="off">
                        <?php
                        $query = qwe("SELECT * FROM `doods` ORDER BY `dood_name`");
                        SelectOpts($query, 'dood_id', 'dood_name', $Craft->dood_id, 'Не выбрана');
                        ?>
                    </select><br>
                    <p>Имя рецепта:<Br>
                        <input type="text" name="rec_name" value="<?php
                        echo $Craft->rec_name;
                        ?>" autocomplete="off"></p>
                    <p>Очков работы:<Br>
                        <input type="number" name="labor_need" value="<?php
                        echo $Craft->labor_need;
                        ?>" autocomplete="off"></p>
                    <p>Длительность (мин):<Br>
                        <input type="number" name="mins" value="<?php
                        echo $Craft->mins;
                        ?>" autocomplete="off"></p>
                </div>
                <br>
                <input type="hidden" name="craft_id" value="<?php
                echo $Craft->craft_id;
                ?>" autocomplete="off">
                <input type="hidden" name="result_item_id" value="<?php
                echo $Craft->result_item_id;
                ?>" autocomplete="off">
                <hr width="320">
                <br>


                <div class="rent_count">
                    <div class="rent_count_in">
                        <?php
                        $i = 0;

                        foreach ($Craft->mats as $v) {
                            edMats($v);
                            $i++;
                        }

                        for ($c = 0; $c < 12 - $i; $c++) {
                            addvansedMat();
                        }
                        ?>

                        <br>
                    </div>
                    <br><br><br>
                     <div class="ok-cancel">
                        <button type="button" class="def_button" value="ok" id="ok">ОК</button>
                         <br><br>
                    </div>
                </div>
            </form>
            <div class="responses"></div>
        </div>
    </div>
</main>
<?php
include_once 'pageb/footer.php';
jsFile('recedit.js');
?>

</body>
</html>

<?php

function edMats($v)
{
    $Mat = new Mat();
    $Mat->reConstruct($v);
    ?>
    <div class="itemline">
        <div class="itemprompt" data-title="<?php echo $Mat->item_name . ' x ' . $Mat->mater_need ?>">
            <div class="itim"
                 style="background-image: url(../img/icons/50/<?php echo $Mat->icon ?>.png)">
                <div class="itdigit">
                    <input style="
                                width: 35px;
                                background-color: transparent;
                                border-color: transparent;
                                color: white;
                                text-shadow: -1px -1px 5px #010101;
                                text-align: right;"
                           autocomplete="off"
                           type="text"
                           name="mater_need[<?php echo $Mat->item_id ?>]"
                           value="<?php echo $Mat->mater_need ?>"
                    >

                    <input type="checkbox" name="del[]" value="<?php echo $Mat->item_id ?>"
                           autocomplete="off"
                           style="background-color: transparent; border-color: transparent;"
                    >
                </div>
            </div>
        </div>
    </div>
    <?php
}

function addvansedMat()
{
    ?>
    <div class="itemline">
        <div class="itemprompt" data-title="Добавить">
            <div class="itim">
                <div class="itdigit">
                    <input style="width: 35px;
                                                            background-color: transparent;
                                                            border-color: transparent;
                                                            color: white;
                                                            text-shadow: -1px -1px 5px #010101;
                                                            text-align: right;"
                           autocomplete="off"
                           name="newmat[]"
                           value="">
                    <input style="width: 35px;
                                                            background-color: transparent;
                                                            border-color: transparent;
                                                            color: white;
                                                            text-shadow: -1px -1px 5px #010101;
                                                            text-align: right;" name="newmatneed[]"
                           autocomplete="off" value="">
                </div>
            </div>
        </div>
    </div>
    <?php
}

