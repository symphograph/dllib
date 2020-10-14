<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';
setcookie('path', 'routestime');
if(!$myip) die();
$userinfo_arr = UserInfo();
if (!$userinfo_arr){
    header("Refresh: 0");
    die();
}

extract($userinfo_arr);

$ver = random_str(8);

?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
<meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, крафт" />
<meta name=“robots” content=“index”>
<meta name="token" content="<?php echo SetToken()?>">
    <title>Время в пути</title>
    <link href="css/packpost.css?ver=<?php echo md5_file('css/packpost.css')?>" rel="stylesheet">

</head>

<div class="site">
    <a class="skip-link screen-reader-text" href="#content">Skip to content</a>

    <header class="masthead">
        <h2 class="site-title">Standard two-column layout</h2>
    </header><!-- .masthead -->
    <aside class="sidebar">
        <h3>The Sidebar</h3>
        <p>The sidebar typically contains things like:</p>
        <ul>
            <li>Links</li>
            <li>Menus</li>
            <li>Ads</li>
        </ul>
    </aside>
    <main id="content" class="main-content">
        <h2>Main content area</h2>
        <p>The main content<br>
            area is where the magic happens. Right now, the main content is on the left and the sidebar is on the right. If you go into the markup for this document and add <code>dir="rtl"</code> to the <code>html</code> element, the two elements will swap spaces because CSS Grid honors text direction.</p>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    </main>



    <aside class="twin">
        This should take up half the space
    </aside>
    <aside class="twin">
        This should take up the other half of the space
    </aside>

    <footer class="colophon grid">
        <aside>Content, layout, design: <a href="" target="_blank" rel="nofollow">рпарправпрп ороро-Hенгновоьь</a>.</aside>
    </footer>

</div>
</html>