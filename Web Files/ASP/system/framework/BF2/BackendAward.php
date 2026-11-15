<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */

namespace System\BF2;
use PDO;

/**
 * This class represents a Backend Award with a series of criteria's that
 * can be tested against a player
 *
 * @package System\BF2
 */
class BackendAward
{
    /**
     * @var int
     */
    public $awardId = 0;

    /**
     * BackendAward constructor.
     *
     * @param $awardId
     * @param $criteria
     * @param AwardCriteria[] $criteria
     */
    public function __construct($awardId, /**
     * @var AwardCriteria[]
     */
    protected $awardCriteria)
    {
        $this->awardId = (int)$awardId;
    }

    /**
     * Determines whether or not a player has met all of the required criteria to
     * earn this backend award.
     *
     * This method does properly allow multiple awarding of backend medals
     *
     * @param Player $player The player to run the criteria against
     * @param PDO $connection Stats database connection
     * @param int $awardCount [Reference Variable] Returns the amount of times the Award has
     *  been awarded to the player.
     *
     * @return bool true if the player has met the criteria to earn this award, or false
     */
    public function criteriaMet(Player $player, PDO $connection, &$awardCount): bool
    {
        // Get the award count (or level for badges) for this award
        $query = sprintf("SELECT COUNT(player_id) FROM player_award WHERE player_id=%d AND award_id=%d", $player->id, $this->awardId);
        $awardCount = (int)$connection->query($query)->fetchColumn(0);
        $isRibbon = ($this->awardId > 3000000);

        // Can only receive ribbons once in a lifetime, so return false if we have it already
        if ($isRibbon && $awardCount > 0) {
            return false;
        }

        // Loop through each criteria and see if we have met the criteria
        foreach ($this->awardCriteria as $criteria)
        {
            // Build the where statement for backend medals
            $where = str_replace('###', $awardCount, $criteria->where);

            /** @noinspection SqlResolve */
            $query = vsprintf("SELECT %s FROM `%s` WHERE player_id=%d AND %s LIMIT 1", [
                $criteria->field,
                $criteria->table,
                $player->id,
                $where
            ]);

            $row = $connection->query($query)->fetch();
            if (empty($row) || !$criteria->checkCriteria($row, $awardCount)) {
                return false;
            }
        }

        return true;
    }
}