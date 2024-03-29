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
global $cfg;
if(!$cfg->myip)
	return;
  echo '<pre>';
  print_r($var);
  echo '</pre>';
}

function OnlyText($string)
{
	$string = trim($string);
	$string = preg_replace('/[^0-9a-zA-Zа-яА-ЯёЁ ]/ui', '',$string);
	$string = trim($string);
	return($string);
}

function Metka($BotName)
{
	//проверяем, помним ли юзера
	//если нет, запоминаем
	$unix_time = time();
	$datetime = date('Y-m-d H:i:s',$unix_time);
	$cooktime = $unix_time+60*60*24*365*5;


    $ip = $_SERVER['REMOTE_ADDR'];
    if($BotName)
    {
        $query = qwe("SELECT * FROM `mailusers` WHERE `email` = '$BotName'");
        if($query and $query->rowCount())//Если бот уже записан
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
            $identy = random_str(12);
            $newid = EmptyIdFinder('mailusers');
            qwe("
            INSERT INTO `mailusers`
            (`mail_id`, `identy`, `ip`, `time`, `last_ip`, `last_time`,`first_name`,`email`)
            VALUES
            ('$newid' ,'$identy', '$ip', '$datetime','$ip','$datetime','$BotName','$BotName')
            ");
        }
        setcookie('identy',$identy,$cooktime,'/','',true,true);
        $userinfo_arr = UserInfo($identy);
        return $userinfo_arr;
    }

    if(empty($_COOKIE['identy']))
    {
        $identy = random_str(12);
		setcookie('identy',$identy,$cooktime,'/','',true,true);

		$newid = EmptyIdFinder('mailusers');
		qwe("
		INSERT INTO `mailusers`
		(`mail_id`, `identy`, `ip`, `time`, `last_ip`, `last_time`)
		VALUES
		('$newid' ,'$identy', '$ip', '$datetime','$ip','$datetime')
		");

		$userinfo_arr = UserInfo($identy);
		
	}
	else
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
            header("Refresh: 0");
			die();
		}
	}

	return $userinfo_arr;
}

