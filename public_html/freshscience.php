<meta charset="utf-8">
<?php
$tstart = microtime(true);

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
if (!$cfg->myip)
    die('rrr');

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/functions/filefuncts.php';

$packNames = getPackNamesFromdb();
$doodNames = doodsFromdb();
$freshTypes = getfreshTypesFromdb();
$cards = cardsFromDir();
//printr($freshTypes);
//qwe("TRUNCATE `freshCards`");
foreach ($cards as $card){
    //printr($card);
    $DefCard = new FreshCardDefiner($packNames, $doodNames, $card, $freshTypes, $card['file']);
    //printr($DefCard->Card);
    //echo '-------------------------------';
}


$qwe = qwe("SELECT DISTINCT item_id FROM freshCards");
foreach ($qwe as $q){
    $item_id = $q['item_id'];
    $pdata = perdata($item_id);
    $pdata = perImplode($pdata);
    $pdatas[$item_id] = $pdata;

}
$pdatas = array_unique($pdatas);
asort($pdatas);

printr($pdatas);



/////--------------------------------------------------------------

function perImplode($pdata){
    for($i=1;$i<=5;$i++){
        $arr[$i] = $pdata[$i] ?? 'n';

    }
    //printr($arr);
    return implode('|',$arr);
}

function perdata(int $item_id):array
{
    $pdata = [];
    $qwe = qwe("SELECT * FROM freshCards WHERE item_id = '$item_id'");
    foreach ($qwe as $q){
        $pdata[$q['fresh_lvl']] = $q['fresh_per'];
    }
    ksort($pdata);
    return $pdata;
}

function perDoubList(int $lvl){
    $qwe = qwe("SELECT * FROM freshCards WHERE fresh_lvl = '$lvl'
/*group by item_id,fresh_per*/
order by fresh_per
");
    $alrady = [];
    foreach ($qwe as $q)
    {
        if(in_array($q['fresh_per'],$alrady))
            continue;

        echo '<p>'.$q['fresh_per'].'</p>';


        $Pack = new Pack();
        $Pack->getFromDB($q['item_id']);
        echo '<b>'.$Pack->item_name.'</b>';
        echo '<br>';
        printr(perdata($q['item_id']));
        //echo '<b>'.$Pack->fresh_group.'</b>';
        //echo '<br>';
        perdubles($q['fresh_per'],$lvl);
        echo '<br>----------------------------------';
        $alrady[] = $q['fresh_per'];
    }
}

function perdubles(int $fresh_per, int $lvl)
{
    $qwe = qwe("
    SELECT * FROM freshCards 
    WHERE fresh_per = '$fresh_per'
    AND fresh_lvl = '$lvl'
    /*GROUP BY item_id*/
    order by packName
    ");
    $arr = [];
    foreach ($qwe as $q){
        if(in_array($q['item_id'],$arr))
            continue;
        $Pack = new Pack();
        $Pack->getFromDB($q['item_id']);
        echo $Pack->item_name.' | '.$q['freshTypeName'].' | '.$Pack->z_from_name.'<br>';
        $arr[] = $q['item_id'];
    }
}

function cardsFromDir() : array
{
    $dir = dirname($_SERVER['DOCUMENT_ROOT']).'/freshcards/tmp/';
    $fileList = FileList($dir);

    $cards = [];
    foreach ($fileList as $k => $file){

        $card = cardFromFile($file);
        $date = cardDate($file);
        if(count($card)){
            $cards[$date] = $card;
        }

    }
    if(!count($cards)){
        return [];
    }

    return $cards;
}

function cardDate(string $file)
{
    if(!str_ends_with($file,'.txt')){
        return false;
    }
    $str = substr($file, 0, -4);
    $Date = DateTime::createFromFormat("d-m-Y G-i-s", $str,new DateTimeZone(' Asia/Sakhalin'));
    $Date->setTimezone(new DateTimeZone('Europe/Moscow'));
    $date = $Date->getTimestamp();
    return date('Y-m-d G:i:s',$date);
}

function getfreshTypesFromdb() : array
{
    $arr = [];
    $qwe = qwe("SELECT * FROM fresh_types");
    if(!$qwe or !$qwe->rowCount()){
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
    $qwe = qwe("SELECT item_id, item_name FROM items where categ_id in (133,171) AND on_off");
    if(!$qwe or !$qwe->rowCount()){
        return [];
    }
    foreach ($qwe as $q){
        $q = (object) $q;
        $packNames[$q->item_id] = mb_strtolower($q->item_name);
    }
    return $packNames;
}

function doodsFromdb() : array
{
    $doodNames = [];
    $qwe = qwe("
    SELECT 
    packs.native_id as item_id,
    packDoods.dood_name 
    FROM packDoods
    INNER JOIN packs 
    ON packDoods.dood_id = packs.dood_id
    ");
    if(!$qwe or !$qwe->rowCount()){
        return [];
    }
    foreach ($qwe as $q){
        $q = (object) $q;
        $doodNames[$q->item_id] = mb_strtolower($q->dood_name);
    }
    return $doodNames;
}

function cardFromFile(string $file) : array
{
    $dir = dirname($_SERVER['DOCUMENT_ROOT']).'/freshcards/tmp/'.$file;
    if(!file_exists($dir)){
        return [];
    }
    $arr = [];
    $data =  file($dir,FILE_IGNORE_NEW_LINES);
    foreach ($data as $str){
        $str = strPrepare($str);
        if(!empty($str))
        $arr[] = $str;
    }
    $arr['datetime'] = cardDate($file);
    $arr['file'] = $file;
    return $arr;
}

function getCardsFromFile() : array
{
    $dir = dirname($_SERVER['DOCUMENT_ROOT']).'/freshcards/Untitled.txt';
    if(!file_exists($dir)){
        return [];
    }


    $file =  file($dir,FILE_IGNORE_NEW_LINES);
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