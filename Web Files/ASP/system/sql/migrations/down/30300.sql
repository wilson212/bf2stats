UPDATE weapon SET name='Hand Grenade', is_explosive=0 WHERE id=12;
UPDATE weapon SET name='Claymore' WHERE id=13;
UPDATE map SET id=603 WHERE name='operation_blue_pearl';

--
-- Always delete record from version table!!!
--
DELETE FROM `_version` WHERE updateid = 40000;
