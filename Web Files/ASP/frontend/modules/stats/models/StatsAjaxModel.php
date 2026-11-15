<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
use System\Database;
use System\Database\DbConnection;
use System\DataTables;
use System\TimeHelper;

/**
 * Mapinfo Ajax Model
 *
 * This model is used to fetch player data for the dataTables javascript library
 *
 * @package Models
 * @subpackage Mapinfo
 */
class StatsAjaxModel
{
    /**
     * @var DbConnection The stats database connection
     */
    protected $pdo;

    /**
     * PlayerAjaxModel constructor.
     */
    public function __construct()
    {
        // Fetch database connection
        $this->pdo = Database::GetConnection('stats');
    }

    /**
     * This method retrieves the top players list for DataTables
     *
     * @param int $armyId The kit ID
     * @param array $data The GET or POSTS data for DataTables
     *
     * @return array
     */
    public function getTopArmyPlayersById($armyId, $data)
    {
        $columns = [
            ['db' => 'army_id', 'dt' => 'check',
                'formatter' => fn($d, $row): string => "<input type=\"checkbox\">"
            ],
            ['db' => 'rank_id', 'dt' => 'rank',
                'formatter' => fn($d, $row): string => "<img class='center' src=\"/ASP/frontend/images/ranks/rank_{$d}.gif\">"
            ],
            ['db' => 'name', 'dt' => 'name'],
            ['db' => 'country', 'dt' => 'country',
                'formatter' => fn($d, $row): string => "<img class='center flag' src=\"/ASP/frontend/images/flags/{$d}.png\">"
            ],
            ['db' => 'score', 'dt' => 'score',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'wins', 'dt' => 'wins',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'losses', 'dt' => 'losses',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'time', 'dt' => 'time',
                'formatter' => function( $d, $row ) {
                    $i = (int)$d;
                    return TimeHelper::SecondsToHms($i);
                }
            ],
            ['db' => 'best', 'dt' => 'best',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'player_id', 'dt' => 'actions',
                'formatter' => function( $d, array $row ): string {
                    $id = (int)$row['player_id'];

                    return '<span class="btn-group">
                            <a href="/ASP/players/view/'. $id .'"  rel="tooltip" title="View Player" class="btn btn-small">
                                <i class="icon-eye-open"></i>
                            </a>
                        </span>';
                }
            ],
        ];

        // Apply Filtering
        $filters = [];

        // Filter kit
        $filters[] = "`army_id` = ". $armyId;

        // Filter by country
        if ($_POST['filterCountry'] != '99') {
            $filters[] = "`country` = ". $this->pdo->quote($_POST['filterCountry']);
        }

        // Fetch data
        $filter = implode(' AND ', $filters);
        return DataTables::FetchData($data, $this->pdo, 'top_player_army_view', 'player_id', $columns, $filter);
    }

