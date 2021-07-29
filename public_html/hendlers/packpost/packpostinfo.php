<?php

if(!isset($_POST)) die();

foreach ($_POST as $k => $v)
{
    $p[$k] = intval($v);
}
$freshlvl =  $p['freshlvl'] ?? 0;
$per = $p['per'] ?? 130;
if($per > 130)
    die('>130!');

$item_id = $p['item_id'];
if(!$item_id) die('item_id');

$from_id = $p['from_id'];
$to_id = $p['to_id'];

if(!isset($_POST['psiol']))
    $psiol = 0;
else
    $psiol = 5;


require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$User = new User;
$User->check();
$user_id = $User->id;


$cook_settings =
    [
        'per' => $per,
        'psiol' => $psiol,
        'from_id' => $from_id,
        'to_id' => $to_id,
        'freshlvl' => $freshlvl,
        'item_id' => $item_id
    ];
$cooktime = time()+60*60*24*360;
setcookie("packpost",serialize($cook_settings),$cooktime,'/');

$Pack = new Pack();
$Pack->byId($item_id,$from_id,$to_id);
if(!$Pack->isCounted()){
    $Item= new Item();
    $Item->byId($item_id);
    $Item->RecountBestCraft(1);
    $Pack = new Pack();
    $Pack->byId($item_id,$from_id,$to_id);
    $Item = null;
}
$Pack->initSalary(
    per: $per,
    siol: $psiol,
    lvl: $freshlvl
);
?>
<div class="pinfo_row">
        <span class="pharam">Товар: [<?php echo $Pack->item_name?>]</span>
    </div><br>
<?php
//printr($Pack);
echo $Pack->Salary->salaryLetter();
//printr($Pack);
//echo SalaryLetter($per,$Pack->pack_price,$psiol,$Pack->Fresh->fresh_per,$Pack->item_name,$Pack->valuta_id);

if($cfg->myip)
{
    $Factory_list = PackPercents($Pack->pack_price,$psiol,$per,$Pack->fresh_per,2,1);

?>
<div class="pinfo_row">
        <span class="pharam">Исправить:</span>
    <span class="value">

        <div>
            <form id="editprice">
            <input type="number" id="newprice" name="newprice" value="<?php echo $Factory_list?>">
            <img src="img/bronze.png" width="15" height="15" alt="b">
                <input type="hidden" name="item_id" value="<?php echo $item_id?>">
                <input type="hidden" name="from_id" value="<?php echo $from_id?>">
                <input type="hidden" name="to_id" value="<?php echo $to_id?>">
                <input type="hidden" name="psiol" value="<?php echo $psiol?>">
                <input type="hidden" name="per" value="<?php echo $per?>">
            <button type="button" id="sendprice" class="def_button">ok</button>
            </form>

        </div>

    </span>
</div>
    <?php
}
?>
