<?php


class Mat extends Item
{
    public int $result_id = 0;
    public int $need_grade = 1;
    public int $craftId = 0;
    public float $mater_need = 0;
    public int $price = 0;
    public int $spm2 = 0;
    public bool $is_buyable = false;
    public int $isbest = 0;



    public function byRcost($q)
    {
        global $lost, $User;
        $q = (object) $q;
        if($q->mater_need == 0)
            return false;

        $this->id = $q->id;
        $this->mater_need = $q->mater_need;
        $this->need_grade = $q->mat_grade ?? 1;
        $this->name = $q->item_name ?? '';
        $this->valut_id = $q->valut_id ?? 500;
        $this->spm2 = intval($q->spm2);
        $this->is_trade_npc = $q->is_trade_npc;
        $this->price_buy = $q->price_buy;
        $this->craftable = $q->craftable;
        $this->is_buyable = ($q->isbest == 3);


        if (self::MatPrice())
            return true;

        if($q->buffer_price and !$this->is_buyable) {

            $this->price = $q->buffer_price;
            //echo '<p>откопал в промежуточных: '.$q->item_name.' '.$this->price.'</p>';
            return true;
        }


        $lost[] = $this->id;
        return false;
    }

    public function MatPrice() : bool
    {
        $Price = new Price($this->id);
        $this->priceData = $Price;

        if($this->id == 500){
            $this->price = 1;
            $Price->price = 1;
            $Price->how = 'Константа';
            $this->priceData = $Price;
            return true;
        }

        if($this->is_buyable or $this->mater_need < 0){
            $Price->byMode();
            if($Price->price) {
                $this->price = $Price->price;
                $this->priceData = $Price;
                return true;
            }
            
            return false;
        }


        if($this->is_trade_npc) {

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

    public function ToolTip($sum)
    {
        if($this->id == 500)
            return $this->name.'<br>'.htmlspecialchars(esyprice(round($sum)));

        $matprice = esyprice($this->priceData->price);
        $matprice = htmlspecialchars($matprice);
        $this->priceData->getColor();

        if($this->priceData->autor > 1){
            $Puser = new User();
            $Puser->byId($this->priceData->autor);
            $autorName = $Puser->user_nick;
            $autorName = '<span style="color: '.$this->priceData->tcolor.'; text-shadow: 0 0 2px white">'.$autorName.'</span>';
            $autorName = htmlspecialchars($autorName);
        }else
            $autorName = '';


        return $this->name.'<br>'.round($sum,4).' шт по<br>'.$matprice.$this->priceData->how.'<br>'.$autorName;

    }

}