<?php


class User
{
    public $age;
    public $ismobiledevice;
    public bool $uncustomed;
    public int $id = 0;
    public int $mode;
    public int $orcost = 0;
    public int $server = 9;
    public int $server_group = 2;
    public int $siol = 0;
    public string $avafile;
    public string $avatar = 'img/8001096.png';
    public string $email;
    public string $first_ip;
    public string $first_name;
    public string $fname;
    public string $identy;
    public string $last_ip;
    public string $last_name;
    public string $last_time;
    public string $mailnick;
    public string $time;
    public string $token;
    public string $user_nick;
    public array $profs = [];
    public $agent;
    public bool $isbot = false;
    public array $folows = [];

    public function byId(int $user_id)
    {
        $qwe = qwe("
            SELECT `mailusers`.*,
            `user_servers`.`server`,
            `servers`.`server_group`,
            `servers`.`server_name`
            FROM
            `mailusers`
            LEFT JOIN `user_servers` 
                ON `user_servers`.`user_id` = `mailusers`.`mail_id`
            LEFT JOIN `servers` 
                ON `servers`.`id` = `server`
            WHERE BINARY `mailusers`.`mail_id` = '$user_id'
            ");
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q = mysqli_fetch_object($qwe);
        self::ByQwe($q);

        return true;
    }

    private function newBot($BotName)
    {

        $datetime = date('Y-m-d H:i:s',time());
        $ip = $_SERVER['REMOTE_ADDR'];
        $identy = random_str(12);
        $newid = EmptyIdFinder('mailusers');

        $qwe = qwe("
        INSERT INTO `mailusers`
        (`mail_id`, `identy`, `ip`, `time`, `last_ip`, `last_time`,`first_name`,`email`)
        VALUES
        ('$newid' ,'$identy', '$ip', '$datetime','$ip','$datetime','$BotName','$BotName')
        ");
        if(!$qwe)
            return false;

        self::byIdenty($identy);
        return $identy;
    }

    private function newUser()
    {
        $datetime = date('Y-m-d H:i:s',time());
        $identy = random_str(12);
        $ip = $_SERVER['REMOTE_ADDR'];
        $newid = EmptyIdFinder('mailusers');
        $qwe = qwe("
            INSERT INTO `mailusers`
            (`mail_id`, `identy`, `ip`, `time`, `last_ip`, `last_time`)
            VALUES
            ('$newid' ,'$identy', '$ip', '$datetime','$ip','$datetime')
            ");
        if(!$qwe)
            return false;

        self::byIdenty($identy);
        return $identy;
    }

    public function isBot()
    {
        $BotName = is_bot();
        if(!$BotName)
            return false;

        $this->isbot = true;
        $qwe = qwe("SELECT * FROM `mailusers` WHERE `email` = '$BotName'");
        if(!$qwe or !$qwe->num_rows){
            //Новый бот. Записываем.
            self::newBot($BotName);
            return true;
        }
        $q = mysqli_fetch_object($qwe);

        //Если бот уже знакомый, обновляем
        $ip = $_SERVER['REMOTE_ADDR'];
        $datetime = date('Y-m-d H:i:s',time());
        qwe("
            UPDATE `mailusers` SET
            `last_ip` = '$ip',
            `last_time` = '$datetime'
            WHERE BINARY `identy` = '$q->identy'");

        self::byIdenty($q->identy);
        return true;
    }

    private function isCookieble() : bool
    {
        if(!empty($_COOKIE["test"]))
        {
            if(!empty($_GET["cookie"]))
            {
                header("Location: {$_SERVER['SCRIPT_NAME']}");
                die();
            }
            //setcookie("test","",time() - 3600);
            return true;
        }


        if(empty($_GET["cookie"]))
        {
            setcookie("test","1");
            header("Location: {$_SERVER['SCRIPT_NAME']}?cookie=1");
            exit();
        }

        exit('<meta charset="utf-8"><h3>Для корректной работы приложения необходимо включить cookies</h3>');
    }

    public function check() : bool
    {
        //проверяем, помним ли юзера
        //если нет, запоминаем
        $unix_time = time();
        $datetime = date('Y-m-d H:i:s',$unix_time);
        $ip = $_SERVER['REMOTE_ADDR'];

        if(self::isBot())
            return true;

        if(empty($_COOKIE['identy']))
        {
            if(!self::isCookieble())
                return false;

            self::newUser();
            return true;
        }


        if(self::byIdenty($_COOKIE['identy']))
        {
            qwe("
            UPDATE `mailusers` SET
            `last_ip` = '$ip',
            `last_time` = '$datetime'
            WHERE BINARY `identy` = '$this->identy'");

            DeviceMark($this->id,$unix_time);
            return true;
        }

        //Кука есть, данных в базе нет.
        setcookie ("identy", "", time()-3600);
        //echo 'Authorization ERROR';
        header("Refresh: 0");
        die();
    }

    public function byIdenty(string $identy = '') : bool
    {

        if(empty($identy))
        {
            if(empty($_COOKIE['identy']))
                return false;
            $identy = OnlyText($_COOKIE['identy']);

            if(iconv_strlen($identy) != 12)
                return false;
        }

        $qwe = qwe("
		SELECT `mailusers`.*,
		`user_servers`.`server`,
		`servers`.`server_group`,
		`servers`.`server_name`
		FROM
		`mailusers`
		LEFT JOIN `user_servers` ON `user_servers`.`user_id` = `mailusers`.`mail_id`
		LEFT JOIN `servers` ON `servers`.`id` = `server`
		WHERE BINARY `mailusers`.`identy` = '$identy'
		");
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q = mysqli_fetch_object($qwe);
        self::ByQwe($q);

        $this->uncustomed = ProfUnEmper($this->id);
        $this->agent = get_browser(null, true);
        $this->ismobiledevice = $this->agent['ismobiledevice'];

        $unix_time = time();
        $cooktime = $unix_time+60*60*24*365*5;
        setcookie('identy',$identy,$cooktime,'/','',true,true);
        return true;
    }

    public function authByEmail() : bool
    {
        if(!$this->email)
            return false;

        $qwe = qwe("
        SELECT * 
        from `mailusers` 
        where `email` = '$this->email'
        ");
        if(!$qwe or !$qwe->num_rows) {
           return self::regNewMail();
        }

        $q = mysqli_fetch_object($qwe);

        return self::authKnownUser($q);
    }

    private function authKnownUser($q)
    {
        //Заменяем текущий identy тем, что привязан к мылу.
        $this->identy = $q->identy;

        //Очищаем ненужный mail_id, который дали ему при установке куки.
        qwe("DELETE FROM `mailusers` WHERE `mail_id` = '$this->id'");

        $ip = $_SERVER['REMOTE_ADDR'];
        $qwe = qwe("UPDATE `mailusers` SET 
            `first_name` = '$this->fname', 
            `last_name` = '$this->last_name', 
            `avatar` = '$this->avatar', 
            `mailnick` = '$this->mailnick', 
            `last_time` = NOW(), 
            `last_ip` = '$ip'
            WHERE `mail_id` = '$q->mail_id'
            ");
        if(!$qwe)
            return false;

        $cooktime = time() + (60*60*24*365*5);
        setcookie('identy',$this->identy,$cooktime,'/','',true,true);
        return true;
    }

    private function regNewMail()
    {
        $qwe = qwe("
            UPDATE `mailusers` SET 
            `first_name` = '$this->fname', 
            `last_name` = '$this->last_name', 
            `avatar` = '$this->avatar', 
            `mailnick` = '$this->mailnick', 
            `last_time` = '$this->last_time', 
            `last_ip` = '$this->last_time',
            `email` = '$this->email'
            WHERE `mail_id` = '$this->id'
            ");
        if(!$qwe)
            return false;

        return true;
    }

    private function ByQwe(object $q)
    {
        $this->age = $q->age ?? 0;
        $this->avafile = $q->avafile ?? '';
        $this->avatar = $q->avatar ?? '';
        $this->email = $q->email ?? false;
        $this->first_ip = $q->ip;
        $this->first_name = $q->first_name ?? 'Незнакомец';
        $this->fname = $q->first_name ?? 'Незнакомец';
        $this->id = $q->mail_id;
        $this->identy = $q->identy;
        $this->last_ip = $q->last_ip ?? '';
        $this->last_name = $q->last_name ?? '';
        $this->last_time = $q->last_time ?? '';
        $this->mailnick = $q->mailnick ?? '';
        $this->mode = $q->mode ?? 1;
        $this->server = $q->server ?? 9;
        $this->server_group = $q->server_group ?? 2;
        $this->siol = intval($q->siol);
        $this->time = $q->time;
        $this->token = $q->token ?? '';
        $this->user_nick = $q->user_nick ?? '';

        if($q->email) {
            if(!$q->user_nick)
                $this->user_nick = NickAdder($q->mail_id);
            else
                $this->user_nick = $q->user_nick;
        }
    }

    public function iniAva()
    {
        if($this->avafile and file_exists($_SERVER['DOCUMENT_ROOT'].'/img/avatars/'.$this->avafile)) {
            $this->avatar = 'img/avatars/' . $this->avafile;
            return $this->avatar;
        }


        include_once dirname($_SERVER['DOCUMENT_ROOT']).'/functions/filefuncts.php';
        //Пробуем получить с мыла по ссылке
        $avafile = AvaGetAndPut($this->avatar,$this->identy);
        if($avafile) {
            $this->avatar = 'img/avatars/'.$avafile;
            return $this->avatar;
        }


        $this->avatar = 'img/8001096.png';
        return $this->avatar;
    }

    public function ServerSelect()
    {
        $qwe = qwe("
        SELECT * 
        FROM `servers` 
        ORDER BY 
		server_group, server_name
	");

        $tooltip = '';
        ?>
        <form method="POST" data-tooltip="<?php echo $tooltip?>" action="serverchange.php" name="server">
            <select name="serv" id="server" class="server" onchange="this.form.submit()">
                <?php SelectOpts($qwe, 'id', 'server_name', $this->server, false); ?>
            </select>
        </form>
        <?php
    }

    public function persRow($q)
    {
        global $User;
        $q->server_group = $User->server_group;
        self::ByQwe($q);
        self::iniAva();



        $chk = '';
        if($q->isfolow) $chk = 'checked';
        ?>
        <div class="persrow">

            <div class="nicon_out">

                <a href="user_prices.php?puser_id=<?php echo $this->id?>" data-tooltip="Смотреть цены">
                <label class="navicon" for="<?php echo $this->id?>" style="background-image: url(<?php echo $this->avatar?>);"></label>
                </a>
                <div class="persnames">
                    <div class="mailnick"><b><?php echo $this->user_nick?></b></div>
                    <div class="mailnick"><?php echo 'Записей: '.$q->cnt?></div>
                </div>
            </div>
            <div class="lastprice">
                <div class="mailnick"><?php echo 'Последняя: '.date('d.m.Y',strtotime($q->mtime)) ?>
                    <?php self::LastUserPriceCell();?>
                </div>
            </div>
            <div class="folow_check">
                <?php
                if($this->id != $User->id)
                {
                    ?>
                    <label for="folw_<?php echo $this->id?>">Доверять ценам
                        <input type="checkbox" <?php echo $chk?> name="folow[<?php echo $this->id?>]" id="folw_<?php echo $this->id?>" value="1">
                    </label>
                    <?php
                }
                ?>
                <div class="mailnick"><?php if($q->flws) echo 'Доверяют: '.$q->flws?></div>
            </div>
        </div>
        <?php
    }

    private function LastUserPriceCell()
    {
        $qwe = qwe("
        SELECT 
        `prices`.`auc_price`,
        `prices`.`item_id`,
        `items`.`item_name`,
        `items`.`icon`,
        `items`.`basic_grade`
        FROM `prices` 
        INNER JOIN `items` ON `items`.`item_id` = `prices`.`item_id`
        AND `prices`.`user_id` = '$this->id'
        AND `prices`.`server_group` = '$this->server_group'
        AND `prices`.`item_id` NOT in (".implode(',',IntimItems()).")
        ORDER BY `time` DESC 
        LIMIT 1
        ");
        if(!$qwe or $qwe->num_rows == 0)
            return false;
        $q = mysqli_fetch_object($qwe);

        PriceCell2($q->item_id,$q->auc_price,$q->item_name,$q->icon,$q->basic_grade);

        return true;
    }

    /**
     * @param $user_id
     * @return array
     * возвращает массив id юзеров, на чьи цены подписан юзер
     */
    public function folows() : array
    {
        if(count($this->folows))
            return $this->folows;

        $qwe = qwe("
            SELECT `folow_id` FROM `folows`
            WHERE `user_id` = '$this->id'
            ");
        if(!$qwe or !$qwe->num_rows)
            return [];

        $folows = [];
        foreach($qwe as $q) {
            $folows[] = $q['folow_id'];
        }
        if(count($folows)){
            $this->folows = $folows;
            return $this->folows;
        }
        return [];
    }

    public function orCost() : int
    {
        if($this->orcost)
            return $this->orcost;

        $Price = new Price(2);
        $Price->Solo();
        $this->orcost = intval($Price->price);
        if(!$this->orcost)
            $this->orcost  = 300;
        return $this->orcost;
    }

    function IsFolow($folow_id)
    {
        $qwe = qwe("
            SELECT * FROM folows 
            WHERE `user_id` = '$this->id'
            AND `folow_id` = '$folow_id'
            ");
        if($qwe and $qwe->num_rows > 0)
            return true;
        return false;
    }

}