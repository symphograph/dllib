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
    public int $autor = 0;
    public string $how = 'Неизвестно';
    public string $color = '';
    public string $tcolor = '';

    public function __construct(int $item_id)
    {
        $this->item_id = $item_id;
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
        if($qwe and $qwe->num_rows) {

            $q = mysqli_fetch_object($qwe);

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

        if(!$User->folows())
            return self::Solo();


        $folows = implode(',',$User->folows);

        $qwe = qwe("
            SELECT `auc_price`, `user_id`,`time`
            FROM `prices`
            WHERE `user_id` in ( $folows )
            AND `item_id` = '$this->item_id'
            AND `server_group` = '$User->server_group'
            ORDER BY `time` DESC 
            LIMIT 1");
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q = mysqli_fetch_object($qwe);
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
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q = mysqli_fetch_object($qwe);
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
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q = mysqli_fetch_object($qwe);

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
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q = mysqli_fetch_object($qwe);
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
            return true;
        }


        $User->folows();
        if(in_array($this->autor,$User->folows)){
            $this->color = self::COLORS[2];
            $this->tcolor = self::TCOLORS[2];
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
            return self::PriceDataForm('Цена: ');
        }


        self::getColor();



        if($this->autor == $User->id) {

            $text = '<a href="user_prices.php" data-tooltip="Все мои цены">Вы указали: </a>';
        }else {

            $Puser = new User();
            $Puser->byId($this->autor);

            if($Puser->user_nick) {

                $text = '<a href="user_prices.php?puser_id='.$Puser->id.'" data-tooltip="Смотреть его(её) цены">'.$Puser->user_nick.'</a> указал: ';
            }else
                $text = 'Кто-то указал: ';
        }

        self::PriceDataForm($text);


        return true;
    }

    public function PriceDataForm($text): bool
    {
        global $User;
        $Server = new Server($User->id);
        $serverStr = '<span style="color: #3E454C" data-tooltip="Выбрать в настройках"><a href="user_customs.php">' . $Server->name . '</a></span>';
        $timestr = '';
        if($this->price)
            $timestr = date('d.m.Y',strtotime($this->time)) .$serverStr.' <br>';
        else
            $timestr = $serverStr.' <br>';
        ?>
        <span style="color: <?php echo $this->tcolor?>">
            <?php echo $timestr.$text?>
        </span>
        <form id="pr_<?php echo $this->item_id?>">
            <div class="money_area_down">
                <?php self::MoneyLineBL();?>
                <span id="PrOk_<?php echo $this->item_id?>"></span>
            </div>
        </form>
        <?php
        return true;
    }

    public function IsValuta() : bool
    {
        $qwe = qwe("SELECT * FROM valutas WHERE valut_id = '$this->item_id'");
        if($qwe and $qwe->num_rows)
            return true;
        $q = mysqli_fetch_object($qwe);
        $valut_id = $q->valut_id;
        if($valut_id)
            return $valut_id;

        return false;
    }

}