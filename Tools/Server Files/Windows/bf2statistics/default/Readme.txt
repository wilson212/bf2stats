In order for the ServerLauncher GUI to read the common scoring file, it needs to have 3 constants defined
that arent normally defined. They are "SCORE_HEAL", "SCORE_GIVEAMMO", "SCORE_REPAIR". By default, these score
values are hardcoded into the correspondning methods (onPlayerHealPoint, onPlayerRepairPoint, onPlayerGiveAmmoPoint), 
which makes it really hard to parse.

If your mod does use a modified scoringCommon.py, add the constants (use the bf2_scoringCommon.py as example),
and place it in this folder with the following format: {modFolderName}_scoringCommon.py.