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
class MapinfoAjaxModel
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
     * @param int $mapId The map id
     * @param array $data The GET or POSTS data for DataTables
     *
     * @return array
     */
    public function getTopMapPlayersById($mapId, $data)
    {
        $columns = [
            ['db' => 'map_id', 'dt' => 'check',
                'formatter' => fn($d, $row): string => "<input type=\"checkbox\">"
            ],
            ['db' => 'player_id', 'dt' => 'id'],
            ['db' => 'name', 'dt' => 'name'],
            ['db' => 'country', 'dt' => 'country',
                'formatter' => fn($d, $row): string => "<img class='center flag' src=\"/ASP/frontend/images/flags/{$d}.png\">"
            ],
            ['db' => 'rank_id', 'dt' => 'rank',
                'formatter' => fn($d, $row): string => "<img class='center' src=\"/ASP/frontend/images/ranks/rank_{$d}.gif\">"],
            ['db' => 'score', 'dt' => 'score',
                'formatter' => fn($d, $row): string => number_format($d)
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
            ['db' => 'games', 'dt' => 'games',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'rank_id', 'dt' => 'actions',
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

        // Fetch data
        $filter = "`map_id` = ". $mapId;
        return DataTables::FetchData($data, $this->pdo, 'player_map_top_players_view', 'player_id', $columns, $filter);
    }
}
