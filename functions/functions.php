<?php
function curl($plink)
{	
	$curl = curl_init();
	 curl_setopt($curl, CURLOPT_HEADER, 0);
	 curl_setopt($curl, CURLOPT_FAILONERROR, 1);
	 curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // allow redirects
	 curl_setopt($curl, CURLOPT_TIMEOUT, 10); // times out after 4s
	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // return into a variable 
	 curl_setopt($curl, CURLOPT_URL, $plink);
	 curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5 GTB6");
	 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	 curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	$somepage = curl_exec($curl);
	//print_r($somepage);
	curl_close($curl);
	return $somepage;
}

function ru_date($format, $date = false)
{
	setlocale(LC_TIME, 'ru_RU.UTF-8');
	if (!$date) {
		$date = time();
	}
	if ($format === '') {
		$format = '%e&nbsp;%bg&nbsp;%Y';
	}
	$months = explode("|", '|января|февраля|марта|апреля|мая|июня|июля|августа|сентября|октября|ноября|декабря');
	$format = preg_replace("~\%bg~", $months[date('n', $date)], $format);
	return strftime($format, $date);
}

function number($n, $titles) 
{
	$n = intval($n);	
	$cases = array(2, 0, 1, 1, 1, 2);
	return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
}

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}

function printr($var) 
{
global $myip;
if(!$myip)
	return;
  echo '<pre>';
  print_r($var);
  echo '</pre>';
}

function OnlyText($string)
{
	$string = trim($string);
	$string = preg_replace('/[^0-9a-zA-Zа-яА-ЯёЁ]/ui', '',$string);
	$string = trim($string);
	return($string);
}

