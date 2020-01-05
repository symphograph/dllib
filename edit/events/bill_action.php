<?PHP
header('Content-Type: text/html; charset=utf-8');
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
//print_r($_POST);
$bank_id = $_POST['act'];
include("../../includs/config.php");
	$bank = 12345;
	$bquery = qwe("SELECT `bill`, `editor` FROM `ev_banks_log` where `bank_id`= '$bank_id' ORDER BY `id` DESC LIMIT 1");
foreach($bquery as $b)
{
	$bill_now = $b['bill'];
	$editor = $b['editor'];
}
echo 'В банке: <br><input type="number" name="bill"
    value= "'.$bill_now.'" style="width: 100px" autocomplete="off"> Записал: '.$editor;
echo '<br><input type="submit" value="Сохранить" style="width: 200px; height: 50px; border-radius: 10px; margin-top: 20px">';
}
?>
