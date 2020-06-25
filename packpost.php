<?php
require_once 'includs/usercheck.php';
setcookie('path', 'packpost');
//if(!$myip) die();
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
    <meta name=“robots” content=“index”>
    <meta name="token" content="<?php echo SetToken()?>">
    <title>Пакулятор</title>
    <link href="css/default.css?ver=<?php echo md5_file('css/default.css')?>" rel="stylesheet">
    <link href="css/user_prices.css?ver=<?php echo md5_file('css/user_prices.css')?>" rel="stylesheet">

    <link href="css/packpost.css?ver=<?php echo md5_file('css/packpost.css')?>" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
    <!--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
    <?php if(!$ismobiledevice)
    {
        ?><script type="text/javascript" src="js/tooltips.js?ver=<?php echo md5_file('js/tooltips.js')?>"></script><?php
    }
    ?>
</head>

<body>

<?php include_once 'includs/header.php';


?>
<main>

    <?php
    $sql = "
select 
packs.item_id,
items.item_name 
FROM items
INNER JOIN packs ON packs.item_id = items.item_id
GROUP BY packs.item_id
order by item_name
";
    $sql = "
select * from zones
where zone_id < 30
order by side, zone_name
";
    $qwe = qwe($sql);

    ?>
    <div id="rent">
<h1>Пакулятор</h1>
        <form id="form">
        <div class="navcustoms">
            <label class="selab">
            <img src="../img/packmaker.png?ver=2" data-tooltip="Откуда">
            <select id='zfrom' name="from_id" autocomplete="off">
                <option value="100">Все локации</option>
                <?php SelectOpts($qwe,'zone_id','zone_name'); ?>

            </select>
            </label>

            <label class="selab">
                <img src="/img/icons/50/icon_item_1338.png" data-tooltip="Что">
                <select id="packselect" name="item_id" autocomplete="off"></select>
            </label>

            <label class="selab">
                <img src="../img/perdaru2.png" data-tooltip="Куда">
                <select id="zto" name="to_id" autocomplete="off"></select>
            </label>
            <div class="selab2">
                <div>
                <label>
                    <img  src="/img/icons/50/icon_item_1405.png" data-tooltip="Возраст пака">
                    <select id="freshtime" name = "freshtime" autocomplete="off">
                      <?php FreshTimeSelect('28941','18');?>
                    </select>
                </label>
                </div>
                <div class="perok"><label>
                        <input type="number" id="per" name="per" value="130" min="1" max="130"/><span>%</span></label>
                    <div class="siol">
                        <div class="nicon_out ">
                            <input type="checkbox" id="siol" name="siol" value="5">
                            <label class="navicon" for="siol" style="background-image: url(../img/icons/50/icon_item_3368.png);"></label>

                        </div>
                    </div>

<!--                <button type="button" id="formsend" class="def_button">Ок</button>-->

                </div>
            </div>
            <div class = "responses">
                <div id = "responses"></div>
            </div>

        </div>

        </form>

        <div id="rent_in" class="rent_in">
            <div class="clear"></div>
            <div class="all_info_area">
                <div class="all_info" id="all_info">
                    <div id="bill">
                        <input type="hidden" id="ritem_id" value="31858"/>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<?php

include_once 'pageb/footer.php'; ?>
</body>

<script type='text/javascript'>

    window.onload = function() {

        //ZtoLoad(18);
        PackLoad(100);
        //FreshTimeLoad('31858','18');
    };
    $('#form').on('change','#zfrom',function()
    {

        ZtoLoad();
        $(document).ready( function() {
            PackLoad();
        });



    });

    $(document).keypress(
        function(event){
            if (event.which == '13') {
                event.preventDefault();
                $('input').blur();
            }
        });

    $(document).ready( function(){


        $('#form').on('change','#packselect',function()
        {

            var zone = $("#packselect option:selected").data("id");
           $("#zfrom").val(zone);
           $("#packselect").children('[data-id!="'+zone+'"]').remove();

            ZtoLoad(zone)
            FreshTimeLoad();

        });
        $('#form').on('change','#freshtime, #per, #siol, #zto',function()
        {
            InfoLoad();
        });
/*
        $('#form').on('click','#formsend',function()
        {
            InfoLoad();
        });
*/

    });

    function InfoLoad(){
        var from_id = $("#zfrom").val();
        if(from_id === "100")
        {
            $("#bill").html('');
            return;
        }

        $.ajax
        ({
            url: "hendlers/packpost/packpostinfo.php", // путь к ajax файлу
            type: "POST",      // тип запроса

            data: $('#form').serialize(),
            dataType: "html",
            cache: false,
            // Данные пришли
            success: function(data )
            {
                $("#bill").html(data);
            }
        });
    }
/*
    $('#form').on('change','#to_id',function()
    {
        var to_id = $("#to_id").val();
        //var item_id = $(".nicon div[class=itim]","#all_info").attr("id").slice(5);
        PackLoad(to_id);
        //LoadTimes();
    });
*/
    function PackLoad(from_id) {
        if(!from_id)
            from_id = $("#zfrom").val();

        $.ajax
        ({
            url: "hendlers/packpost/selectpack.php", // путь к ajax файлу
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
                $("#packselect").html(data );
                FreshTimeLoad();
                //InfoLoad();
            },
            async:false
        });

    }

    function FreshTimeLoad(item_id,from_id) {
        if(!item_id)
            item_id = $("#packselect").val();
        if(!from_id)
            from_id = $("#zfrom").val();

        $.ajax
        ({
            url: "hendlers/packpost/selfreshtime.php", // путь к ajax файлу
            type: "POST",      // тип запроса

            data:
                {
                    item_id: item_id,
                    from_id: from_id
                },

            dataType: "html",
            cache: false,
            // Данные пришли
            success: function(data )
            {
                $("#freshtime").html(data );
                InfoLoad();
            },
            async: false
        });

    }

    function ZtoLoad(from_id) {
        if(!from_id)
            from_id = $("#zfrom").val();
        if(from_id === 100)
        {
            $("#zto").html('');
            $("#bill").html('');
            $("#freshtime").html('');
            return;
        }


        $.ajax
        ({
            url: "hendlers/packpost/selectzones.php", // путь к ajax файлу
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
                $("#zto").html(data );
            },async: false
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
            headers: {
                'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
            },
            cache: false,
            // Данные пришли
            success: function(data)
            {
                $('#responses').html(data);
                LoadTimes();
            }
        });
    }

    function DurDel(dur_id)
    {
        $.ajax({
            url: "hendlers/deldur.php", // путь к ajax файлу
            type: "POST",      // тип запроса
            headers: {
                'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
            },
            data: {
                dur_id: dur_id
            },

            // Данные пришли
            success: function(data) {
                if(data === "ok")
                    $("#row_"+dur_id).hide();
            }
        });
    }
</script>
</html>