function Metka($ip,$BotName)
{
	//проверяем, помечен ли юзер
	//если не помечен, метим
	$unix_time = time();
	$datetime = date('Y-m-d H:i:s',$unix_time);
	$cooktime = $unix_time+60*60*24*365*5;
	$identy = random_str(12);
	$sessmark = random_str(12);
	if(empty($_COOKIE['identy']) or $BotName)
	{
		
		if($BotName)
		{
			$query = qwe("SELECT * FROM `mailusers` WHERE `email` = '$BotName'");
			if(mysqli_num_rows($query)>0)//Если бот уже записан
			{
				foreach($query as $q)
				{$identy = $q['identy'];}
				qwe("
				UPDATE `mailusers` SET
				`last_ip` = '$ip',
				`last_time` = '$datetime'
				WHERE BINARY `identy` = '$identy'");
				
			}else
			{
				//Записываем нового бота
				$newid = EmptyIdFinder('mailusers');
				qwe("
				INSERT INTO `mailusers`
				(`mail_id`, `identy`, `ip`, `time`, `last_ip`, `last_time`,`first_name`,`email`)
				VALUES
				('$newid' ,'$identy', '$ip', '$datetime','$ip','$datetime','$BotName','$BotName')
				");
				
				
			}
			$userinfo_arr = UserInfo($identy);
			return $userinfo_arr;
		}
		
		
		setcookie('identy',$identy,$cooktime,'/','',true,true);

		$newid = EmptyIdFinder('mailusers');
		qwe("
		INSERT INTO `mailusers`
		(`mail_id`, `identy`, `ip`, `time`, `last_ip`, `last_time`)
		VALUES
		('$newid' ,'$identy', '$ip', '$datetime','$ip','$datetime')
		");
		
		
		
		$userinfo_arr = UserInfo($identy);
		
	}else
	{
		$identy = OnlyText($_COOKIE['identy']);
		
		if(iconv_strlen($identy) != 12)
			exit('Bad Coockie');
		
		$userinfo_arr = UserInfo($identy);
		
		if($userinfo_arr)
		{
			setcookie('identy',$identy,$cooktime,'/','',true,true);
					
			qwe("
			UPDATE `mailusers` SET
			`last_ip` = '$ip',
			`last_time` = '$datetime'
			WHERE BINARY `identy` = '$identy'");
			
			$user_id = $userinfo_arr['muser'];
			DeviceMark($user_id,$unix_time);
		}else
		{
			//Кука есть, данных в базе нет.
			setcookie ("identy", "", time() - 3600);
			//echo 'Authorization ERROR';
			exit(header("Location: ".$_SERVER['PHP_SELF'], TRUE, 302));
			return false;
		}
	}
	
	return $userinfo_arr;
}

function DeviceMark($user_id,$unix_time)
{
	$unix_time = time();
	$datetime = date('Y-m-d H:i:s',$unix_time);
	$cooktime = $unix_time+60*60*24*365*5;
	$ip = $_SERVER['REMOTE_ADDR'];
	$agent = get_browser(null, true);
	extract($agent);
	
	if(empty($_COOKIE['sessmark']))
	{
		$sessmark = random_str(12);
		
		
		$sess_id = EmptyIdFinder('sessions');
		
		$qwe = qwe("
		INSERT INTO `sessions`
		(sess_id, `user_id`, `sessmark`, `first_ip`, `last_ip`, `first_time`, `last_time`, `platform`,`browser`,`device_type`, `ismobiledevice`)
		VALUES
		('$sess_id' ,'$user_id', '$sessmark','$ip', '$ip', '$datetime', '$datetime', '$platform', '$browser', '$device_type', '$ismobiledevice' )
		");
		if(!$qwe) die('ERROR_sess');
		setcookie('sessmark',$sessmark,$cooktime,'/','',true,true);
		return true;
	}
	
	//Если девайс помечен
	$sessmark = OnlyText($_COOKIE['sessmark']);	
	if(iconv_strlen($sessmark) != 12)
	{
		setcookie ("sessmark", "", time() - 3600);
		exit(header("Location: ".$_SERVER['PHP_SELF'], TRUE, 302));	
	}
	
	
	$qwe = qwe("SELECT * FROM `sessions` WHERE `sessmark` = '$sessmark' AND `user_id` = '$user_id'");
	if(mysqli_num_rows($qwe)>0)
	{
		$good = false;
		foreach($qwe as $q)
		{
			$good = ($q['platform'] == $platform and $q['browser'] = $browser and $q['device_type'] = $device_type);
			$sess_id = $q['sess_id'];
		}
		
		if(!$good)
		{
			setcookie ("sessmark", "", time() - 3600);
			exit(header("Location: ".$_SERVER['PHP_SELF'], TRUE, 302));	
		}
		
		$qwe = qwe("
		UPDATE `sessions`
		SET   
		`last_ip` = '$ip', 
		`last_time` = '$datetime'
		WHERE `sess_id` = '$sess_id'
		");
		if(!$qwe) die('ERROR_sess2');
		setcookie('sessmark',$sessmark,$cooktime,'/','',true,true);
		return true;
	}

	
	
		$sess_id = EmptyIdFinder('sessions');
		
		$qwe = qwe("
		INSERT INTO `sessions`
		(sess_id, `user_id`, `sessmark`, `first_ip`, `last_ip`, `first_time`, `last_time`, `platform`,`browser`,`device_type`, `ismobiledevice`)
		VALUES
		('$sess_id' ,'$user_id', '$sessmark','$ip', '$ip', '$datetime', '$datetime', '$platform', '$browser', '$device_type', '$ismobiledevice' )
		");
		if(!$qwe) die('ERROR_sess');
		setcookie('sessmark',$sessmark,$cooktime,'/','',true,true);
		return true;
	
}

function UserInfo($identy = '')
{
	if(is_bot())
	{
		$identy = 'oJOffNqzrQZY';
	}
	if(empty($identy))
	{
		if(empty($_COOKIE['identy']))
			return false;
		$identy = OnlyText($_COOKIE['identy']);
		
		if(iconv_strlen($identy) != 12)
			return false;
	}
		
	$query = qwe("
		SELECT
		`mailusers`.`mail_id`,
		`mailusers`.`last_time`,
		`mailusers`.`avatar`,
		`mailusers`.`last_ip`,
		`mailusers`.`email`,
		`mailusers`.`siol`,
		`user_servers`.`server`,
		`servers`.`server_group`,
		`servers`.`server_name`,
		`mailusers`.`first_name`,
		`mailusers`.`user_nick`,
		`mailusers`.`avafile`,
		`mailusers`.`mode`
		FROM
		`mailusers`
		LEFT JOIN `user_servers` ON `user_servers`.`user_id` = `mailusers`.`mail_id`
		LEFT JOIN `servers` ON `servers`.`id` = `server`
		WHERE BINARY `mailusers`.`identy` = '$identy'
		");
	//var_dump($query->num_rows);
	
	if((!$query) or $query->num_rows == 0) 
		return false;
	
	foreach($query as $q)
	{
		$userinfo_arr['muser'] = $q['mail_id'];
		$userinfo_arr['identy'] = $identy;
		$userinfo_arr['server'] = $q['server'] ?? 9;
		$userinfo_arr['server_group'] = $q['server_group'] ?? 2;
		$userinfo_arr['fname'] = $q['first_name'] ?? 'Незнакомец';
		$userinfo_arr['mode'] = $q['mode'] ?? 1;
		$avafile = $q['avafile'];

		if($avafile and file_exists('img/avatars/'.$avafile))
			$userinfo_arr['avatar'] = 'img/avatars/'.$avafile;
		elseif($q['email'])
		{
			$ava = $q['avatar'];
			include_once($_SERVER['DOCUMENT_ROOT'].'/functions/filefuncts.php');
			$avafile = AvaGetAndPut($ava,$identy);
			if($avafile)
				$userinfo_arr['avatar'] = 'img/avatars/'.$avafile;
			else 
				$userinfo_arr['avatar'] = 'img/8001096.png';

		}elseif(!$q['email'])
		{
			$userinfo_arr['avatar'] = 'img/8001096.png';
			$userinfo_arr['user_nick'] = '';
		}

		if($q['email'])
		{
			if(!$q['user_nick']) 
			$userinfo_arr['user_nick'] = NickAdder($q['mail_id']);
		else
			$userinfo_arr['user_nick'] = $q['user_nick'];
		}

		$userinfo_arr['email'] = $q['email'] ?? false;
		$userinfo_arr['siol'] = intval($q['siol']);	
	}
		

	//$userinfo_arr = false;
	return $userinfo_arr;
}

function is_bot()
{
	$user_agent = $_SERVER['HTTP_USER_AGENT'];

	if(empty($user_agent)) 
	return false;
    
    
    $bots = 
	[
        // Yandex
        'YandexBot', 'YandexAccessibilityBot', 'YandexMobileBot', 'YandexDirectDyn', 'YandexScreenshotBot',
        'YandexImages', 'YandexVideo', 'YandexVideoParser', 'YandexMedia', 'YandexBlogs', 'YandexFavicons',
        'YandexWebmaster', 'YandexPagechecker', 'YandexImageResizer', 'YandexAdNet', 'YandexDirect',
        'YaDirectFetcher', 'YandexCalendar', 'YandexSitelinks', 'YandexMetrika', 'YandexNews',
        'YandexNewslinks', 'YandexCatalog', 'YandexAntivirus', 'YandexMarket', 'YandexVertis',
        'YandexForDomain', 'YandexSpravBot', 'YandexSearchShop', 'YandexMedianaBot', 'YandexOntoDB',
        'YandexOntoDBAPI', 'YandexTurbo', 'YandexVerticals',

        // Google
        'Googlebot', 'Googlebot-Image', 'Mediapartners-Google', 'AdsBot-Google', 'APIs-Google',
        'AdsBot-Google-Mobile', 'AdsBot-Google-Mobile', 'Googlebot-News', 'Googlebot-Video',
        'AdsBot-Google-Mobile-Apps',

        // Other
        'Mail.RU_Bot', 'bingbot', 'Accoona', 'ia_archiver', 'Ask Jeeves', 'OmniExplorer_Bot', 'W3C_Validator',
        'WebAlta', 'YahooFeedSeeker', 'Yahoo!', 'Ezooms', 'Tourlentabot', 'MJ12bot', 'AhrefsBot',
        'SearchBot', 'SiteStatus', 'Nigma.ru', 'Baiduspider', 'Statsbot', 'SISTRIX', 'AcoonBot', 'findlinks',
        'proximic', 'OpenindexSpider', 'statdom.ru', 'Exabot', 'Spider', 'SeznamBot', 'oBot', 'C-T bot',
        'Updownerbot', 'Snoopy', 'heritrix', 'Yeti', 'DomainVader', 'DCPbot', 'PaperLiBot', 'StackRambler',
        'msnbot', 'msnbot-media', 'msnbot-news',
    ];

    foreach ($bots as $bot) 
	{
        if (stripos($user_agent, $bot))
            return OnlyText($bot);
    }
	/*
	if($_SERVER['REMOTE_ADDR'] == '188.113.161.10')
		return 'RomaBot';
	*/
    return false;
}

function price_str($price,$valuta)
{
	$minus = '';
	if($price < 0)
	{$price = $price*-1; $minus = '<span style="color: red"><b>-</b></span>';}
	if($valuta == 500)
	{   $price = str_pad($price, 6, "0", STR_PAD_LEFT);
		$g_img = '<img src="../img/gold.png" width="10" height="10" alt="gold"/>';
		$s_img = '<img src="../img/silver.png" width="10" height="10" alt="silver"/>';
		$br_img = '<img src="../img/bronze.png" width="10" height="10" alt="bronze"/>';
	 	if($price < 1000000)
		$s = sscanf($price, "%2d%2d%d", $gold, $silver, $bronse);
	 	else
		$s = sscanf($price, "%3d%2d%d", $gold, $silver, $bronse);
		$price = $minus.$gold.$g_img.str_pad($silver, 2, "0", STR_PAD_LEFT).$s_img.str_pad($bronse, 2, "0", STR_PAD_LEFT).$br_img;
	return($price);
	}else
	{   //$price = str_pad($price, 6, "0", STR_PAD_LEFT);
		$v_img = '<img src="../img/'.$valuta.'.png?ver='.md5_file('../img/'.$valuta.'.png').'" width="10" height="10" alt="coal"/>';
		//$s = sscanf($price, "%2d%2d%d", $gold, $silver, $bronse);
		$price = $price.$v_img;
		return($price);
	}
		 return($price);

}

function EmptyIdFinder($table,$colname = false)
{
	$table = $table;
	
	//Проверям, что это ключевой столбец и что он один.
	$qwe = qwe("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
	if(!$qwe or mysqli_num_rows($qwe) != 1)
		return false;
	
	if(!$colname)//Если столбец не указывали, находим.
	{
		foreach($qwe as $q)
			$colname = $q['Column_name']; 
	}
	
	$sql = "
	SELECT (`$table`.`$colname`+1) as `empty_id`
	FROM `$table`
	WHERE (
		SELECT 1 FROM `$table` as `tmpids` WHERE `tmpids`.`$colname` = (`$table`.`$colname` + 1)
	) IS NULL
	ORDER BY `$table`.`$colname`
	LIMIT 1
	";

	$qwe = qwe($sql);
	if($qwe->num_rows >0)
	{
		foreach($qwe as $q)
			return $q['empty_id'];
	}else
	return 1;
}

function dbCleaner()
{
	qwe("
	DELETE FROM mailusers
    WHERE
    mail_id NOT IN ( SELECT user_id FROM prices GROUP BY user_id ) 
    AND `mode` = 1 
    AND mail_id NOT IN ( SELECT user_id FROM user_routimes GROUP BY user_id )
    AND email IS NULL
    AND TO_DAYS(`last_time`) < TO_DAYS(NOW())-1
	");

	qwe("
	DELETE FROM `user_crafts` 
	WHERE `craft_id` IN 
	(SELECT `craft_id` FROM `crafts` WHERE `on_off` = 0)
	");

	qwe("
	DELETE FROM `user_crafts` 
	WHERE `item_id` IN 
	(SELECT `item_id` FROM `items` WHERE `on_off` = 0)
	");

	$qwe = qwe("SELECT * FROM user_crafts WHERE isbest = 3");
	foreach ($qwe as $q)
    {
        extract($q);
        qwe("
        REPLACE INTO user_buys
        (user_id, item_id)
        values 
        ('$user_id', '$item_id')
        ");
    }

	qwe("
    DELETE from user_buys
    WHERE user_id NOT IN
    (SELECT mail_id FROM mailusers)
    OR item_id NOT IN
    (SELECT item_id from items WHERE on_off)
    ");
}

function PriceValidator($array=[])
{
	$array2=[];
	foreach($array as $k => $v)
	{
		$v = intval($v);
		if($v < 0) $v = 0;
		
		if($k > 0)//если это серебро или бронза
		{
			if($v > 99) $v = 99;
		}else //если это голд
		if($v > 999999999) return false;
		
		$array2[] = $v;
	}
	$setprise = gmp_add(gmp_mul($array2[0],10000),($array2[1]*100+$array2[2]));
	$setprise = gmp_strval($setprise);
	$setprise = intval($setprise);
	if($setprise == 0) 
		return false;
	
	return $setprise;
}

function UserCraftPrice($item_id,$user_id)
{
	$qwe = qwe("
	SELECT * FROM `user_crafts` 
	WHERE `user_id` = '$user_id' 
	AND `item_id` = '$item_id'
	AND `isbest` > 0
	ORDER BY `isbest` DESC
	LIMIT 1
	");
	if(!$qwe) return false;
	if($qwe->num_rows == 0) return false;
	$qwe = mysqli_fetch_assoc($qwe);
	extract($qwe);
	if($isbest == 3)
		return PriceMode($item_id,$user_id)['auc_price'] ?? false;
	
	return $craft_price;
}

function ResultItemId($craft_id)
{
	$craft_id = intval($craft_id);
	$qwe = qwe("
	SELECT `result_item_id` FROM `crafts`
	WHERE `craft_id`= '$craft_id'
	");
	foreach($qwe as $q)
		return $q['result_item_id'];
}

function UserMatPrice($item_id,$user_id,$craftignor = false)
{
	
	$qwe = qwe("
	SELECT * FROM `items` 
	WHERE `item_id` = '$item_id'
	");
	if(!$qwe) return false;
	if($qwe->num_rows == 0) return false;
	$qwe = mysqli_fetch_assoc($qwe);
	extract($qwe);
	if($item_id == 500)
		return 1;
	
	
	
	if($is_trade_npc)
	{
		$valut_id = $valut_id ?? 500;
		if($valut_id == 500)
			return $price_buy;
		
		$matprice = PriceMode($item_id,$user_id)['auc_price'] ?? false;
		if($matprice)
			return $matprice;

		$matprice = PriceMode($valut_id,$user_id)['auc_price'] ?? false;
		if($matprice)
			return $matprice * $price_buy;	
	}
	
	if($craftable and (!$craftignor))
		{return UserCraftPrice($item_id,$user_id); echo $item_name;}
	
	return PriceMode($item_id,$user_id)['auc_price'] ?? false;
}

function ItemAny($item_ids,$colname)
{
	//Получить каку-нибудь колонку для одного или нескольких итемов
	if(is_array($item_ids))
		$item_ids = implode(',',$item_ids);
	
	$qwe = qwe("
	SELECT `item_id`, `".$colname."` FROM `items`
	WHERE `item_id` in (".$item_ids.")
	");
	if(!$qwe) return false;
	if($qwe->num_rows == 0) return false;
	foreach($qwe as $q)
	{	
		$arr[$q['item_id']] = $q[$colname];
	}
	if(count($arr)>0)
		return $arr;
	else 
		return false;
}

function AnyById($any_ids,$table,$colname)
{
	$qwe = qwe("SHOW KEYS FROM `".$table."` WHERE Key_name = 'PRIMARY'");
	if((!$qwe) or $qwe->num_rows != 1) return false;
	$qwe = mysqli_fetch_assoc($qwe);
	$key = $qwe['Column_name'];
	//Получить каку-нибудь колонку для одного или нескольких итемов
	if(is_array($any_ids))
		$any_ids = implode(',',$any_ids);
	
	
	$qwe = qwe("
	SELECT `".$key."`, `".$colname."` FROM `".$table."`
	WHERE `".$key."` in (".$any_ids.")
	");
	if(!$qwe) return false;
	if($qwe->num_rows == 0) return false;
	foreach($qwe as $q)
	{	
		$arr[$q[$key]] = $q[$colname];
	}
	if(count($arr)>0)
		return $arr;
	else 
		return false;
}

function NickGenerator()
{
	$l = 0;
	$keyspaces = 
	[
		['aeiou','bcdfghjklmnpqrstvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ'],
		['аеиоуыэюя','бвгджзклмнпрстфхчшщц','АБВГДЕЖЗИКЛМНОПРСТУФХЧШЩЦЫЭЮЯ']
	];
	$nick = random_str(1,$keyspaces[$l][2]);
	$r = random_int(3,19);
	for($i=0;$i<=$r;$i++)
	{
		$k = intval($i % 2 === 0);
		$nick .= random_str(1,$keyspaces[$l][$k]);
	}
	return $nick;
}

function NickAdder($mail_id)
{
	$qwe = qwe("
	SELECT `name` FROM `npnicks`
	WHERE `name` NOT IN
	(SELECT `user_nick` FROM `mailusers` WHERE `user_nick` is NOT NULL)
	LIMIT 1
	");
	if(!$qwe or $qwe->num_rows == 0)
		$nick = NickGenerator();
	else
	{
		$qwe = mysqli_fetch_assoc($qwe);
		$nick = $qwe['name'];
	}
	
	$qwe = qwe("UPDATE `mailusers` 
	SET `user_nick` = '$nick'
	WHERE `mail_id` = '$mail_id'
	");
	if(!$qwe)
		return false;
	
	return $nick;
}

function AvaGetAndPut($ava,$identy)
{
	$file = random_str(8);
	$tmp = $_SERVER['DOCUMENT_ROOT'].'/imgtmp/'.$file.'.tmp';
	//var_dump($tmp);
	$img = curl($ava);

	file_put_contents($tmp, $img);
	
	$is = is_image($tmp);
	if($is)
	{
		$filename = md5($img).'.'.$is;
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/img/avatars/'.$filename, $img);
		qwe("UPDATE `mailusers` SET `avafile` = '$filename'
		WHERE BINARY  `identy` = '$identy'
		");
	}else
	    return false;

	return $filename;
}

function PriceCell($item_id,$price,$item_name,$icon,$grade,$time='',$isby='',$iscolor = false)
{
	if(!empty($time))
		$time = date('d.m.Y',strtotime($time));
	if($iscolor)
    {
        $colors = ['', '#f35454', '#dcde4f', '#79f148'];
        $color = $colors[$iscolor];
        $color = ' style = "background-color:'.$color.'" ';
    }else
        $color = '';
	
	?>
	<div class="price_cell">
		<div class="price_row">
			<span class="comdate"><?php echo $time?></span>
			<?php if(!empty($isby))
			{
			//var_dump($isby);
			if($isby == 4)
				$checked = 'checked';
			else
				$checked = '';
			?>
			<div class="pricecell_date_row">				
				<div>
					<label data-tooltip="Куплю. Не буду крафтить.">
						<input type="checkbox" <?php echo $checked ?> id="isby_<?php echo $item_id ?>" name="isby"/>
						  <span class="comdate">Покупаемый</span>
					</label>
				</div>
			</div>
			<?php
			}
			?>	
			
		</div>
		<div class="price_row">
			<div class="itim" id="itim_<?php echo $item_id;?>" style="background-image: url('img/icons/50/<?php echo $icon;?>.png')">
				<div class="grade" class="grade" data-tooltip="<?php echo $item_name?>" style="background-image: url(/img/grade/icon_grade<?php echo $grade?>.png)">
					<div class="PrOk" id="PrOk_<?php echo $item_id;?>"></div>
				</div>
			</div>
			<div class="price_pharams">
				<div><span class="item_name" id="itname_<?php echo $item_name?>"><?php echo $item_name?></span>
					<form id="pr_<?php echo $item_id;?>">
						<div class="money_area_down">
						<?php MoneyLineBL($price,$item_id,$color);?>
						</div>
					<input type="hidden" name="item_id" value="<?php echo $item_id;?>"/>
					</form>
				</div>
			</div>
			
		</div>
	</div>
	<?php
}

function PriceCell2($item_id,$price,$item_name,$icon,$grade,$time='')
{
	if(!empty($time))
		$time = date('d.m.Y',strtotime($time));
	?>
	<div class="price_cell">
		<span class="comdate"><?php echo $time?></span>
		<div class="price_row">
			<div class="itim" id="itim_<?php echo $item_id;?>" style="background-image: url('img/icons/50/<?php echo $icon;?>.png')">
				<div class="grade" class="grade" data-tooltip="<?php echo $item_name?>" style="background-image: url(/img/grade/icon_grade<?php echo $grade?>.png)">
					<div class="PrOk" id="PrOk_<?php echo $item_id;?>"></div>
				</div>
			</div>
			<div class="price_pharams">
				<div><span class="item_name"><?php echo $item_name;?></span>
						<div class="money_area_down">
						<?php echo esyprice($price);?>
						</div>
				</div>
			</div>
			
		</div>
	</div>
	<?php
}

function PriceSolo($item_id,$user_id)
{
	//Хотим цены только от себя.
	global $server_group;
	if(!isset($server_group))
		$server_group = ServerGroup($user_id);
	$qwe = qwe("
	SELECT `auc_price`,`time` FROM `prices`
	WHERE `user_id` = '$user_id' 
	AND `item_id` = '$item_id'
	AND `server_group` = '$server_group'
	");
	if(!$qwe or $qwe->num_rows == 0)
		return false;//Конкретно вы ничего не указали. Зыните.
	$qwe = mysqli_fetch_assoc($qwe);
	//var_dump($qwe['auc_price']);
	return ['auc_price' => $qwe['auc_price'],'user_id'=>$user_id,'time'=>$qwe['time']];
}

function PriceWithFrends($item_id,$user_id)
{
	//Хотим цены только от друзей или себя.
	//Друзей предпочитаем, если цена новее.
	global $server_group;
	if(!isset($server_group))
		$server_group = ServerGroup($user_id);
	//Выясняем друзей.
	$qwe = qwe("
	SELECT `folow_id` FROM `folows`
	WHERE `user_id` = '$user_id'
	");
	if(!$qwe or $qwe->num_rows == 0)
		return PriceSolo($item_id,$user_id);
	
	foreach($qwe as $q)
	{
		$folows[] = $q['folow_id'];
	}
	//Добавляем себя в массив
	$folows[] = $user_id;

	$folows = implode(',',$folows);
	//var_dump($folows);
	$qwe2 = qwe("
	SELECT `auc_price`, `user_id`,`time`
	FROM `prices`
	WHERE `user_id` in (".$folows.")
	AND `item_id` = '$item_id'
	AND `server_group` = '$server_group'
	ORDER BY `time` DESC 
	LIMIT 1
	");
	if($qwe2 and $qwe2->num_rows > 0)
	{
		$qwe2 = mysqli_fetch_assoc($qwe2);
		return ['auc_price'=>$qwe2['auc_price'], 'user_id'=>$qwe2['user_id'],'time'=>$qwe2['time']];
	}
	
	
	return false;
}

function PriceMode1($item_id,$user_id)
{
	if(in_array($item_id,IntimItems()))
	{
		$price = PriceSolo($item_id,$user_id);
		if($price)
			return $price;
	}

		
	$price = PriceWithFrends($item_id,$user_id);
	
	if($price)
		return $price;
	
	$price = PriceFromGood($item_id,$user_id);
	if($price)
		return $price;
	return PriceFromAny($item_id,$user_id);
}

function PriceFromAny($item_id,$user_id)
{
	//ищем у кого угодно. Лишь бы найти.
	global $server_group;
	if(!isset($server_group))
		$server_group = ServerGroup($user_id);
	
	$qwe = qwe("
	SELECT `auc_price`, `user_id`,`time` FROM `prices` 
	WHERE `item_id` = '$item_id'
	AND `server_group` = '$server_group'
	ORDER BY `time` DESC 
	LIMIT 1
	");
	if(!$qwe or $qwe->num_rows == 0)
		return false;
	
	$qwe = mysqli_fetch_assoc($qwe);
	
		return ['auc_price'=>$qwe['auc_price'],'user_id'=>$qwe['user_id'],'time'=>$qwe['time']];
}

function PriceFromGood($item_id,$user_id)
{
	//ищем у хороших людей.
	global $server_group;
	if(!isset($server_group))
		$server_group = ServerGroup($user_id);
	
	$qwe = qwe("
	SELECT 
	`prices`.`auc_price`, 
	`prices`.`user_id`,
	`prices`.`time` 
	FROM `prices`
	INNER JOIN folows 
	ON (`prices`.`user_id` = folows.folow_id AND folows.user_id = 893) 
	OR (`prices`.`user_id` = 893 AND folows.user_id = `prices`.`user_id`)
	WHERE `item_id` = '$item_id'
	AND `server_group` = '$server_group'
	ORDER BY `time` DESC
	LIMIT 1
	");
	if(!$qwe or $qwe->num_rows == 0)
		return false;
	
	$qwe = mysqli_fetch_assoc($qwe);
	
		return ['auc_price'=>$qwe['auc_price'],'user_id'=>$qwe['user_id'],'time'=>$qwe['time']];
}

function PriceMode2($item_id,$user_id)
{
	
	if(in_array($item_id,IntimItems()))
	{
		$price = PriceSolo($item_id,$user_id);
		if($price)
			return $price;
	}
		
	return PriceWithFrends($item_id,$user_id);
}

function PriceMode($item_id,$user_id)
{
	global $mode;
	//var_dump($mode);
	
	if($mode == 1)
	{
		//Максимально широко.
		return PriceMode1($item_id,$user_id);
	}
	if($mode == 2)
	{	
		//В пределах друзей.
		return PriceMode2($item_id,$user_id);
	}
	if($mode == 3)
	{
		//Только у себя.
		return PriceSolo($item_id,$user_id);
	}
	//var_dump($pricearr);
	//return $pricearr;
	
}

function IsFolow($user_id,$folow_id)
{
	$qwe = qwe("
	SELECT * FROM folows 
	WHERE `user_id` = '$user_id'
	AND `folow_id` = '$folow_id'
	");
	if($qwe and $qwe->num_rows > 0)
		return true;
	return false;
}

function ServerGroup($user_id)
{
	$qwe = qwe("
	SELECT `server_group` FROM `user_servers`
INNER JOIN `servers` ON `user_servers`.`server` = `servers`.`id`
AND `user_servers`.`user_id` = '$user_id'
	");
	if(!$qwe or $qwe->num_rows == 0) 
		return 2;
	$qwe = mysqli_fetch_assoc($qwe);
	return $qwe['server_group'];
}

function UserCraftStatus($user_id,$item_id)
{
	$qwe = qwe("
	SELECT * FROM `user_crafts` 
	WHERE `user_id` = '$user_id' 
	AND `item_id` = '$item_id' 
	AND `isbest` > 0
	LIMIT 1
	");
	if(!$qwe or $qwe->num_rows == 0) 
			return false;

	$qwe = mysqli_fetch_assoc($qwe);

		return $qwe['isbest'];	
}

function BestCraftForItem($user_id,$item_id)
{
	$qwe = qwe("
	SELECT * FROM `user_crafts` 
	WHERE `user_id` = '$user_id' 
	AND `item_id` = '$item_id' 
	ORDER BY isbest DESC
	LIMIT 1
	");
	if(!$qwe or $qwe->num_rows == 0) 
			return false;

	$qwe = mysqli_fetch_assoc($qwe);

		return $qwe['craft_id'];	
}

/**
 * @return array|null
 * возвращает итемы, цена которых не должна изменяться другими юзерами
 */
function IntimItems()
{
	global $IntimItems;
	if(isset($IntimItems))
		return $IntimItems;
	$qwe = qwe("
	SELECT item_id FROM items 
	WHERE ((!is_trade_npc
	AND ismat
	AND !craftable
	AND on_off
	AND personal)
	OR item_id IN 
	(SELECT valut_id FROM valutas))
	AND item_id != 500
	");
	if(!$qwe or $qwe->num_rows == 0) 
			return [];
	foreach($qwe as $q)
	{
		$IntimItems[] = $q['item_id'];
	}
	return $IntimItems;
}

/**
 * @param $user_id
 * @return array
 * возвращает массив id юзеров, на чьи цены подписан юзер
 */
function Folows($user_id)
{
    $qwe = qwe("
    Select * from folows
    WHERE user_id = '$user_id'
    ");
    if(!$qwe or $qwe->num_rows == 0)
        return [];

    $follows = [];
    foreach ($qwe as $q)
    {
        $follows[] = $q['folow_id'];
    }
    return $follows;
}

function ColorPrice($auc_arr)
{
    global $user_id, $folows;
    if(!isset($folows))
        $folows = Folows($user_id);
    $auc_price = $auc_arr['auc_price'] ?? false;
    if(!$auc_price) return 1;

    if($user_id == $auc_arr['user_id'])
        return 3;

    if(in_array($auc_arr['user_id'],$folows))
        return 2;

    return 1;
}

function modes($mode)
{
    $chks = ['','checked'];
    $mode_names = ['','С миру по нитке', 'Доверие', 'Хардкор'];
    $mode_tooltips =
        [
            '',
            'Режим для новичка.<br>Предпочитает Ваши цены или более новые из доверенных.<br>Если их нет, ищет у других.<br>Спрашивает только, если никто и никогда не указывал цену.<br>',
            'Не видит ничьих цен, кроме Ваших и тех, кому Вы доверяете.<br>Предпочитает более новые.<br>ОР, РР, Честь и прочие субъективные предпочитает Ваши независимо от их новизны.',
            'Видит только Ваши цены.<br>В любой непонятной ситуации будет спрашивать.'
        ];
    ?>
    <form id="fmodes" method="post" action="edit/setmode.php">
        <div class="modes">
            <?php
            foreach($mode_names as $mnk => $mnv)
            {
                if(!$mnv) continue;
                ?>
                <label data-tooltip="<?php echo $mode_tooltips[$mnk];?>">
                    <div>
                        <input type="radio" <?php if($mode == $mnk) echo 'checked'?> name="mode" value="<?php echo $mnk;?>" onchange="this.form.submit()"/>
                        <?php echo $mnv;?>
                    </div>
                </label>
                <?php
            }
            ?>
        </div>
    </form>
    <hr>
    <?php
}

/**
 * возвращает массив итемов, крафт которых может быть изменён при изменении цены исходного итема
 */
function DependentItems($item_id, $arr=[],$i=0)
{
    $i = intval($i);
    $i++;

    $qwe = qwe("
    Select result_item_id, ismat 
    from craft_materials 
    inner join items on craft_materials.result_item_id = items.item_id
    and craft_materials.item_id = '$item_id' 
    and items.on_off
    group by result_item_id
    ");
    if(!$qwe or $qwe->num_rows == 0)
        return [];

    foreach ($qwe as $q)
    {
        $id = $q['result_item_id'];
        $ismat = $q['ismat'];
        $arr[] = $id;
        if($ismat)
            $arr = DependentItems($id, $arr,$i);
    }
    $arr = array_unique($arr);
    sort($arr);
    return $arr;
}

function SelectZone($zone_start=0)
{
    $zone_start = intval($zone_start);
    if(!$zone_start)
    {
        $qwe = qwe("
        select * from zones 
        where zone_id < 30
        order by side, zone_name
        ");

        $input_name = 'from_id';
    }else
    {
        $qwe = qwe("
        SELECT 
        pack_prices.zone_to as zone_id,
        zones.zone_name
        FROM pack_prices
        INNER JOIN zones ON pack_prices.zone_to = zones.zone_id
        AND pack_prices.zone_id = '$zone_start'
        GROUP BY pack_prices.zone_id, pack_prices.zone_to
        ");
        $input_name = 'to_id';
    }
    ?><select autocomplete="off"  name="<?php echo $input_name?>" id="<?php echo $input_name?>"><?php


    foreach($qwe as $q)
    {
        extract($q);
        ?><option value="<?php echo $zone_id?>"><?php echo $zone_name?></option><?php
    }
    ?></select><?php
}
?>