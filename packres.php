<?php 
require_once 'includs/usercheck.php';
setcookie('path', 'packres');

$userinfo_arr = UserInfo();
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
<title>Ресурсы для паков</title>
<link href="css/default.css?ver=<?php echo md5_file('css/default.css')?>" rel="stylesheet">
<link href="css/user_prices.css?ver=<?php echo md5_file('css/user_prices.css')?>" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="js/setbuy.js?ver=<?php echo md5_file('js/setbuy.js')?>"></script>
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
<div id="rent">

	<div class="navcustoms">
	    <h2>Ресурсы для паков</h2><br>
        <div class="prmenu">
            <?php ServerSelect();?>

            <br>
            <a href="user_prices.php"><button class="def_button">Мои цены</button></a>
            <a href="user_customs.php"><button class="def_button">Настройки</button></a>

        </div><hr>
        <div class="modes"></div>
        <?php modes($mode); ?>

        <div class="PrColorsInfo">
            <span style="background-color:#f35454">Чужая цена</span>
            <span style="background-color:#dcde4f">Цена друга</span>
            <span style="background-color:#79f148">Ваша цена</span>
        </div>
        <div class = "responses"><div id = "responses"></div></div>
	</div>

<div id="rent_in" class="rent_in">
<div class="clear"></div>

<div class="all_info_area">
<div class="all_info" id="all_info">
<div id="items">
<div class="prices">
<?php
$sql = "
SELECT 
items.item_id, 
items.item_name, 
items.craftable,  
items.personal,
items.is_trade_npc,
items.valut_id,
items.icon,
items.basic_grade,
prices.auc_price,
prices.time,
isbest,
(isbest = 3) as isbuy,
user_crafts.craft_price
FROM
(
	SELECT item_id, result_item_id 
	FROM craft_materials 
	WHERE result_item_id IN 
	(
		SELECT DISTINCT item_id 
		FROM packs WHERE item_id IN 
		(SELECT result_item_id FROM crafts WHERE on_off)
	)
	OR result_item_id in (49003, 49033, 49034)
	OR result_item_id in 
	(
		SELECT item_id 
		FROM craft_materials 
		WHERE result_item_id 
		IN  (49003, 49033, 49034)
	)
	GROUP BY item_id
) as tmp
INNER JOIN items 
	ON items.item_id = tmp.item_id 
	AND items.on_off 
	AND items.item_id != 500
LEFT JOIN prices 
	ON prices.user_id = '$user_id' 
	AND prices.item_id = items.item_id 
	AND prices.server_group = '$server_group'
LEFT JOIN user_crafts 
	ON user_crafts.user_id = '$user_id' 
	AND user_crafts.item_id = items.item_id 
	AND user_crafts.isbest > 0
	ORDER BY isbuy DESC, item_name
	";
$qwe = qwe($sql);

$prof_q = qwe("SELECT * FROM `user_profs` where `user_id` ='$user_id'");
include 'cat-funcs.php';
include 'edit/funct-obhod2.php';
qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");
foreach($qwe as $q)
{
    //extract($q);
    if($q['craft_price']) continue;
    if(!$q['craftable']) continue;
    if($q['is_trade_npc'] and $q['valut_id'] == 500) continue;
    $itemq = $item_id = $q['item_id'];
    CraftsObhod($item_id,$dbLink,$user_id,$server_group,$server,$prof_q);

    unset($total, $itog, $craft_id, $rec_name, $item_id, $forlostnames, $orcost, $repprice, $honorprice, $dzprice, $soverprice, $mat_deep,
        $crafts, $crdeep, $deeptmp, $craftsq, $icrft,$crftorder,$craftarr);
}
qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");

$folows = Folows($user_id);
$qwe = qwe($sql);
UserPriceList($qwe);

function UserPriceList($qwe)
{

	global $user_id,$userinfo_arr;
    extract($userinfo_arr);
	foreach($qwe as $q)
	{
		extract($q);

        //var_dump($craft_price);
     if($is_trade_npc and $valut_id == 500) continue;

		?><div><?php

		$isby = '';

		if($craftable)
			$isby = intval($isbest)+1;
		if(!$time)
			$time = '01-01-0000';


        $iscolor = false;

        $pr_arr = PriceMode($item_id,$user_id) ?? false;
        if($pr_arr)
        {
            $auc_price = $pr_arr['auc_price'];
            $time = $pr_arr['time'];
            $iscolor = ColorPrice($pr_arr);
        }

		PriceCell($item_id,$auc_price,$item_name,$icon,$basic_grade,$time,$isby,$iscolor);

        if ($craft_price)
        {
            $craft_price = esyprice($craft_price,15,1);
            ?><div>Крафт:<?php echo $craft_price?></div><?php
        }

		?></div><?php
	}

}
?>
</div>



</div>
</div></div>
</div></div>
</main>

<?php
function CraftPriceForItem($item_id,$user_id)
{
    $qwe = qwe("
    SELECT * FROM user_crafts
    WHERE user_id = '$user_id'
    and item_id = '$item_id'
    order by isbest desc 
    limit 1
    ");
    if(!$qwe or $qwe->num_rows == 0)
        return false;
    $qwe = mysqli_fetch_assoc($qwe);
    return $qwe['craft_price'];
}

include_once 'pageb/footer.php'; ?>
</body>
<script type='text/javascript'>
window.onload = function() {

$(".small_del").show();
	
};

	
$('#all_info').on('input','.pr_inputs',function(){
	
	var form_id = $(this).get(0).form.id;
	//var name = $(this).attr("name");
	
	SetPrice(form_id);
	
});
	
function SetPrice(form_id)
{ 
	var form = $("#"+form_id);
	
	var item_id = form_id.slice(3);
	var okid = "#PrOk_"+item_id;

	$.ajax
	({
		url: "hendlers/setprcl.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: form.serialize(),
		
		dataType: "html",
		cache: false,
		// Данные пришли
		success: function(data ) 
		{
			$(okid).html(data );
			$(okid).show(); 
			setTimeout(function() {$(okid).hide('slow');}, 0);
			$("#prdel_"+item_id).show();
		}
	});
}


$('#all_info').on('click','.small_del',function(){
	//Удаляет цену юзера
	var form_id = $(this).get(0).form.id;
	var item_id = form_id.slice(3);
	var okid = "#PrOk_"+item_id;

	$.ajax
	({
		url: "hendlers/setprcl.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: 
			{
				del: 'del',
				item_id: item_id
			},
		
		dataType: "html",
		cache: false,
		// Данные пришли
		success: function(data ) 
		{
			$(okid).html(data );
			$(okid).show(); 
			setTimeout(function() {$(okid).hide('slow');}, 0);
			
			$("#prdel_"+item_id).hide('slow');
			$("#"+form_id).find("input[type=number]").val("");
		}
	});
	
});
	
$('#all_info').on('click','.itim',function(){
	var item_id = $(this).attr('id').slice(5);
	var url = 'catalog.php?item_id='+item_id;
	window.location.href = url;
});
</script>
</html>