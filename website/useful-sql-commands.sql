ALTER TABLE `joblist` CHANGE `bonusjob` `track` ENUM('no','yes','fast','slow','bonus') CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL DEFAULT 'slow'
UPDATE `joblist` SET `track` = 'fast' WHERE `fast_track_order` IS NOT NULL
UPDATE `joblist` SET `track` = 'bonus' WHERE `track` = 'yes'
UPDATE `joblist` SET `track` = 'slow' WHERE `track` = 'no'
ALTER TABLE `joblist` CHANGE `slow_track_order` `joborder` MEDIUMINT( 6 ) NOT NULL 
ALTER TABLE `joblist` DROP `fast_track_order`
ALTER TABLE `joblist` CHANGE `track` `track` ENUM('fast','slow','bonus') CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL DEFAULT 'slow'
-- Order fast - slow - bonus is important

ALTER TABLE `userprogress` ADD `lastupdate` DATETIME NOT NULL 
ALTER TABLE `userprogress` CHANGE `approved` `approved` DATETIME NULL DEFAULT NULL COMMENT 'Set by teacher'
UPDATE `userprogress` SET `lastupdate`=NOW()
ALTER TABLE `userprogress` MODIFY COLUMN `approved` DATETIME AFTER `lastupdate`
