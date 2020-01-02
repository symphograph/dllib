/*
 Navicat Premium Data Transfer

 Source Server         : DL_55
 Source Server Type    : MySQL
 Source Server Version : 50721
 Source Host           : 87.236.19.55:3306
 Source Schema         : graflastor_55

 Target Server Type    : MySQL
 Target Server Version : 50721
 File Encoding         : 65001

 Date: 02/01/2020 21:24:44
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for New_items55
-- ----------------------------
DROP TABLE IF EXISTS `New_items55`;
CREATE TABLE `New_items55`  (
  `item_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for atomization
-- ----------------------------
DROP TABLE IF EXISTS `atomization`;
CREATE TABLE `atomization`  (
  `item_id` int(11) UNSIGNED NOT NULL,
  `atom_to` int(11) UNSIGNED NULL DEFAULT NULL,
  `atoms` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for chars
-- ----------------------------
DROP TABLE IF EXISTS `chars`;
CREATE TABLE `chars`  (
  `char_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `exes` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for craft_all_mats
-- ----------------------------
DROP TABLE IF EXISTS `craft_all_mats`;
CREATE TABLE `craft_all_mats`  (
  `user_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `craft_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `mat_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `mater_need` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `mater_need2` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for craft_all_trash
-- ----------------------------
DROP TABLE IF EXISTS `craft_all_trash`;
CREATE TABLE `craft_all_trash`  (
  `user_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `craft_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `mat_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `mater_need` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for craft_buffer
-- ----------------------------
DROP TABLE IF EXISTS `craft_buffer`;
CREATE TABLE `craft_buffer`  (
  `user_id` int(10) UNSIGNED NOT NULL,
  `craft_id` int(10) UNSIGNED NOT NULL,
  `craft_price` bigint(20) NULL DEFAULT NULL,
  `matspm` bigint(20) NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`, `craft_id`) USING BTREE,
  CONSTRAINT `crbuf_key` FOREIGN KEY (`user_id`) REFERENCES `mailusers` (`mail_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for craft_buffer2
-- ----------------------------
DROP TABLE IF EXISTS `craft_buffer2`;
CREATE TABLE `craft_buffer2`  (
  `user_id` int(11) UNSIGNED NOT NULL,
  `craft_id` int(10) UNSIGNED NOT NULL,
  `craft_price` bigint(20) NULL DEFAULT NULL,
  `spm` int(11) NOT NULL DEFAULT 0,
  `item_id` int(10) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`, `craft_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for craft_groups
-- ----------------------------
DROP TABLE IF EXISTS `craft_groups`;
CREATE TABLE `craft_groups`  (
  `item_id` int(11) UNSIGNED NOT NULL,
  `craft_id` int(11) UNSIGNED NOT NULL,
  `item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `amount` int(11) NULL DEFAULT NULL,
  `group_id` int(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`, `craft_id`) USING BTREE,
  INDEX `names`(`item_name`) USING BTREE,
  CONSTRAINT `craft_groups_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `items` (`item_name`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for craft_lvls
-- ----------------------------
DROP TABLE IF EXISTS `craft_lvls`;
CREATE TABLE `craft_lvls`  (
  `lvl` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL,
  `item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`lvl`, `item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for craft_materials
-- ----------------------------
DROP TABLE IF EXISTS `craft_materials`;
CREATE TABLE `craft_materials`  (
  `craft_id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL,
  `result_item_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `mater_need` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `result_item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `mat_grade` tinyint(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`craft_id`, `item_id`) USING BTREE,
  INDEX `result_item_name`(`result_item_name`) USING BTREE,
  INDEX `item_name`(`item_name`) USING BTREE,
  INDEX `item_id`(`item_id`) USING BTREE,
  INDEX `mat_names`(`item_id`, `item_name`) USING BTREE,
  INDEX `result_item_id`(`result_item_id`) USING BTREE,
  INDEX `result_names`(`result_item_id`, `result_item_name`) USING BTREE,
  CONSTRAINT `craft_materials_ibfk_1` FOREIGN KEY (`craft_id`) REFERENCES `crafts` (`craft_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for craft_tree
-- ----------------------------
DROP TABLE IF EXISTS `craft_tree`;
CREATE TABLE `craft_tree`  (
  `root_craft_id` int(10) UNSIGNED NOT NULL,
  `craft_id` int(10) UNSIGNED NOT NULL,
  `deep` int(10) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`root_craft_id`, `craft_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for crafts
-- ----------------------------
DROP TABLE IF EXISTS `crafts`;
CREATE TABLE `crafts`  (
  `craft_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rec_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `dood_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `dood_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `result_item_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `result_item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `labor_need` int(11) UNSIGNED NULL DEFAULT NULL,
  `profession` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `prof_need` int(11) NULL DEFAULT NULL,
  `result_amount` int(11) NOT NULL DEFAULT 1,
  `on_off` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `isbottom` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `dood_group` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `deep` tinyint(3) UNSIGNED NULL DEFAULT 0,
  `my_craft` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `craft_time` int(10) UNSIGNED NULL DEFAULT 0,
  `prof_id` tinyint(2) UNSIGNED NOT NULL DEFAULT 25,
  `grade` tinyint(3) NOT NULL DEFAULT 0,
  `mins` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `spm` int(11) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`craft_id`) USING BTREE,
  INDEX `result_item_name`(`result_item_name`) USING BTREE,
  INDEX `result_item_id`(`result_item_id`) USING BTREE,
  INDEX `on_off`(`on_off`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8000788 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for deleted_crafts50
-- ----------------------------
DROP TABLE IF EXISTS `deleted_crafts50`;
CREATE TABLE `deleted_crafts50`  (
  `craft_id` bigint(20) NOT NULL,
  PRIMARY KEY (`craft_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for doods
-- ----------------------------
DROP TABLE IF EXISTS `doods`;
CREATE TABLE `doods`  (
  `dood_id` int(11) UNSIGNED NOT NULL,
  `dood_name` varchar(254) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`dood_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ench_it_prices
-- ----------------------------
DROP TABLE IF EXISTS `ench_it_prices`;
CREATE TABLE `ench_it_prices`  (
  `user_id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL,
  `grade` tinyint(2) UNSIGNED NOT NULL,
  `price` int(11) UNSIGNED NULL DEFAULT NULL,
  `time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`, `item_id`, `grade`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enchant_cost
-- ----------------------------
DROP TABLE IF EXISTS `enchant_cost`;
CREATE TABLE `enchant_cost`  (
  `mail_id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) NOT NULL,
  `grade` tinyint(2) UNSIGNED NOT NULL,
  `cost` int(11) UNSIGNED NULL DEFAULT NULL,
  `time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`mail_id`, `item_id`, `grade`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for eq-typ
-- ----------------------------
DROP TABLE IF EXISTS `eq-typ`;
CREATE TABLE `eq-typ`  (
  `eq-types` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ev_banks_log
-- ----------------------------
DROP TABLE IF EXISTS `ev_banks_log`;
CREATE TABLE `ev_banks_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bank_id` tinyint(3) UNSIGNED NULL DEFAULT NULL,
  `bill` int(11) NULL DEFAULT NULL,
  `time` timestamp(0) NULL DEFAULT NULL,
  `editor` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `bank`(`bank_id`) USING BTREE,
  CONSTRAINT `ev_banks_log_ibfk_1` FOREIGN KEY (`bank_id`) REFERENCES `event_banks` (`bank_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 383 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ev_members
-- ----------------------------
DROP TABLE IF EXISTS `ev_members`;
CREATE TABLE `ev_members`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `member_id` int(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `event_id`(`event_id`) USING BTREE,
  CONSTRAINT `ev_members_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 2407 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ev_membs_add
-- ----------------------------
DROP TABLE IF EXISTS `ev_membs_add`;
CREATE TABLE `ev_membs_add`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sh_nick` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`, `sh_nick`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 100087 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ev_salary
-- ----------------------------
DROP TABLE IF EXISTS `ev_salary`;
CREATE TABLE `ev_salary`  (
  `member_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bank_id` tinyint(3) UNSIGNED NOT NULL,
  `salary` int(6) UNSIGNED NULL DEFAULT 0,
  PRIMARY KEY (`member_id`, `bank_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 503 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ev_start
-- ----------------------------
DROP TABLE IF EXISTS `ev_start`;
CREATE TABLE `ev_start`  (
  `start_id` tinyint(3) UNSIGNED NOT NULL,
  `start_date` date NULL DEFAULT NULL,
  `end_date` date NULL DEFAULT NULL,
  PRIMARY KEY (`start_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for event_banks
-- ----------------------------
DROP TABLE IF EXISTS `event_banks`;
CREATE TABLE `event_banks`  (
  `bank_id` tinyint(3) UNSIGNED NOT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `bill` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`bank_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for event_types
-- ----------------------------
DROP TABLE IF EXISTS `event_types`;
CREATE TABLE `event_types`  (
  `event_t_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ev_categ` tinyint(3) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`event_t_id`) USING BTREE,
  INDEX `ev_categ`(`ev_categ`) USING BTREE,
  CONSTRAINT `event_types_ibfk_1` FOREIGN KEY (`ev_categ`) REFERENCES `event_banks` (`bank_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for events
-- ----------------------------
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_t_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `editor` int(11) UNSIGNED NULL DEFAULT NULL,
  `leader` int(11) NULL DEFAULT NULL,
  `time` timestamp(0) NULL DEFAULT NULL,
  `date` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 211 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for folows
-- ----------------------------
DROP TABLE IF EXISTS `folows`;
CREATE TABLE `folows`  (
  `user_id` int(10) UNSIGNED NOT NULL,
  `folow_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`, `folow_id`) USING BTREE,
  INDEX `folow_key`(`folow_id`) USING BTREE,
  CONSTRAINT `folow_key` FOREIGN KEY (`folow_id`) REFERENCES `mailusers` (`mail_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for forup_grades
-- ----------------------------
DROP TABLE IF EXISTS `forup_grades`;
CREATE TABLE `forup_grades`  (
  `item_id` int(11) UNSIGNED NOT NULL,
  `item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `forup_grade` tinyint(2) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for fresh_data
-- ----------------------------
DROP TABLE IF EXISTS `fresh_data`;
CREATE TABLE `fresh_data`  (
  `fresh_lvl` tinyint(3) UNSIGNED NOT NULL,
  `fresh_type` tinyint(3) UNSIGNED NOT NULL,
  `fresh_tstart` int(10) UNSIGNED NULL DEFAULT NULL,
  `fresh_tstop` int(10) UNSIGNED NULL DEFAULT NULL,
  `fresh_per` tinyint(3) NULL DEFAULT NULL,
  PRIMARY KEY (`fresh_lvl`, `fresh_type`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for fresh_lvls
-- ----------------------------
DROP TABLE IF EXISTS `fresh_lvls`;
CREATE TABLE `fresh_lvls`  (
  `fresh_lvl` tinyint(3) UNSIGNED NOT NULL,
  `fresh_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`fresh_lvl`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for grades
-- ----------------------------
DROP TABLE IF EXISTS `grades`;
CREATE TABLE `grades`  (
  `id` tinyint(2) UNSIGNED NOT NULL,
  `gr_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `color` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `chance_craft` int(5) NULL DEFAULT NULL,
  `chance_obsid` int(5) NULL DEFAULT NULL,
  `chance_quest` int(5) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for hide_cl
-- ----------------------------
DROP TABLE IF EXISTS `hide_cl`;
CREATE TABLE `hide_cl`  (
  `craft_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`craft_id`, `user_id`) USING BTREE,
  INDEX `hidecrft_key`(`user_id`) USING BTREE,
  CONSTRAINT `hidecrft_key` FOREIGN KEY (`user_id`) REFERENCES `mailusers` (`mail_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for identy
-- ----------------------------
DROP TABLE IF EXISTS `identy`;
CREATE TABLE `identy`  (
  `identy` char(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `time` datetime(0) NULL DEFAULT NULL,
  `last_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `last_time` datetime(0) NULL DEFAULT NULL,
  `mail_id` int(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`identy`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for item_categories
-- ----------------------------
DROP TABLE IF EXISTS `item_categories`;
CREATE TABLE `item_categories`  (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `item_group` tinyint(3) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for item_groups
-- ----------------------------
DROP TABLE IF EXISTS `item_groups`;
CREATE TABLE `item_groups`  (
  `id` int(8) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `visible_ui` int(1) NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sgr_id` int(10) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for item_subgroups
-- ----------------------------
DROP TABLE IF EXISTS `item_subgroups`;
CREATE TABLE `item_subgroups`  (
  `sgr_id` int(8) NOT NULL,
  `sgr_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `visible_ui` int(1) NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`sgr_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for items
-- ----------------------------
DROP TABLE IF EXISTS `items`;
CREATE TABLE `items`  (
  `item_id` int(11) UNSIGNED NOT NULL COMMENT 'ID предмета',
  `price_buy` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Цена покупки у NPC',
  `price_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Валюта',
  `valut_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT 'ID Валюты',
  `price_sale` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Цена продажи NPC',
  `is_trade_npc` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Продаётся у NPC',
  `category` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Категория',
  `item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Имя предмета',
  `description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Описание',
  `on_off` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Вкл-Выкл',
  `personal` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Персональный',
  `craftable` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Крафтабельный',
  `ismat` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Нужен для рецепта',
  `categ_id` tinyint(3) UNSIGNED NULL DEFAULT NULL COMMENT 'ID Категории',
  `categ_pid` tinyint(3) UNSIGNED NULL DEFAULT NULL,
  `slot` tinyint(2) UNSIGNED NULL DEFAULT NULL COMMENT 'Слот',
  `roll_group` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `lvl` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `inst` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `basic_grade` tinyint(2) UNSIGNED NULL DEFAULT NULL,
  `forup_grade` tinyint(2) UNSIGNED NULL DEFAULT NULL,
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `md5_icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`) USING BTREE,
  INDEX `item_id`(`item_id`, `item_name`) USING BTREE,
  INDEX `item_name`(`item_name`) USING BTREE,
  INDEX `craftable`(`craftable`) USING BTREE,
  INDEX `ismat`(`ismat`) USING BTREE,
  INDEX `roll_group`(`roll_group`) USING BTREE,
  INDEX `lvl`(`lvl`) USING BTREE,
  INDEX `categ_id`(`categ_id`) USING BTREE,
  INDEX `price_type`(`price_type`) USING BTREE,
  INDEX `off`(`on_off`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for mailusers
-- ----------------------------
DROP TABLE IF EXISTS `mailusers`;
CREATE TABLE `mailusers`  (
  `mail_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mail_id-old` bigint(30) NULL DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `age` int(11) NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `time` datetime(0) NULL DEFAULT NULL,
  `last_time` datetime(0) NULL DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `mailnick` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `last_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `identy` char(12) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `token` char(12) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `siol` tinyint(1) NOT NULL DEFAULT 0,
  `user_nick` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `avafile` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `mode` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`mail_id`) USING BTREE,
  UNIQUE INDEX `ident_uniq`(`identy`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11713 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for npnicks
-- ----------------------------
DROP TABLE IF EXISTS `npnicks`;
CREATE TABLE `npnicks`  (
  `id` int(11) NOT NULL,
  `name` varchar(254) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pack_prices
-- ----------------------------
DROP TABLE IF EXISTS `pack_prices`;
CREATE TABLE `pack_prices`  (
  `item_id` int(10) UNSIGNED NOT NULL,
  `zone_id` int(10) UNSIGNED NOT NULL,
  `zone_to` int(10) UNSIGNED NOT NULL,
  `pack_price` int(10) NULL DEFAULT NULL,
  `valuta_id` int(10) UNSIGNED NOT NULL DEFAULT 500,
  `mul` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`item_id`, `zone_id`, `zone_to`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pack_types
-- ----------------------------
DROP TABLE IF EXISTS `pack_types`;
CREATE TABLE `pack_types`  (
  `pack_t_id` tinyint(2) UNSIGNED NOT NULL,
  `pack_t_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `pass_labor` int(255) NOT NULL DEFAULT 0,
  PRIMARY KEY (`pack_t_id`) USING BTREE,
  INDEX `pack_t_id`(`pack_t_id`, `pack_t_name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pack_zones
-- ----------------------------
DROP TABLE IF EXISTS `pack_zones`;
CREATE TABLE `pack_zones`  (
  `pack_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `zone_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `pack_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `zone_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for packs
-- ----------------------------
DROP TABLE IF EXISTS `packs`;
CREATE TABLE `packs`  (
  `item_id` int(11) UNSIGNED NOT NULL,
  `zone_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 100,
  `pack_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `pack_sname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `zone_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `pack_t_id` tinyint(2) UNSIGNED NULL DEFAULT NULL,
  `pack_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `side` int(11) NULL DEFAULT NULL,
  `zone_4` int(6) UNSIGNED NULL DEFAULT NULL,
  `zone_5` int(6) NULL DEFAULT NULL,
  `zone_8` int(6) UNSIGNED NULL DEFAULT NULL,
  `zone_12` int(6) UNSIGNED NULL DEFAULT NULL,
  `zone_17` int(6) UNSIGNED NULL DEFAULT NULL,
  `zone_20` int(6) UNSIGNED NULL DEFAULT NULL,
  `zone_40` int(6) UNSIGNED NULL DEFAULT NULL,
  `zone_30` int(6) UNSIGNED NULL DEFAULT NULL,
  `zone_37` int(6) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`, `zone_id`) USING BTREE,
  INDEX `pack_name`(`pack_name`) USING BTREE,
  INDEX `types`(`pack_t_id`, `pack_type`) USING BTREE,
  CONSTRAINT `packs_ibfk_1` FOREIGN KEY (`pack_t_id`, `pack_type`) REFERENCES `pack_types` (`pack_t_id`, `pack_t_name`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for parsed_crafts
-- ----------------------------
DROP TABLE IF EXISTS `parsed_crafts`;
CREATE TABLE `parsed_crafts`  (
  `craft_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rec_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `dood_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `dood_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `result_item_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `result_item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `labor_need` int(11) UNSIGNED NULL DEFAULT NULL,
  `profession` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `prof_need` int(11) NULL DEFAULT NULL,
  `result_amount` int(11) NOT NULL DEFAULT 1,
  `on_off` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `dood_group` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `deep` tinyint(3) UNSIGNED NULL DEFAULT 0,
  `my_craft` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `craft_time` tinyint(3) UNSIGNED NULL DEFAULT 0,
  `rec_data` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `mater_ids` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`craft_id`) USING BTREE,
  INDEX `result_item_name`(`result_item_name`) USING BTREE,
  INDEX `result_item_id`(`result_item_id`) USING BTREE,
  INDEX `names`(`result_item_id`, `result_item_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8000582 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for parsed_items
-- ----------------------------
DROP TABLE IF EXISTS `parsed_items`;
CREATE TABLE `parsed_items`  (
  `item_id` int(11) UNSIGNED NOT NULL,
  `price_buy` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `price_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `price_sale` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `is_trade_npc` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `category` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `on_off` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `personal` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `craftable` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ismat` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `categ_id` tinyint(3) UNSIGNED NULL DEFAULT NULL,
  `categ_pid` tinyint(3) UNSIGNED NULL DEFAULT NULL,
  `slot` tinyint(2) UNSIGNED NULL DEFAULT NULL,
  `roll_group` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `lvl` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `inst` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `basic_grade` tinyint(2) UNSIGNED NULL DEFAULT NULL,
  `forup_grade` tinyint(2) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`) USING BTREE,
  INDEX `item_id`(`item_id`, `item_name`) USING BTREE,
  INDEX `item_name`(`item_name`) USING BTREE,
  INDEX `off`(`on_off`) USING BTREE,
  INDEX `craftable`(`craftable`) USING BTREE,
  INDEX `ismat`(`ismat`) USING BTREE,
  INDEX `roll_group`(`roll_group`) USING BTREE,
  INDEX `lvl`(`lvl`) USING BTREE,
  INDEX `categ_id`(`categ_id`) USING BTREE,
  INDEX `price_type`(`price_type`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for parsed_last
-- ----------------------------
DROP TABLE IF EXISTS `parsed_last`;
CREATE TABLE `parsed_last`  (
  `id` tinyint(1) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for parsed_materials
-- ----------------------------
DROP TABLE IF EXISTS `parsed_materials`;
CREATE TABLE `parsed_materials`  (
  `craft_id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL,
  `result_item_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `mater_need` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `result_item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`craft_id`, `item_id`) USING BTREE,
  INDEX `result_item_name`(`result_item_name`) USING BTREE,
  INDEX `item_name`(`item_name`) USING BTREE,
  INDEX `item_id`(`item_id`) USING BTREE,
  INDEX `mat_names`(`item_id`, `item_name`) USING BTREE,
  INDEX `result_item_id`(`result_item_id`) USING BTREE,
  INDEX `result_names`(`result_item_id`, `result_item_name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for prices
-- ----------------------------
DROP TABLE IF EXISTS `prices`;
CREATE TABLE `prices`  (
  `user_id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) NOT NULL,
  `auc_price` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `server_group` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `time` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`, `item_id`, `server_group`) USING BTREE,
  CONSTRAINT `userpr_key` FOREIGN KEY (`user_id`) REFERENCES `mailusers` (`mail_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for prices_copy1
-- ----------------------------
DROP TABLE IF EXISTS `prices_copy1`;
CREATE TABLE `prices_copy1`  (
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `auc_price` int(11) NULL DEFAULT NULL,
  `server_group` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `time` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`user_id`, `item_id`, `server_group`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for prices_my
-- ----------------------------
DROP TABLE IF EXISTS `prices_my`;
CREATE TABLE `prices_my`  (
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `auc_price` int(11) NULL DEFAULT NULL,
  `server_group` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `time` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`user_id`, `item_id`, `server_group`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for prof_lvls
-- ----------------------------
DROP TABLE IF EXISTS `prof_lvls`;
CREATE TABLE `prof_lvls`  (
  `lvl` tinyint(2) UNSIGNED NOT NULL,
  `min` int(6) UNSIGNED NULL DEFAULT NULL,
  `max` int(6) UNSIGNED NULL DEFAULT NULL,
  `save_or` tinyint(2) UNSIGNED NULL DEFAULT NULL,
  `save_time` tinyint(3) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`lvl`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for profs
-- ----------------------------
DROP TABLE IF EXISTS `profs`;
CREATE TABLE `profs`  (
  `prof_id` tinyint(2) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profession` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `used` tinyint(1) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`prof_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for report_list
-- ----------------------------
DROP TABLE IF EXISTS `report_list`;
CREATE TABLE `report_list`  (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `report_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for reports
-- ----------------------------
DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `item_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `mess` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `report_type` tinyint(3) UNSIGNED NULL DEFAULT NULL,
  `time` timestamp(0) NULL DEFAULT NULL,
  `dtime` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 47 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for rolls
-- ----------------------------
DROP TABLE IF EXISTS `rolls`;
CREATE TABLE `rolls`  (
  `roll_id` int(10) UNSIGNED NOT NULL,
  `roll_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `item_group` tinyint(1) UNSIGNED NULL DEFAULT NULL,
  `double_up` tinyint(1) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`roll_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for seeds
-- ----------------------------
DROP TABLE IF EXISTS `seeds`;
CREATE TABLE `seeds`  (
  `item_id` bigint(20) NOT NULL,
  `category` varchar(254) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `item_name` varchar(254) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `cultisize` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for servers
-- ----------------------------
DROP TABLE IF EXISTS `servers`;
CREATE TABLE `servers`  (
  `id` tinyint(2) UNSIGNED NOT NULL AUTO_INCREMENT,
  `server_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `server_group` tinyint(1) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `sess_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL,
  `sessmark` char(12) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `first_ip` char(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `last_ip` char(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `first_time` datetime(0) NULL DEFAULT NULL,
  `last_time` datetime(0) NULL DEFAULT NULL,
  `platform` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `browser` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `device_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ismobiledevice` tinyint(3) NULL DEFAULT NULL,
  PRIMARY KEY (`sess_id`) USING BTREE,
  INDEX `usess`(`user_id`) USING BTREE,
  CONSTRAINT `usess` FOREIGN KEY (`user_id`) REFERENCES `mailusers` (`mail_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sides
-- ----------------------------
DROP TABLE IF EXISTS `sides`;
CREATE TABLE `sides`  (
  `side_id` tinyint(1) NOT NULL,
  `side_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`side_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for slots
-- ----------------------------
DROP TABLE IF EXISTS `slots`;
CREATE TABLE `slots`  (
  `id` tinyint(2) UNSIGNED NOT NULL,
  `slot_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tiptops
-- ----------------------------
DROP TABLE IF EXISTS `tiptops`;
CREATE TABLE `tiptops`  (
  `tip_id` int(11) UNSIGNED NOT NULL,
  `tip_text` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`tip_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tracker_users
-- ----------------------------
DROP TABLE IF EXISTS `tracker_users`;
CREATE TABLE `tracker_users`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uservid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `random` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `id_level` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `style` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `language` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `flag` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `joined` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `lastconnect` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `pid` int(11) NULL DEFAULT NULL,
  `time_offset` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `nick` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ts_groups
-- ----------------------------
DROP TABLE IF EXISTS `ts_groups`;
CREATE TABLE `ts_groups`  (
  `group_id` tinyint(3) UNSIGNED NOT NULL,
  `group_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `group_lvl` tinyint(3) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ts_us_groups
-- ----------------------------
DROP TABLE IF EXISTS `ts_us_groups`;
CREATE TABLE `ts_us_groups`  (
  `cldbid` int(11) NOT NULL,
  `group_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 8,
  `nickname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sh_nick` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`cldbid`, `group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ts_users
-- ----------------------------
DROP TABLE IF EXISTS `ts_users`;
CREATE TABLE `ts_users`  (
  `cldbid` int(11) UNSIGNED NOT NULL,
  `cluid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ts-nick` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `created` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `lastconnected` datetime(0) NULL DEFAULT NULL,
  `totalconnections` int(11) NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `lastip` bigint(20) NULL DEFAULT NULL,
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `guild_member` tinyint(3) UNSIGNED NULL DEFAULT NULL,
  `sh_nick` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `group_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 8,
  PRIMARY KEY (`cldbid`) USING BTREE,
  INDEX `sh_nick`(`sh_nick`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for updates
-- ----------------------------
DROP TABLE IF EXISTS `updates`;
CREATE TABLE `updates`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `user` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `time` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `price` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `item_id` int(11) NULL DEFAULT NULL,
  `edit_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `nick` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `sbor_id` int(11) NULL DEFAULT NULL,
  `to` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `res` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 317 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_best_crafts
-- ----------------------------
DROP TABLE IF EXISTS `user_best_crafts`;
CREATE TABLE `user_best_crafts`  (
  `user_id` int(11) UNSIGNED NOT NULL,
  `start_item_id` int(11) UNSIGNED NOT NULL,
  `craft_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`, `start_item_id`, `craft_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_crafts
-- ----------------------------
DROP TABLE IF EXISTS `user_crafts`;
CREATE TABLE `user_crafts`  (
  `user_id` int(11) UNSIGNED NOT NULL,
  `craft_id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `isbest` tinyint(2) UNSIGNED NULL DEFAULT NULL,
  `u_craft` int(11) NULL DEFAULT NULL,
  `auc_price` int(11) UNSIGNED NULL DEFAULT 0,
  `craft_price` bigint(11) NULL DEFAULT NULL,
  `updated` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `labor_total` decimal(10, 4) NOT NULL DEFAULT 0.0000,
  `spmu` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`, `craft_id`) USING BTREE,
  CONSTRAINT `key_mailuser` FOREIGN KEY (`user_id`) REFERENCES `mailusers` (`mail_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_items
-- ----------------------------
DROP TABLE IF EXISTS `user_items`;
CREATE TABLE `user_items`  (
  `user_item` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `crafts` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `auto_best` int(11) NULL DEFAULT NULL,
  `user_best` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_profs
-- ----------------------------
DROP TABLE IF EXISTS `user_profs`;
CREATE TABLE `user_profs`  (
  `user_id` int(11) UNSIGNED NOT NULL,
  `prof_id` tinyint(2) UNSIGNED NOT NULL,
  `prof` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `lvl` int(11) NULL DEFAULT NULL,
  `time` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`user_id`, `prof_id`) USING BTREE,
  CONSTRAINT `uprof_key` FOREIGN KEY (`user_id`) REFERENCES `mailusers` (`mail_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_servers
-- ----------------------------
DROP TABLE IF EXISTS `user_servers`;
CREATE TABLE `user_servers`  (
  `user_id` int(11) UNSIGNED NOT NULL,
  `server` tinyint(2) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`) USING BTREE,
  CONSTRAINT `us_servkey` FOREIGN KEY (`user_id`) REFERENCES `mailusers` (`mail_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for valutas
-- ----------------------------
DROP TABLE IF EXISTS `valutas`;
CREATE TABLE `valutas`  (
  `valut_id` int(10) UNSIGNED NOT NULL,
  `valut_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `vicon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`valut_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for zones
-- ----------------------------
DROP TABLE IF EXISTS `zones`;
CREATE TABLE `zones`  (
  `zone_id` tinyint(3) UNSIGNED NOT NULL,
  `zone_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `side` tinyint(1) UNSIGNED NULL DEFAULT NULL,
  `is_get` tinyint(1) UNSIGNED NULL DEFAULT NULL,
  `get_west` tinyint(1) UNSIGNED NULL DEFAULT NULL,
  `get_east` tinyint(1) UNSIGNED NULL DEFAULT NULL,
  `fresh_type` tinyint(3) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`zone_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
