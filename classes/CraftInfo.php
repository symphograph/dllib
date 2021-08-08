<?php

class CraftInfo
{
    private const flowers = [
        2178, 3564, 3622, 3627, 3628, 3659, 3667, 3671, 3680, 3684, 3685, 3711, 3713, 8009, 14629, 14630, 14631, 16268, 16273, 16290
    ];
    public string    $craft_name    = '';
    public int       $profit        = 0;
    public int       $profitor      = 0;
    public int|float $labor_total   = 0;
    public int       $item_id       = 0;
    public int       $mins          = 0;
    public string    $sptime        = '';
    public int       $prof_need     = 0;
    public string    $prof_name     = 'Прочее';
    public string    $dood_name     = '';
    public int       $craft_price   = 0;
    public int       $spmu          = 0;
    public int       $isGoldable;
    public int       $labor_need2   = 0;
    public int|float $labor_single  = 0;
    public array     $mats          = [];
    public int       $result_amount = 0;
    public int       $matMoney      = 0;
    public int $prefType = 0;
    public int $craft_id = 0;
    private Price    $Price;




    public function __construct(
        private Craft $Craft,
        private Item $Item,
    )
    {
        global $User;
        if(!isset($User)){
            return false;
        }
        $this->Craft->InitForUser();
        if(!$Craft->setCountedData()){
            return false;
        }

        $Prof = new Prof();
        $Prof->InitForUser($Craft->prof_id);


        $this->item_id    = $this->Item->item_id;
        $this->craft_id = $this->Craft->craft_id;
        $this->craft_name = $Craft->rec_name ?? $this->Item->item_name;
        $this->prof_need  = $Craft->prof_need ?? 0;
        $this->prof_name  = $Prof->name;
        $this->dood_name  = $this->Craft->dood_name;

        $this->labor_total  = floatval($Craft->labor_total);
        $this->labor_total  = round($this->labor_total, 2);
        $this->labor_need2  = $this->Craft->labor_need2;
        $this->labor_single = $this->Craft->labor_single;

        $u_amount = $_POST['u_amount'] ?? 0;
        $u_amount = intval($u_amount);
        if(!$u_amount){
            $u_amount = 1;
        }

        $this->Price = new Price($this->Item->item_id);
        $this->Price->byMode();

        $this->mins        = $this->Craft->mins;
        $this->sptime      = SPTime($this->Craft->mins);
        $this->spmu        = $this->Craft->spmu;
        $this->craft_price = $this->Craft->craft_price;
        $this->result_amount     = $this->Craft->result_amount;
        $this->isGoldable  = $this->Item->isGoldable;
        $this->prefType = $this->Craft->isbest;

        self::countProfit();

        $this->mats = $this->Craft->mats;
        self::matsData();

        return true;
    }

    private function countProfit() : void
    {
        if(!$this->Price->price){
            return;
        }
        $this->profit = round($this->Price->price*0.9 - $this->Craft->craft_price);

        if($this->labor_total) {
            $this->profitor = round($this->profit/$this->labor_total);
        }

    }

    public function matsData() : void
    {
        $mArr = [];

        foreach ($this->mats as $m){
            $Mat =  new Mat();
            $Mat->clone($m);
            if($Mat->item_id == 500){
                $this->matMoney = $Mat->mater_need;
                continue;
            }

            $Mat->MatPrice();

            if(in_array($Mat->item_id,self::flowers) and $Mat->mater_need < 0){
                $Mat->priceData->price = $this->craft_price;
                $Mat->priceData->autor = 1;
                $Mat->priceData->how = 'Себестоимость (крафт)';
            }

            $Mat->ToolTip($Mat->mater_need,0);

            $mArr[] = $Mat;
        }
        $this->mats = $mArr;
    }

}