    /**
     * This method retrieves the top players list for DataTables
     *
     * @param int $kitId The kit ID
     * @param array $data The GET or POSTS data for DataTables
     *
     * @return array
     */
    public function getTopKitPlayersById($kitId, $data)
    {
        $columns = [
            ['db' => 'kit_id', 'dt' => 'check',
                'formatter' => fn($d, $row): string => "<input type=\"checkbox\">"
            ],
            ['db' => 'rank_id', 'dt' => 'rank',
                'formatter' => fn($d, $row): string => "<img class='center' src=\"/ASP/frontend/images/ranks/rank_{$d}.gif\">"
            ],
            ['db' => 'name', 'dt' => 'name'],
            ['db' => 'country', 'dt' => 'country',
                'formatter' => fn($d, $row): string => "<img class='center flag' src=\"/ASP/frontend/images/flags/{$d}.png\">"
            ],
            ['db' => 'kills', 'dt' => 'kills',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'deaths', 'dt' => 'deaths',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'time', 'dt' => 'time',
                'formatter' => function( $d, $row ) {
                    $i = (int)$d;
                    return TimeHelper::SecondsToHms($i);
                }
            ],
            ['db' => 'score', 'dt' => 'score',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'ratio', 'dt' => 'ratio',
                'formatter' => function( $d, array $row ): string {
                    // Get player ratio
                    $kills = (int)$row['kills'];
                    $deaths = (int)$row['deaths'];

                    $den = $this->getDenominator($kills, $deaths);
                    $ratio = ($den == 0) ? "0/0" : ($kills / $den) . '/' . ($deaths / $den);;
                    $color = ($d > 0.99) ? "green" : "red";

                    $d = number_format($d, 2);
                    return "<span class=\"text-nowrap\">$ratio (<span style=\"color: $color\">$d</span>)</span>";
                }
            ],
            ['db' => 'player_id', 'dt' => 'actions',
                'formatter' => function( $d, array $row ): string {
                    $id = (int)$row['player_id'];

                    return '<span class="btn-group">
                            <a href="/ASP/players/view/'. $id .'"  rel="tooltip" title="View Player" class="btn btn-small">
                                <i class="icon-eye-open"></i>
                            </a>
                        </span>';
                }
            ],
        ];

        // Apply Filtering
        $filters = [];

        // Filter kit
        $filters[] = "`kit_id` = ". $kitId;

        // Filter by country
        if ($_POST['filterCountry'] != '99') {
            $filters[] = "`country` = ". $this->pdo->quote($_POST['filterCountry']);
        }

        // Fetch data
        $filter = implode(' AND ', $filters);
        return DataTables::FetchData($data, $this->pdo, 'top_player_kit_view', 'player_id', $columns, $filter);
    }

    /**
     * This method retrieves the top players list for DataTables
     *
     * @param int $weaponId The weapon ID
     * @param array $data The GET or POSTS data for DataTables
     *
     * @return array
     */
    public function getTopWeaponPlayersById($weaponId, $data)
    {
        $columns = [
            ['db' => 'weapon_id', 'dt' => 'check',
                'formatter' => fn($d, $row): string => "<input type=\"checkbox\">"
            ],
            ['db' => 'rank_id', 'dt' => 'rank',
                'formatter' => fn($d, $row): string => "<img class='center' src=\"/ASP/frontend/images/ranks/rank_{$d}.gif\">"
            ],
            ['db' => 'name', 'dt' => 'name'],
            ['db' => 'country', 'dt' => 'country',
                'formatter' => fn($d, $row): string => "<img class='center flag' src=\"/ASP/frontend/images/flags/{$d}.png\">"
            ],
            ['db' => 'kills', 'dt' => 'kills',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'deaths', 'dt' => 'deaths',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'time', 'dt' => 'time',
                'formatter' => function( $d, $row ) {
                    $i = (int)$d;
                    return TimeHelper::SecondsToHms($i);
                }
            ],
            ['db' => 'ratio', 'dt' => 'ratio',
                'formatter' => function( $d, array $row ): string {
                    // Get player ratio
                    $kills = (int)$row['kills'];
                    $deaths = (int)$row['deaths'];

                    $den = $this->getDenominator($kills, $deaths);
                    $ratio = ($den == 0) ? "0/0" : ($kills / $den) . '/' . ($deaths / $den);
                    $color = ($d > 0.99) ? "green" : "red";

                    $d = number_format($d, 2);
                    return "<span class=\"text-nowrap\">$ratio (<span style=\"color: $color\">$d</span>)</span>";
                }
            ],
            ['db' => 'fired', 'dt' => 'fired'],
            ['db' => 'accuracy', 'dt' => 'accuracy',
                'formatter' => function( $d, array $row ): string {
                    // Get player ratio
                    $fired = number_format((int)$row['fired']);
                    $percent = ($d * 100);
                    return "<span style=\"border-bottom: 1px dotted #000;\" rel=\"tooltip\" title=\"Shots Fired: $fired\">$percent%</span>";
                }
            ],
            ['db' => 'player_id', 'dt' => 'actions',
                'formatter' => function( $d, array $row ): string {
                    $id = (int)$row['player_id'];

                    return '<span class="btn-group">
                            <a href="/ASP/players/view/'. $id .'"  rel="tooltip" title="View Player" class="btn btn-small">
                                <i class="icon-eye-open"></i>
                            </a>
                        </span>';
                }
            ],
        ];

        // Apply Filtering
        $filters = [];

        // Filter kit
        $filters[] = "`weapon_id` = ". $weaponId;

        // Filter by country
        if ($_POST['filterCountry'] != '99') {
            $filters[] = "`country` = ". $this->pdo->quote($_POST['filterCountry']);
        }

        // Fetch data
        $filter = implode(' AND ', $filters);
        return DataTables::FetchData($data, $this->pdo, 'top_player_weapon_view', 'player_id', $columns, $filter);
    }

