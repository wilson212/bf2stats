<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */

namespace System;
use System\BF2\Player;
use System\Collections\Dictionary;
use System\Database\DbConnection;
use System\Database\UpdateOrInsertQuery;
use System\IO\Path;

/**
 * BattleSpy Anti-Cheat Analyzer
 *
 * This class is used as an Anti-Cheat software to catch and flag players who
 * exceed specified thresholds in the specified areas:
 * <pre>
 * - Score per minute exceeds threshold
 * - Excessive kills on a single player
 * - Excessive total kills in the round
 * - Excessive awards earned by a single player in the round
 * - Invalid promotion received
 * </pre>
 *
 * BattleSpy will never ban or suspend players. This system will
 * only flag players for further review by the stats admin.
 *
 * @package System
 */
class BattleSpy
{
    /**
     * @var int Cross Service Exploitation Flag
     */
    const FLAG_PLAYER_CSE = 1;

    /**
     * @var int Player Banned prior to round start Flag
     */
    const FLAG_PLAYER_BANNED = 2;

    /**
     * @var int Excessive Score Per Minute Flag
     */
    const FLAG_PLAYER_SPM = 3;

    /**
     * @var int Excessive Kill on a Player Flag
     */
    const FLAG_PLAYER_TARGET_KILLS = 4;

    /**
     * @var int Excessive Kill on a Player Flag
     */
    const FLAG_PLAYER_KILLS = 5;

    /**
     * @var int Excessive Earned Awards on Player Flag
     */
    const FLAG_PLAYER_AWARDS = 6;

    /**
     * @var int Excessive Team Kills on a Player Flag
     */
    const FLAG_PLAYER_TEAMKILLS = 7;

    /**
     * @var int Excessive Weapon Accuracy Flag
     */
    const FLAG_PLAYER_ACCURACY = 8;

    /**
     * @var int Invalid rank promotion
     */
    const FLAG_PLAYER_PROMOTION = 9;

    /**
     * @var array
     */
    protected $notifications = [];

    /**
     * @var bool Indicates whether BattleSpy is enabled
     */
    protected bool $enabled;

    /**
     * @var bool Indicates whether BattleSpy rank checking is enabled
     */
    protected bool $rankCheck;

    /**
     * @var int Gets or Sets the maximum score per minute threshold
     */
    protected float $maxSPM;

    /**
     * @var int Gets or Sets the maximum kills per minute threshold
     */
    protected float $maxKPM;

    /**
     * @var int Gets or Sets the maximum kills per target threshold
     */
    protected int $maxTargetKills;

    /**
     * @var int Gets or Sets the maximum team kills threshold
     */
    protected int $maxTeamKills;

    /**
     * @var int Gets or Sets the maximum earned awards threshold (excludes backend awards)
     */
    protected int $maxAwards;

    /**
     * @var int Gets or sets the maximum weapon accuracy
     */
    protected int $maxWeaponAcc;

    /**
     * @var Dictionary
     */
    protected $rankData;

    /**
     * BattleSpy constructor.
     *
     * @param int $serverId
     * @param int $roundId
     */
    public function __construct(/**
     * @var DbConnection The database connection
     */
    protected DbConnection $pdo, protected $serverId, protected $roundId)
    {
        // Load configuration
        $this->enabled = ((int)Config::Get('battlespy_enable') != 0);
        $this->rankCheck = ((int)Config::Get('battlespy_rank_check') != 0);
        $this->maxSPM = (float)Config::Get('battlespy_max_spm');
        $this->maxKPM = (float)Config::Get('battlespy_max_kpm');
        $this->maxTargetKills = (int)Config::Get('battlespy_max_target_kills');
        $this->maxTeamKills = (int)Config::Get('battlespy_max_team_kills');
        $this->maxAwards = (int)Config::Get('battlespy_max_awards');

        // Added in later release
        $this->maxWeaponAcc = (int)Config::GetOrDefault('battlespy_max_accuracy', 50);

        // Load rank data
        if ($this->rankCheck)
        {
            $rankData = include Path::Combine(SYSTEM_PATH, 'config', 'ranks.php');
            $this->rankData = new Dictionary(false, $rankData);
        }
    }

