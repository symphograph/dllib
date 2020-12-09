<?php
$path = $_SERVER['SCRIPT_NAME'];
$path = str_replace('/','',$path);
$path = str_replace('.php','',$path);
function pajeHeader()
{
    global $User;
    if(!isset($User))
    {
        $User = new User();
        $User->check();
    }

    $User->iniAva();
    $first_name = $User->fname;
    $user_nick = $User->user_nick ?? $first_name;

    if(empty($User->email))
    {
        $profile = 'Войтии';
        $profileLink = '../oauth/mailru.php';
    }else
    {
        $profile = $user_nick;
        $profileLink = '../profile.php';
    }

    ?>
    <header id="header">

        <div class="sitename">
            <a href="/"><div class='logo'></div></a>
            <h1><a href="/" style="text-decoration: none;">Dead Legion</a></h1>
        </div>


        <nav>
            <a  href="../users.php" style="color: white" title="Сообщество">
                <div class="navicon" style="background-image: url(img/comunity.png);"></div>
                <div class="navname">сообщество</div>
            </a>
            <a  href="../packtable.php" style="color: white" title="Цены на паки">
                <div class="navicon" style="background-image: url(img/icons/50/icon_item_1338.png);"></div>
                <div class="navname">паки</div>
            </a>
            <a href="../catalog.php" style="color: white;" title="Умный калькулятор">
                <div class="navicon" style="background-image: url(img/icons/50/icon_item_4069.png);"></div>
                <div class="navname">крафкулятор</div>
            </a>
            <a href="<?php echo $profileLink;?>" style="color: white;" title="Профиль">
                <div class="navicon" style="background-image: url(<?php echo $User->avatar?>);"></div>
                <div class="navname"><?php echo $profile;?></div>
            </a>
        </nav>
    </header>
<?php
}
pajeHeader();