<?php


class Price
{
    const  COLORS = [
        '',
        '#f35454',
        '#dcde4f',
        '#79f148'
    ];

    const  TCOLORS = [
        '',
        '#f35454',
        'green',
        'darkgreen'
    ];

    public int $item_id = 0;
    public int $price = 0;
    public string $time = '2020-02-22';
    public string $date = '2020-02-22';
    public int $autor = 0;
    public string $how = 'Неизвестно';
    public string $color = '';
    public string $tcolor = '';
    public int $serverMedian = 0;
    public array $exploded = [];
    public string $text = 'Цена: ';
    public string $text2 = 'Цена: ';

    public function __construct(int $item_id = 0)
    {
        $this->item_id = $item_id;
    }

    public function initData()
    {
        self::byMode();
        self::getColor();
        //self::exploded();
        $this->date = date('d.m.Y',strtotime($this->time));
        self::text2();
    }

    public function byMode() : bool
    {
        global $User;
        if(!isset($User))
            die('Missed User');

        $this->how = 'Цена пользователя';
        if($User->mode == 1) {
            //Максимально широко.
            return self::Mode1();
        }

        if($User->mode == 2) {
            //В пределах друзей.
            return self::Mode2();
        }

        if($User->mode == 3) {
            //Только у себя.
            return self::Solo();
        }

        die('Missed mode');
    }

    private function Mode1() : bool
    {

        if(in_array($this->item_id,IntimItems())) {
            if(self::Solo())
                return true;
        }

        if(self::withFrends())
            return true;

        if(self::fromGood())
            return true;

        return self::fromAny();
    }

    private function Mode2() : bool
    {

        if(in_array($this->item_id,IntimItems())) {
            if(self::Solo())
                return true;
        }

        return self::withFrends();
    }