    /**
     * Analyzes a players stats to check for cheating, and applies
     * an internal report if the player is found to be cheating.
     */
    public function analyze(Player $player): void
    {
        // Quit if not enabled
        if (!$this->enabled) {
            return;
        }

        // Calculate total time the player played in the round by minutes
        $mins = round($player->roundTime / 60, 3);

        /** Check score per minute */
        if ($this->maxSPM > 0)
        {
            $spm = ($mins == 0) ? 0 : round($player->roundScore / $mins, 3);
            if ($spm > $this->maxSPM)
            {
                // Determine severity
                $plus50p = $this->maxSPM * 1.5;
                $double = $this->maxSPM * 2;

                // Report player for having too high of a SPM
                $severity = ($spm > $double) ? 3 : (($spm > $plus50p) ? 2 : 1);
                $message = sprintf("Player Score per Min (%.3f) exceeds threshold of (%.3f)", $spm, $this->maxSPM);
                $this->report($player, $message, self::FLAG_PLAYER_SPM, $severity);
            }
        }

        /** Check kills per minute */
        if ($this->maxKPM > 0)
        {
            $kpm = ($mins == 0) ? 0 : round($player->kills / $mins, 3);
            if ($kpm > $this->maxKPM)
            {
                // Determine severity
                $plus50p = $this->maxKPM * 1.5;
                $double = $this->maxKPM * 2;

                // Report player for having too high of a KPM
                $severity = ($spm > $double) ? 3 : (($spm > $plus50p) ? 2 : 1);
                $message = sprintf("Player Kills per Min (%.3f) exceeds threshold of (%.3f)", $kpm, $this->maxKPM);
                $this->report($player, $message, self::FLAG_PLAYER_KILLS, $severity);
            }
        }

        /** Check target kills */
        if ($this->maxTargetKills > 0)
        {
            // Set severity limits
            $plus50p = $this->maxTargetKills * 1.5;
            $double = $this->maxTargetKills * 2;

            foreach ($player->victims as $pid => $count)
            {
                if ($count > $this->maxTargetKills)
                {
                    // Report player for having too many kills on a single player
                    $severity = ($count > $double) ? 3 : (($count > $plus50p) ? 2 : 1);
                    $message = sprintf("Player to Player Kills (%d) on victim Player (%d) exceeds threshold of (%d)", $count, $pid, $this->maxTargetKills);
                    $this->report($player, $message, self::FLAG_PLAYER_TARGET_KILLS, $severity);
                }
            }
        }

        /** Check awards */
        if ($this->maxAwards > 0)
        {
            $awardCount = count($player->earnedAwards);
            if ($awardCount > $this->maxAwards)
            {
                $message = "Player Award Count (%d) exceeds threshold of (%d)";
                $this->report($player, sprintf($message, $awardCount, $this->maxAwards), self::FLAG_PLAYER_AWARDS, 1);
            }
        }

        /** Check teamkills */
        if ($this->maxTeamKills > 0 && $player->teamKills > $this->maxTeamKills) {
            // Determine severity
            $plus50p = $this->maxTeamKills * 1.5;
            $double = $this->maxTeamKills * 2;
            // Report player for having too many team kills
            $severity = ($player->teamKills > $double) ? 3 : (($player->teamKills > $plus50p) ? 2 : 1);
            $message = sprintf("Player Team Kills (%d) exceeds threshold of (%d)", $player->teamKills, $this->maxTeamKills);
            $this->report($player, $message, self::FLAG_PLAYER_TEAMKILLS, $severity);
        }

        /** Check weapon accuracy */
        if ($this->maxWeaponAcc > 0)
        {
            $weapons = Config::GetOrDefault('battlespy_weapons', []);
            foreach ($player->weaponData as $weapon)
            {
                // We must fire at least three bullets
                if ($weapon->fired < 3 || !in_array($weapon->id, $weapons)) {
                    continue;
                }

                // Get accuracy
                $accuracy = round($weapon->hits / $weapon->fired, 3) * 100;
                if ($accuracy > $this->maxWeaponAcc)
                {
                    // Determine severity
                    $plus50p = $this->maxWeaponAcc + 10;
                    $double = $this->maxWeaponAcc + 15;

                    // Report player for having too many team kills
                    $name = StatsData::GetWeaponNameById($weapon->id);
                    $severity = ($accuracy > $double) ? 3 : (($accuracy > $plus50p) ? 2 : 1);
                    $message = sprintf(
                        "Player Weapon Accuracy with a (%s) of (%d%%) exceeds threshold of (%d%%). %d shots were fired.",
                        $name, $accuracy, $this->maxWeaponAcc, $weapon->fired
                    );
                    $this->report($player, $message, self::FLAG_PLAYER_ACCURACY, $severity);
                }
            }
        }
    }

