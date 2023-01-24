<?php
class PackProfit
{

    private int $valut_id;
    private int $craft_price;
    private int|float $labor_total;
    private int $finalSalary;


    public int $finalGoldSalary  = 0;
    public int $profit       = 0;
    public int $profitOr     = 0;


    public function __construct(
        int $finalSalary,
        int $valut_id,
        int $craft_price,
        int|float $labor_total
    )
    {
        $this->finalSalary = $finalSalary;
        $this->craft_price = $craft_price;
        $this->valut_id    = $valut_id ?? 500;
        $this->labor_total = $labor_total;
        $this->finalGoldSalary = $finalSalary;

        self::profit();
        self::profitOr();
    }



    private function valutConvert() : int
    {
        global $coalprice, $shellprice;
        $valutes = [32103=>$coalprice,32106=>$shellprice,500=>1];
        $salary = $valutes[$this->valut_id] * $this->finalSalary;
        return round($salary);
    }

    private function profit() : void
    {
        $finalSalary = $this->finalSalary;
        if($this->valut_id != 500){
            $finalSalary = self::valutConvert();
            $this->finalGoldSalary = $finalSalary;
        }
        $this->profit = $finalSalary - $this->craft_price;
    }

    private function profitOr() : void
    {
        $Price = new Price(2);
        $Price->byMode();
        $orCost = $Price->price;
        $this->profitOr = round(self::valutConvert() / $this->labor_total);
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

}