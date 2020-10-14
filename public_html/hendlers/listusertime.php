<?php
$from_id = $_POST['from_id'] ?? 0;
$from_id = intval($from_id);
if(!$from_id) die('Откуда?');
$to_id = $_POST['to_id'] ?? 0;
$to_id = intval($to_id);
if(!$to_id) die('Куда?');
$transport = $_POST['transport'] ?? 0;
$transport = intval($transport);
if(!$transport) die('Куда?');
$buff_1 = $_POST['buff'][1] ?? 0;
$buff_1 = intval($buff_1);
$buff_2 = $_POST['buff'][2] ?? 0;
$buff_2 = intval($buff_2);
$buff_3 = $_POST['buff'][3] ?? 0;
$buff_3 = intval($buff_3);
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';
//printr($_POST);
if(!$user_id) die('user_id');

$qwe = qwe("
SELECT
user_routimes.dur_id,
user_routimes.user_id as tuser_id,
user_routimes.time,
mailusers.user_nick as tuser_nick,
mailusers.avafile,
user_routimes.durway,
if(IFNULL(mailusers.email,1),0,1) as registred
FROM 
mailusers
INNER JOIN user_routimes ON user_routimes.user_id = mailusers.mail_id
AND user_routimes.from_id = '$from_id'
AND user_routimes.to_id = '$to_id'
AND user_routimes.transport = '$transport'
AND user_routimes.buff_1 = '$buff_1'
AND user_routimes.buff_2 = '$buff_2'
AND user_routimes.buff_3 = '$buff_3'
ORDER BY
registred DESC, user_routimes.time DESC");

if((!$qwe) or $qwe->num_rows == 0)
    die('<br>Нет записей с такими параметрами.');
?>
<br>
<?php
foreach ($qwe as $q)
{
    extract($q);
    if(empty($tuser_nick))
        $tuser_nick = 'Неизвестный';

    if($avafile)
    {
        $avafile = '/img/avatars/'.$avafile;
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].$avafile))
            $avafile = '/img/init_ava.png';
    }else
        $avafile = '/img/init_ava.png';
    ?>
    <div class="perscell" id="row_<?php echo $dur_id?>">
        <div>
            <div class="nicon_out">
                <label class="navicon" style="background-image: url(<?php echo $avafile?>);"></label>
                <div class="persnames">
                    <div class="mailnick"><b><?php echo $tuser_nick?></b></div>
                    <div class="mailnick"><span><?php echo $durway?></span>мин</div>
                </div>
            </div>
            <?php
            if($user_id == $tuser_id)
            {
                ?><input
                type="button"
                id="<?php echo $dur_id?>"
                style="display: block;"
                name="dur_id" class="small_del"
                value="del"
                onclick="DurDel(<?php echo $dur_id?>)"
                data-tooltip="Удалить">
                <input type="hidden" id="to_input" value="<?php echo $durway?>">
                <?php
            }
            ?>
        </div>
    </div>
<?php
}
?>