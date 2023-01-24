<?php

namespace api\item;

use api\Category;
use api\craft\Craft;
use PDO;

class Info
{
    public int|null $id;
    /**
     * @var array<Craft>|null
     */
    public array|null $Crafts;
    /**
     * @var array<Item>|null
     */
    public array|null $CraftResults;
    public Craft|null $BestCraft;
    public Category|null $Category;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    private function getResults() : array
    {
        $qwe = qwe("
        SELECT craft_materials.result_item_id FROM craft_materials  
        INNER JOIN items ON items.item_id = craft_materials.result_item_id
        AND items.on_off
        AND craft_materials.item_id = :item_id
        INNER JOIN crafts c on craft_materials.craft_id = c.craft_id
        AND c.on_off
        GROUP BY c.result_item_id
        ",
            ['item_id' => $this->id]
        );
        if(!$qwe or !$qwe->rowCount()){
            return [];
        }
        return $qwe->fetchAll(PDO::FETCH_COLUMN,0);

    }

    public function initResults(): void
    {
        $itemIds = self::getResults();
        if(empty($itemIds)){
            return;
        }
        $Results = Item::searchList($itemIds);
        if(!empty($Results)){
            $this->CraftResults = $Results;
        }
    }

    public function initCategory(int $categId): void
    {
        $Category = Category::byId($categId);
        if(!$Category) return;
        $this->Category = $Category;
    }

    public static function byId(int $id) :  self
    {
        $Info = new self($id);
        $Info->initResults();
        return $Info;
    }
}