<?php


class Mat extends Item
{
    public int $result_id = 0;
    public int $need_grade = 1;
    public int $craftId = 0;
    public float $mater_need = 0;
    public int $price = 0;
    public int  $spm2       = 0;
    public int  $isbest     = 0;
    public string $tooltip = '';


    public function byRcost($q)
    {
        global $lost, $User;
        $q = (object) $q;
        if($q->mater_need == 0)
            return false;

        $this->item_id    = $q->id;
        $this->mater_need = $q->mater_need;
        $this->need_grade = $q->mat_grade ?? 1;
        $this->item_name = $q->item_name ?? '';
        $this->valut_id = $q->valut_id ?? 500;
        $this->spm2 = intval($q->spm2);
        $this->is_trade_npc = $q->is_trade_npc;
        $this->price_buy = $q->price_buy;
        $this->craftable = $q->craftable;
        $this->isBuyCraft = parent::isBuyCraft();


        if (self::MatPrice())
            return true;

        if($q->buffer_price and !$this->isBuyCraft) {

            $this->price = $q->buffer_price;
            //echo '<p>откопал в промежуточных: '.$q->item_name.' '.$this->price.'</p>';
            return true;
        }

       if (!$this->craftable)
            $lost[] = $this->item_id;
        return false;
    }

    public function MatPrice() : bool
    {
        $Price = new Price($this->item_id);
        $this->priceData = $Price;

        if($this->item_id == 500){
            $this->price = 1;
            $Price->price = 1;
            $Price->how = 'Константа';
            $this->priceData = $Price;
            return true;
        }

        if($this->isBuyCraft or $this->mater_need < 0){
            $Price->byMode();
            if($Price->price) {
                $this->price = $Price->price;
                $this->priceData = $Price;
                return true;
            }
            
            return false;
        }


        if($this->is_trade_npc and !$this->craftable) {

            if($this->valut_id == 500){

                $this->price = $this->price_buy;
                $Price->price = $this->price_buy;
                $Price->how = 'Куплено у NPC';
                $this->priceData = $Price;
                return true;
            }

            $Price->byMode();
            if($Price->price){

                $this->price = $Price->price;
                $this->priceData = $Price;
                return true;
            }

            $vPrice = new Price($this->valut_id);
            $vPrice->byMode();
            if($vPrice->price){

                $this->price = $vPrice->price * $this->price_buy;
                $Price->price = $this->price;
                $Price->how = 'Куплено у NPC';
                $this->priceData = $Price;
                return true;
            }
            return false;
        }

        if($this->craftable) {

            $Price->byCraft();
            if($Price->price) {
                $this->price = $Price->price;
                $this->priceData = $Price;
                return true;
            }
        }



        $Price->byMode();
        if($Price->price) {
            $this->price = $Price->price;
            $this->priceData = $Price;
            return true;
        }

        return false;
    }

    public function ToolTip($sum,$hsch = true)
    {
        if($this->item_id == 500)
            return $this->item_name.'<br>'.htmlspecialchars(esyprice(round($sum)));

        $matprice = esyprice($this->priceData->price);
        if($hsch){
            $matprice = htmlspecialchars($matprice);
        }

        $this->priceData->getColor();

        if($this->priceData->autor > 1){
            $Puser = new User();
            $Puser->byId($this->priceData->autor);
            $autorName = $Puser->user_nick;
            $autorName = '<span style="color: '.$this->priceData->tcolor.'; text-shadow: 0 0 2px white">'.$autorName.'</span>';
            if($hsch){
                $autorName = htmlspecialchars($autorName);
            }

        }else
            $autorName = '';

        $this->tooltip = $this->item_name.'<br>'.round($sum,4).' шт по<br>'.$matprice.$this->priceData->how.'<br>'.$autorName;
        return $this->tooltip;

    }

    public function clone(Mat $m){
        foreach ($m as $name => $value){
            $this->$name = $value;
        }
    }

}