    public function Solo() : bool
    {
        global $User;

        $qwe = qwe("
            SELECT `auc_price`,`time` FROM `prices`
            WHERE `user_id` = '$User->id' 
            AND `item_id` = '$this->item_id'
            AND `server_group` = '$User->server_group'
            ");
        if($qwe and $qwe->rowCount()) {

            $q= $qwe->fetchObject();

            $this->autor = $User->id;
            $this->price = $q->auc_price;
            $this->time = $q->time;
            return true;
        }

        if(self::IsValuta())
            return false;

        if(!in_array($this->item_id,IntimItems()))
            return false;


        $arr =  ItemAny($this->item_id,'price_sale');
        if(!$arr)
            return false;


        $price = array_shift($arr);
        $price = intval($price);
        if(!$price)
            return false;


        $this->price = $price;
        $this->autor = 1;
        $this->time = '2020-02-22';

        return true;
    }

    private function withFrends() : bool
    {
        global $User;
        //Хотим цены только от друзей или себя.
        //Друзей предпочитаем, если цена новее.
        //Выясняем друзей.

        if(!count($User->folows()))
            return self::Solo();


        $folows = implode(',',$User->folows);

        $qwe = qwe("
            SELECT `auc_price`, `user_id`,`time`
            FROM `prices`
            WHERE (`user_id` in ( $folows ) or `user_id` = '$User->id')
            AND `item_id` = '$this->item_id'
            AND `server_group` = '$User->server_group'
            ORDER BY `time` DESC 
            LIMIT 1");
        if(!$qwe or !$qwe->rowCount())
            return false;

        $q= $qwe->fetchObject();
        $this->price = $q->auc_price;
        $this->autor = $q->user_id;
        $this->time = $q->time;

        return true;
    }

    private function fromGood() : bool
    {
        //ищем у юзеров, чьи записи преимущественно адеквадны
        global $User;

        $qwe = qwe("
            SELECT 
            `prices`.`auc_price`, 
            `prices`.`user_id`,
            `prices`.`time` 
            FROM `prices`
            INNER JOIN folows 
            ON (`prices`.`user_id` = folows.folow_id AND folows.user_id = 893) 
            OR (`prices`.`user_id` = 893 AND folows.user_id = `prices`.`user_id`)
            WHERE `item_id` = '$this->item_id'
            AND `server_group` = '$User->server_group'
            ORDER BY `time` DESC
            LIMIT 1");
        if(!$qwe or !$qwe->rowCount())
            return false;

        $q= $qwe->fetchObject();
        $this->price = $q->auc_price;
        $this->autor = $q->user_id;
        $this->time = $q->time;

        return true;
    }

    private function fromAny() : bool
    {
        //ищем у кого угодно. Лишь бы найти.
        global $User;

        $qwe = qwe("
            SELECT `auc_price`, `user_id`,`time` FROM `prices` 
            WHERE `item_id` = '$this->item_id'
            AND `server_group` = '$User->server_group'
            ORDER BY `time` DESC 
            LIMIT 1");
        if(!$qwe or !$qwe->rowCount())
            return false;

        $q= $qwe->fetchObject();

        $this->price = $q->auc_price;
        $this->autor = $q->user_id;
        $this->time = $q->time;

        return true;
    }

    public function byCraft() : bool{

        global $User;

        $qwe = qwe("
            SELECT `craft_price`, `updated` FROM `user_crafts` 
            WHERE `user_id` = '$User->id' 
            AND `item_id` = '$this->item_id'
            AND `isbest` in (1,2)
            ORDER BY `isbest` DESC
            LIMIT 1
            ");
        if(!$qwe or !$qwe->rowCount())
            return false;

        $q= $qwe->fetchObject();
        if(!$q->craft_price)
            return false;

        $this->price = $q->craft_price;
        $this->autor = 1;
        $this->time = $q->updated;
        $this->how  = 'Себестоимость (крафт)';
        return true;
    }

    public function getColor(): bool
    {
        if(!$this->price){
            $this->color = self::COLORS[1];
            $this->tcolor = self::TCOLORS[1];
            return true;
        }


        global $User;

        if($User->id == $this->autor){
            $this->color = self::COLORS[3];
            $this->tcolor = self::TCOLORS[3];
            $this->how = 'Ваша цена';
            return true;
        }



        if(in_array($this->autor,$User->folows())){
            $this->color = self::COLORS[2];
            $this->tcolor = self::TCOLORS[2];
            $this->how = 'Цена друга';
            return true;
        }


        $this->color = self::COLORS[1];
        $this->tcolor = self::TCOLORS[1];
        return true;
    }

    public function MoneyLineBL()
    {
        global $User;
        //self::getColor();

        $is_show = intval($this->autor == $User->id);
        $is_shows = ['style="display: none;"',''];
        $gol = strrev(substr(strrev($this->price),4,10));
        $sil = strrev(substr(strrev($this->price),2,2));
        $bro = strrev(substr(strrev($this->price),0,2));
        $img_gold = '<img src="img/gold.png" width="15" height="15" alt="g"/>';
        $img_silver = '<img src="img/silver.png" width="15" height="15" alt="s"/>';
        $img_bronze = '<img src="img/bronze.png" width="15" height="15" alt="b"/>';
        ?>

        <div class="money-line">
            <input
                type="number"
                name="setgold"
                class="pr_inputs"
                value= "<?php echo $gol;?>"
                min=0 max="999999999"
                id="gol_<?php echo $this->item_id;?>"
                autocomplete="off"
                style="background-color: <?php echo $this->color;?>"
            >
            <?php echo $img_gold;?>
        </div>

        <div class="money-line">
            <input
                type="number"
                name="setsilver"
                class="pr_inputs"
                value= "<?php echo $sil;?>"
                min=0 max=99
                id="sil_<?php echo $this->item_id;?>"
                autocomplete="off"
                style="background-color: <?php echo $this->color;?>"
            >
            <?php echo $img_silver;?>
        </div>

        <div class="money-line">
            <input
                type="number"
                name="setbronze"
                class="pr_inputs"
                value= "<?php echo $bro;?>"
                min=0 max=99 id="bro_<?php echo $this->item_id;?>"
                autocomplete="off"
                style="background-color: <?php echo $this->color;?>"
            >
            <?php echo $img_bronze;?>
        </div>

        <input type="hidden" name="item_id" value="<?php echo $this->item_id;?>">
        <input type="button" id="prdel_<?php echo $this->item_id;?>" <?php echo $is_shows[$is_show]?> name="del" class="small_del" value="del" data-tooltip="Удалить свою цену">
        <?php

    }

    public function MoneyForm(): bool
    {
        global $User;

        if(!$this->price){
            $this->color = '';
            return self::PriceDataForm();
        }


        self::getColor();
        $text = self::text();

        self::PriceDataForm();


        return true;
    }

    public function text()
    {
        global $User;

        if(!$this->price){
            return $this->text;
        }

        if($this->autor == $User->id) {

            $this->text = '<a href="user_prices.php" data-tooltip="Все мои цены">Вы указали: </a>';
        }else {

            $Puser = new User();
            $Puser->byId($this->autor);

            if($Puser->user_nick) {

                $this->text = '<a href="user_prices.php?puser_id='.$Puser->id.'" data-tooltip="Смотреть его(её) цены">'.$Puser->user_nick.'</a> указал: ';
            }else
                $this->text = 'Кто-то указал: ';
        }

        return $this->text;
    }

    public function text2()
    {

        global $User;
        $Server = new Server($User->id);
        $serverStr = '<span style="color: #3E454C" data-tooltip="Выбрать в настройках"><a href="user_customs.php">' . $Server->name . '</a></span>';
        $timestr = '';
        if($this->price)
            $timestr = date('d.m.Y',strtotime($this->time)) .$serverStr.' <br>';
        else
            $timestr = $serverStr.' <br>';

        ob_start();
        ?>

        <span style="color: <?php echo $this->tcolor?>">
            <?php echo $timestr.self::text()?>
        </span>
        <?php
        $this->text2 = ob_get_clean();
        return $this->text2;

    }

    public function PriceDataForm(): bool
    {
        self::text2();
        echo $this->text2;
        ?>

        <form id="pr_<?php echo $this->item_id?>">
            <div class="money_area_down">
                <?php self::MoneyLineBL();?>
                <span id="PrOk_<?php echo $this->item_id?>"></span>
            </div>
        </form>
        <?php
        return true;
    }

    public function IsValuta() : int
    {
        $qwe = qwe("SELECT * FROM valutas WHERE valut_id = '$this->item_id'");
        if(!$qwe or !$qwe->rowCount())
            return 0;
        $q= $qwe->fetchObject();

        if($q->valut_id)
            return $q->valut_id;

        return 0;
    }

    public function serverMedian()
    {
        global $User;
        $qwe = qwe("
            SELECT * FROM prices 
            WHERE item_id = '$this->item_id' 
            AND server_group = '$User->server_group'
            order by time DESC
            ");
        if(!$qwe or !$qwe->rowCount()){
            return false;
        }
        $arr = [];
        $i = 0;
        foreach ($qwe as $q)
        {
            if($q['auc_price'] > 1){
                $i++;
                $arr[] = $q['auc_price'];
                //echo $q['time'].esyprice($q['auc_price']);
            }

            if($i>=100)
                break;
        }

        if(count($arr)){
            $this->serverMedian = median($arr);
            return $this->serverMedian;
        }


        foreach ($qwe as $q)
        {
            $arr[] = $q['auc_price'];
        }

        $this->serverMedian = median($arr);
        return $this->serverMedian;
    }

    public function serverMedianPrint(){
        if(!self::serverMedian())
            return false;

        ?><br>В среднем по больнице:<br><?php
        echo esyprice($this->serverMedian,1);
        ?><br><br><?php
        return true;
    }

    public function exploded() : array
    {

        $price = round($this->price);

        $minus = $price < 0 ? '-' : '';
        $str = (string) $price;
        $str = strrev($str);

        sscanf($str,'%2s%2s%s',$b,$s,$g);
        $arr = [$g,$s,$b];

        $arr = array_map('strrev',$arr);
        $arr = array_map('intval',$arr);
        array_unshift($arr,$minus);

        $this->exploded = $arr;
        return $arr;

    }

    public function del() : bool
    {
        global $User;
        if(!isset($User->id)){
            return false;
        }

        $qwe = qwe("
            DELETE FROM `prices`
            WHERE `user_id` = :user_id
            AND `item_id` = :item_id 
            AND `server_group` = :server_group;
            ",[
                'user_id'=>$User->id,
                'item_id'=>$this->item_id,
                'server_group'=>$User->server_group
            ]);
        if(!$qwe){
            return false;
        }


        $qwe = qwe("
            DELETE FROM `user_crafts` 
            WHERE `user_id` = :user_id
            AND `isbest` < 2",
            ['user_id'=>$User->id]
        );
        if(!$qwe){
            return false;
        }

        return true;
    }

    public function insert(int $value, bool|int $multi = false) : bool
    {
        if(!$value){
            return false;
        }

        global $User;
        if(!isset($User->id)){
            return false;
        }

        $qwe = qwe("
            REPLACE INTO `prices`
            (user_id,item_id,auc_price,server_group,`time`)
            VALUES 
            (:user_id,:item_id,:auc_price,:server_group, now())
            ", [
            'user_id'      => $User->id,
            'item_id'      => $this->item_id,
            'auc_price'    => $value,
            'server_group' => $User->server_group
        ]);
        if(!$qwe){
            return false;
        }

        if(!$multi){
            return $User->clearUCraftCache();
        }
        return true;

    }



}