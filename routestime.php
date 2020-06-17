<?php 
require_once 'includs/usercheck.php';
setcookie('path', 'routestime');

$userinfo_arr = UserInfo();
if (!$userinfo_arr){
    header("Refresh: 0");
    die();
}

extract($userinfo_arr);

$ver = random_str(8);

?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
  <meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, крафт" />
  <meta name=“robots” content=“index, nofollow”>
<title>Время в пути</title>
<link href="css/default.css?ver=<?php echo md5_file('css/default.css')?>" rel="stylesheet">
<link href="css/user_prices.css?ver=<?php echo md5_file('css/user_prices.css')?>" rel="stylesheet">
<link href="css/tradetime.css?ver=<?php echo md5_file('css/tradetime.css')?>" rel="stylesheet">

<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php if(!$ismobiledevice)
{
	?><script type="text/javascript" src="js/tooltips.js?ver=<?php echo md5_file('js/tooltips.js')?>"></script><?php
}
?>
</head>

<body>

<?php include_once 'includs/header.php';
//$puser_nick = AnyById($puser_id,'mailusers','user_nick')[$puser_id];
	  
?>
<main>

<?php
$sql = "
SELECT 
zones.side,
zones.zone_id as from_id,
zones.zone_name as zfrom,
pack_prices.zone_to as to_id,
(SELECT zone_name FROM zones WHERE zone_id = pack_prices.zone_to) as zto
FROM pack_prices
INNER JOIN zones ON pack_prices.zone_id = zones.zone_id
AND zones.zone_id < 30
GROUP BY pack_prices.zone_id, pack_prices.zone_to
ORDER BY side, zfrom
";
$qwe = qwe($sql);

?>
<div id="rent">

    <div class="navcustoms">
        <h2>Время перевозки</h2>
        <br>
        <hr>
        <div class = "responses">
            <div id = "responses"></div>
        </div>

        <div class="timetable">
            <?php TimeRouteForm($qwe); ?>
        </div>

    </div>

    <div id="rent_in" class="rent_in">
        <div class="clear"></div>
        <div class="all_info_area">
            <div class="all_info" id="all_info">
                <div id="items">
                    <div class="prices">
                    <?php
                    function TimeRouteForm($qwe)
                    {
                        $zold = '';
                        //echo '<div>';
                        foreach ($qwe as $q)
                        {
                            extract($q);

                            ?>
                            <div class="wayarea" id="wayarea">
                                <form id="form">
                                <div class="inputs">
                                <div>
                                <label>
                                    <img height="100%" src="../img/packmaker.png?ver=2">
                                    <?php SelectZone(0)?>
                                </label>
                                <br>
                                <label>
                                    <img height="100%"  src="../img/perdaru2.png">
                                    <div id="zonetoselect" style="width: 100%"><?php SelectZone(1)?></div>
                                </label>
                                </div>
                                </div>
                                <div class="inputs">
                                    <label data-tooltip="Костюм гитанского торговца">
                                        <img width="20px" src="../img/icons/50/costume_set/nu_f_sk_hippie001.png">
                                        <input autocomplete="off"  type="checkbox" name="buff[3]" value="46705">
                                    </label>
                                    <label data-tooltip="Органическая добавка">
                                        <img width="20px" src="../img/icons/50/icon_skill_catapult03.png">
                                        <input autocomplete="off"  type="checkbox" name="buff[2]" value="42314">
                                    </label>
                                    <label id="buff_1" style="display: none" data-tooltip="Экологичное топливо">
                                        <img width="20px" src="../img/icons/50/quest/icon_item_quest142.png">
                                        <input autocomplete="off"  type="checkbox" name="buff[1]" value="26548">
                                    </label>
                                    <label>Трактор
                                        <input autocomplete="off" id="trans_1" checked required type="radio" name="transport" value="1">
                                    </label>
                                    <label>Болид
                                        <input autocomplete="off" id="trans_2" required type="radio" name="transport" value="2">
                                    </label>
                                </div><br><hr><br>
                                <div class="inputs">
                                    <label data-tooltip="Время в пути">
                                        <img height="100%" src="../img/icons/50/icon_item_0474.png">
                                        <input name="time" id="time" required autocomplete="off"  type="number" max="60" min="1">мин.
                                    </label>

                                    <button type="button" id="sendtime" class="def_button">Добавить</button>

                                </div>
                                </form>
                            </div>
                            <?php
                            break;
                        }

                    }
                    ?>
                    </div>
                    <div id="results">
                        <div id="table_div"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</main>