    /**
     * Analyzes a players rank to check for promotion cheating, and applies
     * an internal report if the player is found to be cheating. This method
     * should only be called when a promotion happened in a round.
     *
     * @param Player $player The player object from the snapshot
     * @param int $globalScore The players global score currently in the database
     * @param int $globalTime The players global time played currently in the database
     *
     * @return bool true if the rank is verified, false otherwise
     */
    public function verifyPromotion(Player $player, $globalScore, $globalTime): bool
    {
        // Just return true if disabled
        if (!$this->enabled || !$this->rankCheck) {
            return true;
        }

        // Verify ranks points
        if ($this->rankData->tryGetValue($player->rank, $rank))
        {
            // Is this a backend awarded rank?
            if ($rank['backend']) {
                // Prepare message...
                $message = "Player was promoted to '%s' erroneously: Backend awarded rank.";
                $this->report($player, sprintf($message, $rank['name']), self::FLAG_PLAYER_PROMOTION, 3);
                return false;
            } elseif ($player->isAi) {
                // Stop here for bots, we only are checking special ranks for them
                return true;
            }

            // Get current score after round completion
            $globalScore += $player->roundScore;
            $globalTime += $player->roundTime;

            // Check points
            if ($globalScore < $rank['points'])
            {
                // Prepare message...
                $message = "Player was promoted to '%s' erroneously: Not enough points.";
                $this->report($player, sprintf($message, $rank['name']), self::FLAG_PLAYER_PROMOTION, 2);
                return false;
            }

            // Check time played
            if ($globalTime < $rank['time'])
            {
                // Prepare message...
                $message = "Player was promoted to '%s' erroneously: Not enough time played.";
                $this->report($player, sprintf($message, $rank['name']), self::FLAG_PLAYER_PROMOTION, 2);
                return false;
            }

            // If we are here, we are good
            return true;
        }

        // Rank doesn't exist?
        return false;
    }

    /**
     * Adds a report message to the list of reports
     *
     * @param Player $player The offending player
     * @param string $message The message that describes the player offense
     * @param int $flagCode The constant flag code of the offense
     * @param int $severity The severity level of the offense, in a range of 1 to 3,
     *  1 being low, and 3 being high.
     */
    public function report(Player $player, $message, $flagCode, $severity): void
    {
        $this->notifications[] = [
            'player_id' => $player->id,
            'player_name' => $player->name,
            'player_rank' => $player->rank,
            'flag' => $flagCode,
            'severity' => min(abs($severity), 3),
            'message' => $message
        ];
    }

    /**
     * Fetches the current list of report messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->notifications;
    }

    /**
     * Finalizes and saves the reported messages for
     * an admin to see. If this method is not called,
     * this report will be disposed.
     */
    public function finalize(): void
    {
        // Only create a database report if we have any messages
        if ($this->notifications !== [])
        {
            // Create report record for the database
            $query = new UpdateOrInsertQuery($this->pdo, 'battlespy_report');
            $query->set('server_id', '=', $this->serverId);
            $query->set('round_id', '=', $this->roundId);
            $query->executeInsert();

            // Grab report ID
            $reportId = $this->pdo->lastInsertId("id");

            // Insert report messages
            $query = new UpdateOrInsertQuery($this->pdo, 'battlespy_message');
            $query->set('report_id', '=', $reportId);

            // Insert messages
            foreach ($this->notifications as $report)
            {
                $query->set('player_id', '=', $report['player_id']);
                $query->set('flag', '=', $report['flag']);
                $query->set('severity', '=', $report['severity']);
                $query->set('message', '=', $report['message']);
                $query->executeInsert();
            }

            // Clear notifications
            $this->notifications = [];
        }
    }
}