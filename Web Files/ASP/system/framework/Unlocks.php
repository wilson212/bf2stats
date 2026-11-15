<?php
namespace System;

use DomainException;
use PDO;
use Throwable;

/** The unlock budget and selection rules shared by the two GameSpy endpoints. */
class Unlocks
{
    public static function countByRank(int $rank): int
    {
        if ($rank >= 9) return 7;
        if ($rank >= 7) return 6;
        return max(0, $rank - 1);
    }

    public static function countByBadges(int $pid, int $rank, PDO $connection): int
    {
        $level = (int)Config::Get('game_unlocks_bonus');
        if ($level <= 0 || $rank < (int)Config::Get('game_unlocks_bonus_min')) {
            return 0;
        }
        // Multiple levels/rounds of the same badge grant only one point.
        $stmt = $connection->prepare('SELECT COUNT(DISTINCT award_id) FROM player_award
            WHERE player_id = ? AND level >= ?
            AND award_id IN (1031119,1031120,1031109,1031115,1031121,1031105,1031113)');
        $stmt->execute([$pid, $level]);
        return (int)$stmt->fetchColumn();
    }

    public static function select(int $pid, int $id, PDO $connection): void
    {
        $connection->beginTransaction();
        try {
            // Serialize choices for a player before reading their remaining budget.
            $stmt = $connection->prepare('SELECT rank_id FROM player WHERE id = ? FOR UPDATE');
            $stmt->execute([$pid]);
            $rank = $stmt->fetchColumn();
            if ($rank === false) {
                throw new DomainException('Player Not Found!');
            }
            $stmt = $connection->prepare('SELECT id FROM `unlock` WHERE id = ?');
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() === false) {
                throw new DomainException('Invalid Unlock!');
            }
            $stmt = $connection->prepare('SELECT unlock_id FROM player_unlock WHERE player_id = ?');
            $stmt->execute([$pid]);
            $selected = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
            $mode = (int)Config::Get('game_unlocks');
            if ($mode === 1 || in_array($id, $selected, true)) {
                $connection->commit();
                return;
            }
            if ($mode !== 0) {
                throw new DomainException('Unlock Selection Disabled!');
            }
            $stmt = $connection->prepare('SELECT parent_id FROM unlock_requirement WHERE child_id = ?');
            $stmt->execute([$id]);
            foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $parent) {
                if (!in_array((int)$parent, $selected, true)) {
                    throw new DomainException('Required Unlock Not Selected!');
                }
            }
            $budget = self::countByRank((int)$rank) + self::countByBadges($pid, (int)$rank, $connection);
            if (count($selected) >= $budget) {
                throw new DomainException('No Unlocks Available!');
            }
            $stmt = $connection->prepare('INSERT INTO player_unlock (player_id, unlock_id, timestamp) VALUES (?, ?, ?)');
            $stmt->execute([$pid, $id, time()]);
            $connection->commit();
        } catch (Throwable $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            throw $e;
        }
    }
}
