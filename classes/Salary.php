<?php


class Salary
{
    const  StandartPrem = 2;
    private float $flatSalary = 0;
    public float $factoryPrice = 0;
    public float $finalSalary  = 0;
    public string     $salaryLetter = '';

    public function __construct(
        private int $per,
        private int $siol,
        private int $db_price,
        private int $fresh_per,
        private int $valut_id
    )
    {
        $this->per         = $per ?? 0;
        $this->siol        = $siol ?? 0;
        $this->db_price    = $db_price ?? 0;
        $this->fresh_per   = $fresh_per ?? 0;
        $this->valut_id    = $valut_id ?? 500;

        self::flatSalary();
        self::perSiol();
        self::finalSalary();
        self::salaryLetter();
    }

    private function flatSalary() : void
    {
        $this->flatSalary = round($this->db_price / 130 * 100);

    }

    private function perSiol(): void
    {
        $persiol = $this->flatSalary * ($this->per / 100);
        //$persiol = round($persiol);

        if($this->valut_id == 500){
            $persiol = $persiol * (1 + $this->siol / 100);
            //$persiol = round($persiol);
        }
        $this->factoryPrice = $persiol;
    }

    private function finalSalary() : void
    {
        $salary = $this->factoryPrice * (1 + ($this->fresh_per / 100));
        //$salary = round($salary);
        $salary = $salary * (1 + self::StandartPrem / 100);
        if($this->valut_id !== 500){
            $salary /= 100;
            $this->factoryPrice /= 100;
            $this->flatSalary /= 100;
        }
        $salary = round($salary);
        $this->finalSalary = $salary;
    }

    public function salaryLetter() : string|false
    {
        $finalSalary  = round($this->finalSalary);
        $flatSalary   = round($this->flatSalary);
        $factoryPrice = round($this->factoryPrice);


        $finalSalary  = price_str($finalSalary, $this->valut_id);
        $flatSalary   = price_str($flatSalary, $this->valut_id);
        $factoryPrice = price_str($factoryPrice, $this->valut_id);
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
        $result = ob_get_clean();
        $this->salaryLetter = $result;
        return $result;
    }
}