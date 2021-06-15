<meta charset="utf-8">
<?php
$tstart = microtime(true);

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if (!$cfg->myip)
    die('rrr');

/*
$types = [];
$qwe = qwe("SELECT item_id, item_name, description FROM items where categ_id = 133");
foreach ($qwe as $q){
    $q = (object) $q;
    if(!str_contains($q->description,'Тип'))
        continue;
    $preg = preg_match_all('#Тип(.+?)товар#',$q->description,$arr);
    $typeName = explode(' ',$arr[1][0])[1];
    $types[] = $typeName;
    //printr($typeName);
}
$types = array_unique($types);
printr($types);
*/
$resume = [];
$cards = getCardsFromFile();
$packNames = getPackNamesFromdb();
$freshTypes = getfreshTypesFromdb();
foreach ($cards as $card){
    $DefCard = new FreshCardDefiner($packNames, $card, $freshTypes);
    printr($DefCard->Card);
    $resume[$DefCard->Card->freshTypeName][$DefCard->Card->fresh_lvl][$DefCard->Card->condition][$DefCard->Card->fresh_per] = 1;
    echo '_______________________________';
    ksort($resume[$DefCard->Card->freshTypeName]);
    $conditions[$DefCard->Card->condition] = 1;
}
ksort($resume);
printr($resume);
printr($conditions);


function getfreshTypesFromdb() : array
{
    $arr = [];
    $qwe = qwe("SELECT * FROM fresh_types");
    if(!$qwe or !$qwe->num_rows){
        return $arr;
    }
    foreach ($qwe as $q){
        $q = (object) $q;
        $arr[$q->id] = $q->name;
    }
    return $arr;
}

function getPackNamesFromdb() : array
{
    $packNames = [];
    $qwe = qwe("SELECT item_id, item_name FROM items where categ_id in (133,171)");
    if(!$qwe or !$qwe->num_rows){
        return [];
    }
    foreach ($qwe as $q){
        $q = (object) $q;
        $packNames[$q->item_id] = mb_strtolower($q->item_name);
    }
    return $packNames;
}

function getCardsFromFile() : array
{
    $url = dirname($_SERVER['DOCUMENT_ROOT']).'/freshcards/Untitled.txt';
    if(!file_exists($url))
        return [];

    $file =  file($url,FILE_IGNORE_NEW_LINES);
    $cards = [];
    $add = [];
    foreach ($file as $str){


        if ($str != ''){
            $str = strPrepare($str);
            if(!empty($str))
                $add[] = $str;
        }else{
            $cards[] = $add;
            $add = [];
        }
    }
    return $cards;
}

function strPrepare(string $str)
{
    $str = removeBOM($str);
    $str = preg_replace('/\s/', ' ', $str);
    $str = trim($str);
    $str = mb_strtolower($str);
    return $str;
}