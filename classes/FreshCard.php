<?php


class FreshCard
{
    public int $item_id = 0;
    public string $packName      = 'Не нашел имя';
    public int    $fresh_id      = 0;
    public string $freshTypeName = 'Тип не определен';
    public string $condition = 'неизвестно';
    public int $fresh_lvl = 0;
    public int $fresh_per = 0;
    public int $beforeNext = 0;
    public string $datetime = '';
    public string $tmp = '';
    public string $file = '';
    public string $master = '';
    public string $owner = '';

    public function insertTodb() : void
    {

        qwe("
        INSERT IGNORE INTO freshCards
        (
         item_id,  
         fresh_id, 
         freshTypeName, 
         `condition`, 
         fresh_lvl, 
         fresh_per, 
         beforeNext, 
         datetime, 
         tmp, 
         file,
         packName,
         master,
         owner
         ) 
        VALUES 
        (
         '$this->item_id',
         '$this->fresh_id',
         '$this->freshTypeName',
         '$this->condition',
         '$this->fresh_lvl',
         '$this->fresh_per',
         '$this->beforeNext',
         '$this->datetime',
         '$this->tmp',
         '$this->file',
         :packName,
         :master,
         :owner
         )
        ",['packName'=> $this->packName, 'master' => $this->master,'owner'=>$this->owner]);
    }
}