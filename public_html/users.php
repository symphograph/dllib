<?php
setcookie('path', 'users');
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

?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
  <meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, крафт" />
  <meta name=“robots” content=“index, nofollow”>
<title>Пользователи</title>
<?php CssMeta(['default.css','users.css']);?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
</head>

<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php';

?>
<main>
    <div id="rent">
        <div class="menu_area">
            <div class="navcustoms">
                <h2>Цены пользователей</h2>
                <div class="buttons">

                    <form method="POST" action="serverchange.php" name="server">
                        <select name="serv" id="server" class="server" onchange="this.form.submit()">
                        <?php
                        $query = qwe("SELECT * FROM `servers`");
                        SelectOpts($query, 'id', 'server_name', $User->server, false);

                        ?>
                        </select>
                    </form>
                    <a href="user_prices.php"><button class="def_button">Мои цены</button></a>
                    <a href="user_customs.php"><button class="def_button">Настройки</button></a>
                </div>
            </div><hr>
        </div>
        <div id="rent_in" class="rent_in">
            <div class="clear"></div>
            <div class="all_info" id="all_info">
                <div id="items">
                    <details><summary><b>Как это работает?</b></summary>
                        <div class="long_text"><br>
                        В обычном режиме калькулятор ищет цены, предпочитая Ваши записи.
                        Если их нет, используются цены других пользователей.
                        В этом случае можно получить неожиданный результат.<br>
                        Здесь Вы можете выбрать пользователей, чьим ценам Вы доверяете.
                        В настройках можно выбрать желаемую область видимости цен.<br><br>

                        Если цена пользователя, которому Вы доверяете, новее Вашей, она будет использована в расчетах.<br><br>
                        Эта опция не распространяется на предметы, имеющие цену субъективного характера. Например, Ремесленнаую репутацию, Очки работы, Честь, Вексель региональной общины и еще пару сотен подобных. При любых настройках из этого списка будет предпочитаться именно Ваша цена.
                        <br><br>
                        Ники сгенерированы случайным образом.
                        <?php
                        if($User->email)
                            echo 'Изменить свой ник можно в <a href="profile.php">профиле</a>.';
                        ?>
                        </div>
                    </details><br>
                    <form method="post" id="fol_form">
                    <?php
                    $IntimString = implode(',',IntimItems());

                    //var_dump($device_type);
                    $checks = ['','checked'];

                    $qwe = qwe("
                    SELECT 
                        mailusers.*,
                        `cnt`,
                        `avatar` as remote_avalink,
                        `avafile`, 
                        `mtime`, 
                        (folows.folow_id > 0) as `isfolow`,
                        `identy` as `midenty`,
                        flwt.flws
                        FROM
                        (SELECT `user_id`, COUNT(*) as `cnt`, max(`time`) as `mtime` FROM `prices`
                        WHERE `server_group` = '$User->server_group'
                        AND `item_id` NOT in ( $IntimString )
                        GROUP BY `user_id`
                        ORDER BY `time` DESC
                        ) as `tmp`
                        INNER JOIN `mailusers` ON `mailusers`.`mail_id` = `tmp`.`user_id`
                        AND `mailusers`.`email` LIKE '%@%'
                        LEFT JOIN `folows` ON folow_id = `mail_id` AND `folows`.`user_id` = '$User->id'
                        LEFT JOIN (SELECT count(*) as flws, user_id, folow_id  FROM `folows` GROUP BY folow_id) as flwt 
                        ON `mail_id` = flwt.folow_id
                        ORDER BY `isfolow` DESC, YEAR(`mtime`) DESC, MONTH(`mtime`) DESC, WEEK(`mtime`,1) DESC, (cnt>50) DESC, `mtime` DESC
                        LIMIT 100
                        ");

                    foreach($qwe as $q)
                    {
                        $q = (object) $q;
                        $Puser = new User();
                        $Puser->persRow($q);
                        ?><hr><?php
                    }
                    ?>

                    <input type="hidden" name="folow[0]" value=0>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php 
include_once 'pageb/footer.php';
addScript('js/users.js');
if(!$User->ismobiledevice)
    addScript('js/tooltips.js');
?>
</body>
<?php

?>

</html>