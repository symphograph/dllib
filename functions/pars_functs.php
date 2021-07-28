<?php

function curl_redir_exec($ch)
{	
	static $curl_loops = 0;
  static $curl_max_loops = 20;
  if ($curl_loops >= $curl_max_loops)
    {
    $curl_loops = 0;
    return false;
    }
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $data = curl_exec($ch);
  list($header, $data) = explode("\n\n", $data, 2);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  
  if ($http_code == 301 || $http_code == 302)
    {
    $matches = array();
    preg_match('/Location:(.*?)\n/', $header, $matches);
    $url = @parse_url(trim(array_pop($matches)));
    if (!$url)
      {
      $curl_loops = 0;
      return $data;
      }
    $last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
    
    if (!$url['scheme'])
      $url['scheme'] = $last_url['scheme'];
    if (!$url['host'])
      $url['host'] = $last_url['host'];
    if (!$url['path'])
      $url['path'] = $last_url['path'];
    $new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query']?'?'.$url['query']:'');
    echo $new_url.' --- '.$http_code.'<br>';
    curl_setopt($ch, CURLOPT_URL, $new_url);
    return curl_redir_exec($ch);
    }
  else
    {
    $curl_loops = 0;
    return $data;
    }
}

/*
function LettersOnly($string)
{
	$string = strip_tags($string);
	$string = preg_replace('/[^a-zA-Zа-яА-ЯёЁ ]/ui', '',$string);
	$string = trim($string);
	return($string);
}
*/
/*
function DigitsOnly($string)
{
	//echo $string;
$string = strip_tags($string);
$string = preg_replace('/[^0-9]/ui', '',$string);
$string = intval($string);
	return($string);
}
*/
function DigitsOnly2($string)
{
	//echo $string;
$string = strip_tags($string);
$string = preg_replace('/[^0-9.]/ui', '',$string);
	//var_dump($string);
$string = $string*100;
$string = intval($string);
	return($string);
}
/*
function ItemNames($string)
{
	$string = strip_tags($string);
	$string = preg_replace('/[^0-9a-zA-Zа-яА-ЯёЁ \,\.\(\)\]\[\_\:«»\-]/ui', '',$string);
	$string = trim($string);
	return($string);
}
*/
function AboutCraft($preg, $type, $table)
{

	$pregm = preg_match_all($preg, $table, $arr);

	if(!$pregm) return(null);
	$output = $arr[1][0];
	if($type == 'digits')
		$output = DigitsOnly($output);
	if($type == 'letters')
		$output = LettersOnly($output);
	if($type == 'item_names')
		$output = ItemNames($output);
	if($type == 'descr')
		$output = Description($output);
	if($type == 'price_type')
		$output = PriceType($output);
	if($type == 'img')
		$output = Img($output);
	if($type == 'prof_need')
	{
		
		//$output = explode('с',$output);
		//$output = $output[1];
		$output = DigitsOnly($output);
		//var_dump($output);
	}
	if($type == 'craft_time')
		$output = DigitsOnly($output);
	
	return($output);
}

function Img($string)
{
$string = strip_tags($string);
//$string = preg_replace('/[^0-9]/ui', '',$string);
//$string = intval($string);
	return($string);
}
/*
function Description($string)
{
	$string = explode('Изготовление',$string);
	$string = $string[0];
	$string = explode('Стоимость:',$string);
	$string = $string[0];
	$string = str_replace('<hr class="hr_long">', '<br>',$string);
	$string = strip_tags($string,'<br>');
	$string = str_replace('123Ячейки для гравировки:', '',$string);
	$string = preg_replace('/[^0-9a-zA-Zа-яА-ЯёЁ \,\.\(\)\]\[\_\:«»\-(?<br>)]/ui', '',$string);
	$string = trim($string);
	return($string);
}
*/
function PriceType($output)
{
	$price_type = null;
	if(preg_match('/alt="lp"/',$output))
	{
		$price_type = 'Ремесленная репутация';
	}

	if(preg_match('/alt="honor_point"/',$output))
	{
		$price_type = 'Честь';
	}

	if(preg_match('#item--23633#',$output))
	{
		$price_type = 'Дельфийская звезда';
	}
	if(preg_match('#item--25816#',$output))
	{
		$price_type = 'Коллекционная монета «Джин»';
	}
	
	if(preg_match('#item--26921#',$output))
	{
		$price_type = 'Звездный ролл';
	}

    if(preg_match('#item--8001661#',$output))
    {
        $price_type = 'Арткоин';
    }

	if(preg_match('/alt="bronze"/',$output))
	{
		$price_type = 'gold';
	}
	return($price_type);
}

function ValutID($valut_name)
{
	$qwe = qwe("
	SELECT * FROM `valutas`
	WHERE `valut_name` = '$valut_name'
	");
	if(!$qwe or $qwe->rowCount() == 0)
		return false;
	$q = $qwe->fetchObject();
	
	return $q->valut_id;
}

function ParsIcons($item_id)
{
	$plink = 'http://archeagecodex.com/ru/item/'.$item_id;
//var_dump($item_id);
	$somepage = curl($plink);
	sleep(1);
	if(!$somepage) 
		return false;
	
	preg_match_all('#<td class="item-icon"><div style="position: relative; left: 0; top: 0;"><img src="//archeagecodex.com/items/(.+?)\.png" style#is', $somepage, $arr);
	if(!$arr)
		return false;
	
	if(!$arr[0][0])
		return false;
	//var_dump($arr[1][0]); die;
	$file = $arr[1][0];
	
	
	$file = str_replace("\\",'/',$file);
	$plink = 'https://archeagecodex.com/items/'.$file.'.png';
	

	$file = strip_tags($file);

	$tmp = dirname($_SERVER['DOCUMENT_ROOT']).'/imgtmp/'.$file.'.png';
	$img = curl($plink);

	

	file_force_contents($tmp, $img);

	if(!is_image($tmp))
	{
		echo 'img_error<br>';
		return false;
	}
		
	
	$path = 'img/icons/50/'.$file.'.png';
	file_force_contents($path, $img);
	
	$imghash = md5_file('img/icons/50/'.$file.'.png');
		qwe("
		UPDATE `items` 
		SET `icon` = '$file',
		`md5_icon` = '$imghash'
		WHERE `item_id` = '$item_id'
		");

	return $file;
	
}

function grab_image($url,$saveto){
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($saveto)){
        unlink($saveto);
    }
    $fp = fopen($saveto,'x');
    fwrite($fp, $raw);
    fclose($fp);
}

function IsItemExistInBD($item_id)
{
	$qwe = qwe("
	SELECT `item_id`
	FROM `items`
	WHERE `item_id` = '$item_id'
	");
	if((!$qwe) or $qwe->rowCount() == 0) 
		return false;
	
	return true;
}

function IsCraftExistInBD($craft_id)
{
	$qwe = qwe("
	SELECT `craft_id`
	FROM `crafts`
	WHERE `craft_id` = '$craft_id'
	");
	if((!$qwe) or $qwe->rowCount() == 0) 
		return false;
	
	return true;
}
function IsCraftDeletedInBD($craft_id)
{
	$qwe = qwe("
	SELECT `craft_id`
	FROM `deleted_crafts50`
	WHERE `craft_id` = '$craft_id'
	");
	if((!$qwe) or $qwe->rowCount() == 0) 
		return false;
	
	return true;
}
?>