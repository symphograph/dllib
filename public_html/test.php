<meta charset="utf-8">
<?php
//$tstart = microtime(true);

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
if (!$cfg->myip)
    die('rrr');

class myTest{
    private int $item_id;
    public string $item_name;

    public function __construct(public int $categ_id)
    {
    }

    public function __set(string $name, $value): void
    {

    }

    public function test()
    {

    }
}

$qwe = qwe("SELECT * FROM items where item_name and on_off limit 10");
$t = $qwe->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE,'myTest');
$t = new myTest();
$a = $t->intval();
printr($t);
?>


