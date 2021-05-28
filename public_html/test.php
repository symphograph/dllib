<meta charset="utf-8">
<?php
$tstart = microtime(true);

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if (!$cfg->myip)
    die('rrr');
class Test1
{
    public int $a;
    private int $b;
    public function clone($q)
    {
        $q = (object) $q;
        foreach ($q as $k=>$v){
            if(!$v or empty($v))
                continue;
            $this->$k = $v;
        }
    }

}

class Test2 extends Test1
{
    public int $c;
    public int|null $d = 5;

    public function clone($q)
    {
        parent::clone($q);
    }


}
$q = ['a'=> 1,'b'=>2,'c'=>3,'d' => 0,'loh'=>'lohh'];
$Test1 = new Test1;
$Test1->clone($q);
$Test2 = new Test2;
$Test2->clone($Test1);
printr($Test2);
echo '<br><br>'. (microtime(true) - $tstart);
?>

