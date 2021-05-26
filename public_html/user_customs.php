<?php
setcookie('path', 'user_customs');
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/filefuncts.php';
$User = new User();
if (!$User->check()) {
    header("Refresh: 0");
    die();
}

$ver = random_str(8);

$customType = $_GET['type'] ?? 0;
$customType = intval($customType);

$custWays = ['basedprices','custprofs','userprofile'];
$custWay = $custWays[$customType];
?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
  <meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, крафт" />
  <meta name=“robots” content=“index, nofollow”>
<title>Настройки</title>
<link href="css/default.css?ver=<?php echo $ver?>" rel="stylesheet">
<link href="css/customs.css?ver=<?php echo $ver?>" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=0.7">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
</head>

<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php';?>

<main>
    <div id="rent">
        <div class="menu_area">
            <div class="navcustoms">
                <div onClick="ContentLoad('<?php echo $custWays[0]?>')">
                    <div class="navicon" style="background-image: url(img/icons/50/icon_item_1766.png);"></div>
                    <div class="navname">Базовые цены</div>
                </div>
                <form method="POST" action="serverchange.php" name="server">
                    <select name="serv" id="server" class="server" onchange="this.form.submit()">
                    <?php
                    $query = qwe("SELECT * FROM `servers`");
                    SelectOpts($query, 'id', 'server_name', $User->server, false);

                    ?>
                    </select>
                </form>
                <div  onClick="ContentLoad('<?php echo $custWays[1]?>')">
                    <div class="navicon" style="background-image: url(img/profs/Обработка_камня.png);"></div>
                    <div class="navname">Уровни ремесла</div>
                </div>
            </div>

            <div class="modes">Режимы</div>
                <?php modes($User->mode); ?>
            </div>

            <div id="rent_in" class="rent_in">
                <div class="clear"></div>
                <div class="all_info" id="all_info">
                    <div id="items"></div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php 
include_once 'pageb/footer.php';
if(!$User->ismobiledevice)
    addScript('js/tooltips.js');
addScript('js/user-customs.js');
?>
<script type="text/javascript">
window.onload = function() {
ContentLoad("<?php echo $custWay?>");
};
</script>
</body>
</html>