function DeviceMark($user_id,$unix_time = 0)
{
    if(!$unix_time)
	    $unix_time = time();

	if(empty($_COOKIE['sessmark']))
	{
	    //Add new device
		$sessmark = random_str(12);
        SetSess($user_id,$sessmark,$unix_time);
        return true;
	}
	
	//We know this device
	$sessmark = OnlyText($_COOKIE['sessmark']);	
	if(iconv_strlen($sessmark) != 12)
	{
		setcookie ("sessmark", "", time() - 3600);
        header("Refresh: 0");
		exit();
	}
	
	
	$qwe = qwe("
    SELECT * FROM `sessions` 
    WHERE `sessmark` = '$sessmark' 
    AND `user_id` = '$user_id'");
	if(!$qwe or !$qwe->rowCount())
	{
        SetSess($user_id, $sessmark, $unix_time);
        return true;
    }
    $q= $qwe->fetchObject();

    $agent = get_browser(null, true);
    $agent = (object) $agent;


    $good = ($q->platform == $agent->platform and $q->browser == $agent->browser and $q->device_type == $agent->device_type);
    $sess_id = $q->sess_id;


    if(!$good)
    {
        setcookie ("sessmark", "", time() - 3600);
        header("Refresh: 0");
        die();
    }

    $datetime = date('Y-m-d H:i:s',$unix_time);
    $ip = $_SERVER['REMOTE_ADDR'];

    $qwe = qwe("
    UPDATE `sessions`
    SET   
    `last_ip` = '$ip', 
    `last_time` = '$datetime'
    WHERE `sess_id` = '$sess_id'
    ");
    if(!$qwe) die('ERROR_sess2');

    $cooktime = $unix_time+60*60*24*365*5;
    setcookie('sessmark',$sessmark,$cooktime,'/','',true,true);
    return true;
}

function SetSess($user_id,$sessmark,$unix_time)
{
    $agent = get_browser(null, true);
    if(!$agent) die('agent');
    $agent = (object) $agent;

    $datetime = date('Y-m-d H:i:s',$unix_time);
    $cooktime = $unix_time+60*60*24*365*5;
    $sess_id = EmptyIdFinder('sessions');
    $ip = $_SERVER['REMOTE_ADDR'];

    $qwe = qwe("
    INSERT INTO `sessions`
    (
     sess_id, `user_id`, `sessmark`, `first_ip`, `last_ip`, `first_time`,
     `last_time`, `platform`,`browser`,`device_type`, `ismobiledevice`
     )
    VALUES
    (
     '$sess_id' ,'$user_id', '$sessmark','$ip', '$ip', '$datetime', 
     '$datetime', '$agent->platform', '$agent->browser', '$agent->device_type', '$agent->ismobiledevice'
     )
    ");
    if(!$qwe) die('ERROR_sess');
    setcookie('sessmark',$sessmark,$cooktime,'/','',true,true);
    return true;
}

function UserInfo($identy = '')
{
    $userinfo_arr = false;
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
	//var_dump($query->rowCount());
	
	if((!$query) or $query->rowCount() == 0) 
		return false;
	
	foreach($query as $q)
	{
		$userinfo_arr['muser'] = $q['mail_id'];
        $userinfo_arr['user_id'] = $q['mail_id'];
		$userinfo_arr['identy'] = $identy;
		$userinfo_arr['server'] = $q['server'] ?? 9;
		$userinfo_arr['server_group'] = $q['server_group'] ?? 2;
		$userinfo_arr['fname'] = $q['first_name'] ?? 'Незнакомец';
		$userinfo_arr['mode'] = $q['mode'] ?? 1;
		$avafile = $q['avafile'];

		if($avafile and file_exists($_SERVER['DOCUMENT_ROOT'].'/img/avatars/'.$avafile))
			$userinfo_arr['avatar'] = 'img/avatars/'.$avafile;
		elseif($q['email'])
		{
			$ava = $q['avatar'];
			include_once($_SERVER['DOCUMENT_ROOT'].'/../functions/filefuncts.php');
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
		//TODO Сиоль приходит формы и работает в паках. Но база этого не помнит!
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

    return false;
}

function price_str($price,$valuta)
{

	$minus = '';
	if($price < 0)
	{
	    $price = $price*-1;
	    $minus = '<span style="color: red"><b>-</b></span>';
	}
	if($valuta == 500)
	{
        return $minus . esyprice($price,1);
	}

    $v_img = '<img src="../img/'.$valuta.'.png?ver='.md5_file($_SERVER['DOCUMENT_ROOT'].'/img/'.$valuta.'.png').'" width="10" height="10" alt="coal"/>';

    return $price.$v_img;
}

function EmptyIdFinder($table,$colname = false)
{

	//Проверям, что это ключевой столбец и что он один.
	$qwe = qwe("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
	if(!$qwe or $qwe->rowCount() != 1)
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
	if($qwe->rowCount())
    {
        foreach($qwe as $q)
            return $q['empty_id'];
    }

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

	$qwe = qwe("SELECT user_id, item_id FROM user_crafts WHERE isbest = 3");
	foreach ($qwe as $q)
    {
        $q = (object) $q;
        qwe("
        REPLACE INTO user_buys
        (user_id, item_id)
        values 
        ('$q->user_id', '$q->item_id')
        ");
    }

	/*
	qwe("DELETE FROM user_crafts WHERE isbest != 2");
	qwe("UPDATE user_crafts SET craft_price = null");
	*/

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
	if($qwe->rowCount() == 0) return false;
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
	if((!$qwe) or $qwe->rowCount() != 1) return false;
	$q = $qwe->fetchObject();
	$key = $q->Column_name;
	//Получить каку-нибудь колонку для одного или нескольких итемов
	if(is_array($any_ids))
		$any_ids = implode(',',$any_ids);
	
	
	$qwe = qwe("
	SELECT `".$key."`, `".$colname."` FROM `".$table."`
	WHERE `".$key."` in (".$any_ids.")
	");
	if(!$qwe) return false;
	if($qwe->rowCount() == 0) return false;
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
	if(!$qwe or $qwe->rowCount() == 0)
		$nick = NickGenerator();
	else
	{
		$q = $qwe->fetchObject();
		$nick = $q->name;
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
	$tmp = dirname($_SERVER['DOCUMENT_ROOT']).'/imgtmp/'.$file.'.tmp';

	global $cfg;
	if(in_array($identy,$cfg->broken_avas))
	    return 'init_ava.png';


	$img = curl($ava);

	file_put_contents($tmp, $img);
	
	$is = is_image($tmp);
	unlink($tmp);
	if($is)
	{
		$filename = md5($img).'.'.$is;
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/img/avatars/'.$filename, $img);
		qwe("UPDATE `mailusers` SET `avafile` = '$filename'
		WHERE BINARY  `identy` = '$identy'
		");
	}else
	    return 'init_ava.png';

	return $filename;
}

function PriceCell(int $item_id,$item_name,$icon, $grade,$time='',$isby='',$amount='')
{
    $Price = new Price($item_id);

    if(in_array($_SERVER['SCRIPT_NAME'],['/user_prices.php','/hendlers/user_prices.php']))
        $Price->Solo();
    else
    {
        $Price->byMode();
        $Price->getColor();
    }



    $grade = intval($grade);
    if(!$grade)
        $grade = 1;
	if(!empty($time))
		$time = date('d.m.Y',strtotime($Price->time));
	?>
	<div class="price_cell">
		<div class="price_row">
			<span class="comdate"><?php echo $time?></span>
			<?php if(!empty($isby))
			{
                $chks = ['',' checked '];
                $chk = intval($isby);

                ?>
                <div class="pricecell_date_row">
                    <div>
                        <label data-tooltip="Куплю. Не буду крафтить.">
                            <input type="checkbox" <?php echo $chks[$chk] ?> id="isby_<?php echo $item_id ?>" name="isby"/>
                              <span class="comdate">Покупаемый</span>
                        </label>
                    </div>
                </div>
                <?php
			}
			?>	
			
		</div>
		<div class="price_row">

            <?php
            $Cubik = new Cubik($item_id,$icon,$grade,$item_name,$amount);
            $Cubik->print();
            ?>
			<div class="price_pharams">
				<div><span class="item_name" id="itname_<?php echo $item_name?>"><?php echo $item_name?></span>
					<form id="pr_<?php echo $item_id;?>">
						<div class="money_area_down">
						<?php $Price->MoneyLineBL();?>
						</div>
					<input type="hidden" name="item_id" value="<?php echo $item_id;?>"/>
					</form>
				</div>
			</div>
			
		</div>
	</div>
	<?php
}

function PriceCell2($item_id,$price,$item_name,$icon,$grade,$time='',$amount='')
{
	if(!empty($time))
		$time = date('d.m.Y',strtotime($time));
	?>
	<div class="price_cell">
		<span class="comdate"><?php echo $time?></span>
		<div class="price_row">

            <?php

            $Cubik = new Cubik($item_id,$icon,$grade,$item_name,$amount);
            $Cubik->print();
			?>
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

function BestCraftForItem($user_id,$item_id)
{
	$qwe = qwe("
	SELECT * FROM `user_crafts` 
	WHERE `user_id` = '$user_id' 
	AND `item_id` = '$item_id' 
	ORDER BY isbest DESC
	LIMIT 1
	");
	if(!$qwe or $qwe->rowCount() == 0) 
			return false;

	$q = $qwe->fetchObject();

    return $q->craft_id;
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
	WHERE 
    (
	    (
	        !is_trade_npc
            AND ismat
            AND !craftable
            AND on_off
            AND personal
        )
        OR item_id IN (SELECT valut_id FROM valutas)
    )
	AND item_id != 500
	");
	if(!$qwe or $qwe->rowCount() == 0) 
			return [];
	foreach($qwe as $q)
	{
		$IntimItems[] = $q['item_id'];
	}
	return $IntimItems;
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

function allZones() : array
{
    $arr = [];
    $qwe = qwe("SELECT * FROM zones");
    if(!$qwe or !$qwe->rowCount()){
        return $arr;
    }

    foreach ($qwe as $q)
    {
        $Zone = new Zone();
        $Zone->byQ($q);
        $arr[$Zone->zone_id] = $Zone;
    }
    return $arr;
}

function SelectZone($zone_start=0,$zone_selected = 0)
{
    $zone_start = intval($zone_start);
    if(!$zone_start)
    {
        $qwe = qwe("
        select * from zones 
        where fresh_type
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
        $q = (object) $q;
        $chk = '';
        if($q->zone_id == $zone_selected)
            $chk = ' selected ';
        ?><option value="<?php echo $q->zone_id?>"<?php echo $chk?>><?php echo $q->zone_name?></option><?php
    }
    ?></select><?php
}

function UserPriceList($qwe)
{

    global $lost;
    foreach($qwe as $q)
    {
        $q = (object) $q;
        $time = 0;
        ?><div class="price_object"><?php

        $isby = '';

        if($q->craftable)
            $isby = intval($q->isbuy);

        $amount = $q->mater_need ?? '';
        $basic_grade = $q->basic_grade ?? 1;

        $Price = new Price($q->item_id);
        $Price->byMode();
        $Price->getColor();

        if($q->is_trade_npc and $q->valut_id == 500)
            PriceCell2($q->item_id,$q->price_buy,$q->item_name,$q->icon,$basic_grade,'',$amount);
        else
            PriceCell($q->item_id,$q->item_name,$q->icon,$basic_grade,$Price->time,$isby,$amount);


        if ($q->craft_price)
        {
            $esyprice = esyprice($q->craft_price,1);
            ?><div>Крафт:<?php echo $esyprice?></div><?php
        }

        ?></div><?php
    }

}

function PackZoneFromId(int $item_id)
{

    $qwe = qwe("
    SELECT zone_from FROM packs 
    WHERE item_id = '$item_id'
    ");
    if(!$qwe or !$qwe->rowCount())
        return [];

    foreach ($qwe as $q)
    {
        $arr[] = $q['zone_from'];
    }
    return $arr;
}

function PackPercents($pack_price,$siol,$per,$fresh_per,$standart,$factlist = false)
{

    $salary = $pack_price/130*100*($per/100)*(1+$siol/100);
    $salary = round($salary);

    $Factory_list = $salary;
    if($factlist)
        return $Factory_list;

    $salary = $salary*(1+($fresh_per/100));
    $salary = round($salary);


    $salary = $salary*(1+$standart/100);
    $salary = round($salary);

    return $salary;
}

function SalaryLetter($per, $pack_price, $siol, $fresh_per, $item_id, $valuta)
{
    ob_start();
    if ($valuta != 500)
        $siol = 0;

    $salary = PackPercents($pack_price, $siol,$per,$fresh_per,2,0);
    $Factory_list = PackPercents($pack_price, $siol,$per,$fresh_per,2,1);

    $salary = price_str($salary, $valuta);
    $pack_price = round($pack_price/130*100);
    $pack_price = price_str($pack_price, $valuta);
    $Factory_list = price_str($Factory_list, $valuta);
    $freguency = 100+$fresh_per;


    ?>

    <div class="pinfo_row">
        <span class="pharam"><b>Фактическая выручка</b></span>
        <span class="value" data-tooltip="Сколько вы получите из письма"><b><?php echo $salary?></b></span>
    </div>
    <hr><br>
    <div class="pinfo_row">
        <span class="pharam">Оновная плата</span>
        <span class="value" data-tooltip="Чистыми без всего при 100%"><?php echo $pack_price?></span>
    </div>

    <div class="pinfo_row">
        <span class="pharam">Льгота</span>
        <span class="value" data-tooltip="Сиоль"><?php echo $siol?>%</span>
    </div>
    <div class="pinfo_row">
        <span class="pharam">Ставка</span>
        <span class="value" data-tooltip="Текущий процент у торговца"><?php echo $per?>%</span>
    </div>
    <div class="pinfo_row">
        <span class="pharam">Срок годности</span>
        <span class="value" data-tooltip="Свежесть"><?php echo $freguency?>%</span>
    </div>
    <div class="pinfo_row">
        <span class="pharam">Дополнительная надбавка</span>
        <span class="value">2%</span>
    </div>
    <?php
    if($valuta == 500)
    {
        ?>
        <hr>
        <div class="pinfo_row">
        <span class="pharam">В списке фактории</span>
        <span class="value" data-tooltip="Отображается в списке цен в фактории"><?php echo $Factory_list?></span>
        </div>
        <?php
    }
    return ob_get_clean();
}

function PackObject($item_id)
{
    global $user_id;
    $qwe = qwe("
	SELECT
    packs.zone_from,
    packs.pack_sname,
    pack_types.pack_t_name,
    pack_types.pack_t_id,
    pack_types.pass_labor,
    zones.zone_name,
    zones.side,
    round(`pass_labor` * (100 - IFNULL(`save_or`,0)) / 100,0) AS `pass_labor2`,
    uc.craft_price	       
    FROM
    packs
    INNER JOIN pack_types ON packs.item_id = '$item_id' 
    AND pack_types.pack_t_id = packs.pack_t_id
    INNER JOIN zones ON packs.zone_from = zones.zone_id
    LEFT JOIN `user_profs` ON `user_profs`.`user_id` = '$user_id'
    AND `user_profs`.`prof_id` = 5
    LEFT JOIN `prof_lvls` ON `user_profs`.`lvl` = `prof_lvls`.`lvl`
    LEFT JOIN user_crafts uc on packs.item_id = uc.item_id	
    and uc.isbest > 0 and uc.user_id = `user_profs`.`user_id`
    limit 1
	");
    if(!$qwe or $qwe->rowCount() == 0)
        return [];
    return $qwe->fetchObject();
}

function ProfUnEmper(int $user_id)
{
    $prof_q = qwe("SELECT * FROM `user_profs` where `user_id` ='$user_id'");
    if($prof_q and $prof_q->rowCount())
        return false;

    $query = qwe("
	SELECT *
	FROM `profs`
	WHERE `used` = 1");
    foreach($query as $q)
    {
        $q = (object) $q;
        qwe("
		REPLACE INTO `user_profs` 
		(`user_id`, `prof_id`, `lvl`) 
		VALUES 
		('$user_id', '$q->prof_id', 1)");
    }
    return true;
}

function UserPriceList2($qwe)
{
    global $puser_id, $User;

    if(!$qwe or !$qwe->rowCount()){
        ?>
        <div>
            Похоже, что записей о ценах нет.<br>
            Их Можно сделать здесь:<br><br>
            <a href="catalog.php"><button class="def_button">Крафкулятор</button></a><br><br>
            <a href="user_customs.php"><button class="def_button">Настройки</button></a><br><br>
            <a href="packres.php"><button class="def_button">Ресурсы для паков</button></a>
        </div>
        <?php
        return false;
    }


    foreach($qwe as $q)
    {
        $q = (object) $q;
        ?><div><?php

        $chk = $isby = '';

        if($q->craftable)
            $isby = intval($q->ismybuy);

        $basic_grade = $q->basic_grade ?? 1;

        if($puser_id == $User->id) {
            PriceCell($q->item_id, $q->item_name, $q->icon, $basic_grade, $q->time, $isby);
        }else
            PriceCell2($q->item_id,$q->auc_price,$q->item_name,$q->icon,$basic_grade,$q->time);
        ?>
        </div><?php
    }
}

function printVals($name,$val,$tooltip = '')
{
    if(!empty($tooltip)){
        $tooltip = ' data-tooltip="'.$tooltip.'" ';
    }
    ?>
    <div class="crresults"<?php echo $tooltip?>>
        <div><?php echo $name?></div>
        <div><?php echo $val;?></div>
    </div>
    <?php
}

function SPTime($mins)
{
    if(!$mins)
        return '';

    $m = $mins;
    $h = floor($m/60);
    $m = $m-$h*60;

    $d = floor($h/24);
    $h = $h-$d*24;
    if($d>0)
        $d = $d.'д.+';
    else
        $d = '';
    return $d.$h.':'.$m;
}

