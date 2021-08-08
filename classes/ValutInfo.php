<?php

class ValutInfo
{
    public array $transItems = [];
    public array $monetisationData = [];
    public int $median = 0;

    public function __construct(
        public int $valut_id,
        public string $icon = ''
    )
    {
        $this->transItems = self::transItems();
        self::monetisationData();

    }

    public function transItems() : array
    {
        $qwe = qwe("
        SELECT 
        item_id, 
        item_name, 
        price_buy, 
        icon,
        basic_grade as grade
        FROM items 
        WHERE !personal
        AND valut_id = :valut_id
        AND is_trade_npc
        AND on_off
        ORDER BY categ_id",['valut_id' => $this->valut_id]);
        if(!$qwe || !$qwe->rowCount()){
            return [];
        }
        foreach ($qwe as $q){
            $q = (object) $q;

            $cubik = new Cubik(
                id: $q->item_id,
                icon: $q->icon,
                grade: $q->grade,
                tooltip: $q->item_name
            );

            $arr[] = $cubik;

        }
        return $arr;
    }

    public function monetisationData() : bool
    {
        $max = self::maxVal();
        $qwe = qwe("
            SELECT 
            item_id, item_name, price_buy, icon, basic_grade as grade
            FROM items
            WHERE !personal
            AND valut_id = :valut_id
            AND is_trade_npc
            AND on_off
            AND price_buy < :max
            ORDER BY categ_id",
            ['valut_id' => $this->valut_id,'max' => $max]
        );
        if(!$qwe || !$qwe->rowCount())
            return false;

        foreach ($qwe as $q){
            $q = (object) $q;
            $Price = new Price($q->item_id);
            $Price->byMode();
            if(!$Price->price)
                continue;

            $val_pr = round($Price->price / $q->price_buy * 0.9);
            if(!$val_pr)
                continue;
            $this->monetisationData[] = new Cubik(
                id: $q->item_id,
                icon: $q->icon,
                grade: $q->grade,
                tooltip: $q->item_name,
                value: $val_pr
            );
            $formedian[$q->item_id] = $val_pr;
        }

        array_multisort(
            array_column($this->monetisationData,
                         'value'),
            $this->monetisationData
        );

        $this->median = median($formedian ?? []);

        return true;
    }

    private function maxVal()
    {
        $maximums = [
            3 => 12001,
            4 => 8000,
            6 => 501,
            23633 => 2000
        ];
        if(isset($maximums[$this->valut_id]))
            return $maximums[$this->valut_id];
        else
            return 100000;
    }

}