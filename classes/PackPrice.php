<?php
class PackPrice
{
    const  StandartPrem = 2;
    private int|float $db_price;
    private int       $per;
    private int       $siol;
    private int       $fresh_per;
    public int        $factoryPrice = 0;
    private int       $flatSalary;
    private int       $perSalary;
    public int        $finalSalary  = 0;
    private int       $valut_id;
    private int       $craft_price;
    public int        $profit       = 0;
    public int        $profitOr     = 0;
    private int       $item_id;
    private int       $laborSum     = 0;

    public function __construct(
        int $item_id,
        int $db_price,
        int $per,
        int $siol,
        int $fresh_per,
        int $valut_id,
        int $craft_price
    )
    {

        $this->per         = $per;
        $this->siol        = $siol;
        $this->item_id     = $item_id;
        $this->db_price    = $db_price;
        $this->fresh_per   = $fresh_per;
        $this->craft_price = $craft_price;
        $this->valut_id    = $valut_id ?? 500;

        self::flatSalary();
        self::perSiol();
        self::finalSalary();
        self::profit();
        self::profitOr();

    }

    private function flatSalary() : void
    {
        $this->flatSalary = round($this->db_price / 130 * 100);
        if($this->valut_id !== 500){
            $this->flatSalary /= 100;
        }
    }


    private function perSiol(): void
    {
        $persiol = $this->flatSalary * ($this->per / 100);
        $persiol = round($persiol);

        if($this->valut_id == 500){
            $persiol = $persiol * (1 + $this->siol / 100);
            $persiol = round($persiol);
        }
        $this->factoryPrice = $persiol;
    }

    private function finalSalary() : void
    {
        $salary = $this->factoryPrice * (1 + $this->fresh_per / 100);
        $salary = round($salary);
        $salary = $salary * (1 + self::StandartPrem / 100);
        $salary = round($salary);
        $this->finalSalary = $salary;
    }

    private function valutConvert() : int
    {
        global $coalprice, $shellprice;
        $valutes = [32103=>$coalprice,32106=>$shellprice];
        $salary = $valutes[$this->valut_id] * $this->finalSalary;
        return round($salary);
    }

    private function profit() : void
    {
        $finalSalary = $this->finalSalary;
        if($this->valut_id != 500){
            $finalSalary = self::valutConvert();
        }
        $this->profit = $finalSalary - $this->craft_price;
    }

    private function profitOr() : void
    {
        $Price = new Price(2);
        $Price->byMode();
        $orCost = $Price->price;
        $this->profitOr = round($this->craft_price / $orCost);
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function SalaryLetter() : string|false
    {
        $finalSalary  = price_str($this->finalSalary, $this->valut_id);
        $flatSalary   = price_str($this->flatSalary, $this->valut_id);
        $factoryPrice = price_str($this->factoryPrice, $this->valut_id);
        $freguency    = 100 + $this->fresh_per;

        ob_start();
        ?>

        <div class="pinfo_row">
            <span class="pharam"><b>Фактическая выручка</b></span>
            <span class="value" data-tooltip="Сколько вы получите из письма"><b><?php echo $finalSalary ?></b></span>
        </div>
        <hr><br>
        <div class="pinfo_row">
            <span class="pharam">Оновная плата</span>
            <span class="value" data-tooltip="Чистыми без всего при 100%"><?php echo $flatSalary ?></span>
        </div>

        <div class="pinfo_row">
            <span class="pharam">Льгота</span>
            <span class="value" data-tooltip="Сиоль"><?php echo $this->siol ?>%</span>
        </div>
        <div class="pinfo_row">
            <span class="pharam">Ставка</span>
            <span class="value" data-tooltip="Текущий процент у торговца"><?php echo $this->per ?>%</span>
        </div>
        <div class="pinfo_row">
            <span class="pharam">Срок годности</span>
            <span class="value" data-tooltip="Свежесть"><?php echo $freguency ?>%</span>
        </div>
        <div class="pinfo_row">
            <span class="pharam">Дополнительная надбавка</span>
            <span class="value">2%</span>
        </div>
        <?php
        if($this->valut_id == 500)
        {
            ?>
            <hr>
            <div class="pinfo_row">
                <span class="pharam">В списке фактории</span>
                <span class="value" data-tooltip="Отображается в списке цен в фактории"><?php echo $factoryPrice?></span>
            </div>
            <?php
        }
        return ob_get_clean();
    }

    public function printPriceData() : void
    {
        $toolip = htmlspecialchars(self::salaryLetter());
        $profit = price_str($this->profit, 500);
        $finalSalary = price_str($this->finalSalary , $this->valut_id);
        $profitOr = price_str($this->profitOr, 500);
        $imgor = '<img src="/img/icons/50/2.png" width="15px" height="15px" alt="imgor"/>';

        ?>
        <div class="pprice" data-tooltip="<?php echo  $toolip?>">
            <?php echo  $finalSalary?>
            <a href="/packpost.php?item_id=<?php echo $this->item_id ?>">
                <img width="15px" src="/img/icons/50/quest/icon_item_quest023.png"/>
            </a>
        </div>
        <div class="pprice">
            <?php echo $profit ?>
            <br>
            <?php echo $profitOr . '/' . $imgor ?>
        </div>
        <?php
    }

}