    /**
     * This method retrieves the top players list for DataTables
     *
     * @param int $vehicleId The vehicle ID
     * @param array $data The GET or POSTS data for DataTables
     *
     * @return array
     */
    public function getTopVehiclePlayersById($vehicleId, $data)
    {
        $columns = [
            ['db' => 'vehicle_id', 'dt' => 'check',
                'formatter' => fn($d, $row): string => "<input type=\"checkbox\">"
            ],
            ['db' => 'rank_id', 'dt' => 'rank',
                'formatter' => fn($d, $row): string => "<img class='center' src=\"/ASP/frontend/images/ranks/rank_{$d}.gif\">"
            ],
            ['db' => 'name', 'dt' => 'name'],
            ['db' => 'country', 'dt' => 'country',
                'formatter' => fn($d, $row): string => "<img class='center flag' src=\"/ASP/frontend/images/flags/{$d}.png\">"
            ],
            ['db' => 'kills', 'dt' => 'kills',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'deaths', 'dt' => 'deaths',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'time', 'dt' => 'time',
                'formatter' => function( $d, $row ) {
                    $i = (int)$d;
                    return TimeHelper::SecondsToHms($i);
                }
            ],
            ['db' => 'score', 'dt' => 'score',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'ratio', 'dt' => 'ratio',
                'formatter' => function( $d, array $row ): string {
                    // Get player ratio
                    $kills = (int)$row['kills'];
                    $deaths = (int)$row['deaths'];

                    $den = $this->getDenominator($kills, $deaths);
                    $ratio = ($den == 0) ? "0/0" : ($kills / $den) . '/' . ($deaths / $den);
                    $color = ($d > 0.99) ? "green" : "red";

                    $d = number_format($d, 2);
                    return "<span class=\"text-nowrap\">$ratio (<span style=\"color: $color\">$d</span>)</span>";
                }
            ],
            ['db' => 'player_id', 'dt' => 'actions',
                'formatter' => function( $d, array $row ): string {
                    $id = (int)$row['player_id'];

                    return '<span class="btn-group">
                            <a href="/ASP/players/view/'. $id .'"  rel="tooltip" title="View Player" class="btn btn-small">
                                <i class="icon-eye-open"></i>
                            </a>
                        </span>';
                }
            ],
        ];

        // Apply Filtering
        $filters = [];

        // Filter kit
        $filters[] = "`vehicle_id` = ". $vehicleId;

        // Filter by country
        if ($_POST['filterCountry'] != '99') {
            $filters[] = "`country` = ". $this->pdo->quote($_POST['filterCountry']);
        }

        // Fetch data
        $filter = implode(' AND ', $filters);
        return DataTables::FetchData($data, $this->pdo, 'top_player_vehicle_view', 'player_id', $columns, $filter);
    }

    /**
     * Calculate greatest common divisor of x and y. The result is always positive even
     * if either of, or both, input operands are negative.
     *
     * @param number $x
     * @param number $y
     *
     * @return number A positive number that divides into both x and y
     */
    public function getDenominator($x, $y): float|int
    {
        while ($y != 0)
        {
            $remainder = $x % $y;
            $x = $y;
            $y = $remainder;
        }

        return abs($x);
    }
}