<?php

include_once 'pageb/footer.php'; ?>
</body>
<?php
/*
function RowsForGoogleScript($qwe,$cols = [])
{
    if(!$qwe or $qwe->num_rows ==0)
        return [];


    $vvv = [];
    foreach ($qwe as $q)
    {
        $vv = [];
        foreach ($q as $k =>$v)
        {
            //echo $k.'<br>';
            $vv[] = "'".$v."'";
            $cols[$k] = "'".$k."'";
        }
        $vvv[] = "[".implode(',',$vv)."]";
    }

    $cols = implode(',',$cols);
    var_dump($cols);
    $rows = implode(',',$vvv);
    return $rows;
}
//$rows = RowsForGoogleScript($qwe);
*/
?>
<script type='text/javascript'>

window.onload = function() {

    istractor();
    LoadTimes();
};
function istractor() {
    if($('#trans_1').prop("checked"))
    {
       // console.log("yes");
        $("#buff_1").show(0);
    }else
    {
       // console.log('no');

        $("#buff_1").hide(0);
        $("#buff_1").find("input[type=checkbox]").prop("checked", false);
    }

}

$(document).ready( function(){
    $('#form').on('change','input[type=radio]',function()
    {
        istractor();
    });
});
$('#form').on('input',function()
{
    LoadTimes();
});
function LoadTimes(){
    $.ajax
    ({
        url: "hendlers/listusertime.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data: $('#form').serialize(),


        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $("#table_div").html(data );
        }
    });
}
$('#form').on('change','#from_id',function()
{
    var from_id = $("#from_id").val();
    //var item_id = $(".nicon div[class=itim]","#all_info").attr("id").slice(5);
    SelectLoad(from_id);
    LoadTimes();
});
function SelectLoad(from_id) {

    $.ajax
    ({
        url: "hendlers/selectzonesto.php", // путь к ajax файлу
        type: "POST",      // тип запроса

        data:
            {
                from_id: from_id
            },

        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data )
        {
            $("#zonetoselect").html(data );
            $("#zonetoselect").show();

        }
    });
}
$('#form').on('click','#sendtime',function()
{

   SendTime();

});

function SendTime() {
    var form = $('#form');

    if(!$("#time").val() > 0)
    {
       // console.log("is 0");
        return false;
    };
    $.ajax
    ({
        url: "hendlers/tradetimeadd.php", // путь к ajax файлу
        type: "POST",      // тип запроса




        data: form.serialize(),

        dataType: "html",
        cache: false,
        // Данные пришли
        success: function(data)
        {
           $('#responses').html(data);
            LoadTimes();
        }
    });
}

<?php
/*
google.charts.load('current', {'packages':['table']});
google.charts.setOnLoadCallback(drawTable);

function drawTable() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Name');
    data.addColumn('string', 'Salary');
    data.addColumn('string', 'ffff1');
    data.addColumn('string', 'ffff2');
    data.addColumn('string', 'ffff3');
    data.addColumn('string', 'ffff4');
    data.addColumn('string', 'ffff5');
    data.addColumn('string', 'ffff6');
    data.addColumn('string', 'ffff7');
    data.addRows([
        <?php echo $rows;?>
    ]);

    var table = new google.visualization.Table(document.getElementById('table_div'));

    table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
}
*/
?>
</script>
</html>