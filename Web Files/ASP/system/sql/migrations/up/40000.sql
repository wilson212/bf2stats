--
-- fix pre-ASP4 db and constants.py mismatches
--

UPDATE weapon SET name='Claymore', is_explosive=1 WHERE id=12;
UPDATE weapon SET name='Hand Grenade' WHERE id=13;
UPDATE map SET id=120 WHERE name='operation_blue_pearl';

--
-- Always update version table!!!
--

INSERT INTO `_version`(`updateid`, `version`) VALUES (40000, '4.0.0');
