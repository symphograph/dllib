<?php
qwe("
UPDATE `items` 
SET `craftable` = 0, `ismat` = 0");
qwe("
UPDATE `items` SET `craftable` = 1 
WHERE `item_id` in
(SELECT DISTINCT `result_item_id` 
FROM `crafts`
WHERE `on_off` = 1)");
qwe("
UPDATE `items` 
SET `ismat` = 1 
WHERE `item_id` in
(SELECT DISTINCT `item_id` 
FROM `craft_materials` WHERE `result_item_id` in
(SELECT DISTINCT `result_item_id` FROM `crafts` WHERE `on_off` = 1))");
?>