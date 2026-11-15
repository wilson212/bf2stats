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

class ServiceAjaxModel
{
    /**
     * @var DbConnection The stats database connection
     */
    protected $pdo;

    /**
     * ServiceAjaxModel constructor.
     */
    public function __construct()
    {
        // Fetch database connection
        $this->pdo = Database::GetConnection('stats');
    }

    /**
     * This method retrieves the Rising Star data for DataTables
     *
     * @param array $data The GET or POSTS data from DataTables
     *
     * @return array
     */
    public function getRisingStarList($data)
    {
        $columns = [
            ['db' => 'pos', 'dt' => 'check',
                'formatter' => fn($d, $row): string => "<input type=\"checkbox\">"
            ],
            ['db' => 'pos', 'dt' => 'position'],
            ['db' => 'joined', 'dt' => 'joined',
                'formatter' => function( $d, $row ): string {
                    $i = (int)$d;
                    return date('F jS, Y g:i A T', $i);
                }
            ],
            ['db' => 'player_id', 'dt' => 'id'],
            ['db' => 'name', 'dt' => 'name'],
            ['db' => 'weeklyscore', 'dt' => 'score',
                'formatter' => function( $d, $row ): string {
                    $score = round($d / 10000, 2);
                    return "{$score}%";
                }
            ],
            ['db' => 'rank_id', 'dt' => 'rank',
                'formatter' => fn($d, $row): string => "<img class='center' src=\"/ASP/frontend/images/ranks/rank_{$d}.gif\">"
            ],
            ['db' => 'country', 'dt' => 'country',
                'formatter' => fn($d, $row): string => "<img class='center flag' src=\"/ASP/frontend/images/flags/{$d}.png\">"
            ],
            ['db' => 'player_id', 'dt' => 'actions',
                'formatter' => function( $d, array $row ): string {
                    $id = $row['player_id'];
                    return '<span class="btn-group">
                            <a href="/ASP/players/view/'. $id .'"  rel="tooltip" title="View Player Details" class="btn btn-small">
                                <i class="icon-eye-open"></i>
                            </a>
                        </span>';
                }
            ],
        ];

        return DataTables::FetchData($data, $this->pdo, 'rising_star_view', 'pos', $columns);
    }

    /**
     * This method retrieves all eligible Sergeant Majors for DataTables
     *
     * @param array $data The GET or POSTS data from DataTables
     *
     * @return array
     */
    public function getSmocList($data)
    {
        $columns = [
            ['db' => 'player_id', 'dt' => 'check',
                'formatter' => fn($d, $row): string => "<input type=\"checkbox\">"
            ],
            ['db' => 'rank_id', 'dt' => 'rank',
                'formatter' => fn($d, $row): string => "<img class='center' src=\"/ASP/frontend/images/ranks/rank_{$d}.gif\">"
            ],
            ['db' => 'name', 'dt' => 'name'],
            ['db' => 'country', 'dt' => 'country',
                'formatter' => fn($d, $row): string => "<img class='center flag' src=\"/ASP/frontend/images/flags/{$d}.png\">"
            ],
            ['db' => 'global_score', 'dt' => 'score',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'spm', 'dt' => 'spm',
                'formatter' => function( $d, $row ): string {
                    $score = round($d / 10000, 2);
                    return "{$score}";
                }
            ],
            ['db' => 'weekly_score', 'dt' => 'rising',
                'formatter' => function( $d, $row ): string {
                    $score = round($d / 10000, 2);
                    return "{$score}%";
                }
            ],
            ['db' => 'rank_games', 'dt' => 'games',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'lastonline', 'dt' => 'seen',
                'formatter' => function( $d, $row ): string {
                    $i = (int)$d;
                    return date('F jS, Y g:i A T', $i);
                }
            ],
            ['db' => 'player_id', 'dt' => 'actions',
                'formatter' => function( $d, array $row ): string {
                    $id = $row['player_id'];
                    return '<span class="btn-group">
                            <a id="go-'. $id .'" href="/ASP/players/view/'. $id .'"  rel="tooltip" title="View Player Details" class="btn btn-small" target="_blank">
                                <i class="icon-eye-open"></i>
                            </a>
                            <a id="select-'. $id .'" href="#" rel="tooltip" title="Select Player" class="btn btn-small">
                                <i class="icon-ok"></i>
                            </a>
                        </span>';
                }
            ],
        ];

        // Apply Filtering
        $filters = ['`banned` = 0'];
        $value = (int)$_POST['showBots'];
        if ($value == 0) {
            $filters[] = "`is_bot` != 1";
        }

        $filter = (count($filters) == 0) ? '' : implode(' AND ', $filters);
        return DataTables::FetchData($data, $this->pdo, 'eligible_smoc_view', 'player_id', $columns, $filter);
    }

    /**
     * This method retrieves all eligible Lieutenant Generals for DataTables
     *
     * @param array $data The GET or POSTS data from DataTables
     *
     * @return array
     */
    public function getGeneralList($data)
    {
        $columns = [
            ['db' => 'player_id', 'dt' => 'check',
                'formatter' => fn($d, $row): string => "<input type=\"checkbox\">"
            ],
            ['db' => 'rank_id', 'dt' => 'rank',
                'formatter' => fn($d, $row): string => "<img class='center' src=\"/ASP/frontend/images/ranks/rank_{$d}.gif\">"
            ],
            ['db' => 'name', 'dt' => 'name'],
            ['db' => 'country', 'dt' => 'country',
                'formatter' => fn($d, $row): string => "<img class='center flag' src=\"/ASP/frontend/images/flags/{$d}.png\">"
            ],
            ['db' => 'global_score', 'dt' => 'score',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'spm', 'dt' => 'spm',
                'formatter' => function( $d, $row ): string {
                    $score = round($d / 10000, 2);
                    return "{$score}";
                }
            ],
            ['db' => 'weekly_score', 'dt' => 'rising',
                'formatter' => function( $d, $row ): string {
                    $score = round($d / 10000, 2);
                    return "{$score}%";
                }
            ],
            ['db' => 'rank_games', 'dt' => 'games',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'lastonline', 'dt' => 'seen',
                'formatter' => function( $d, $row ): string {
                    $i = (int)$d;
                    return date('F jS, Y g:i A T', $i);
                }
            ],
            ['db' => 'player_id', 'dt' => 'actions',
                'formatter' => function( $d, array $row ): string {
                    $id = $row['player_id'];
                    return '<span class="btn-group">
                            <a id="go-'. $id .'" href="/ASP/players/view/'. $id .'"  rel="tooltip" title="View Player Details" class="btn btn-small" target="_blank">
                                <i class="icon-eye-open"></i>
                            </a>
                            <a id="select-'. $id .'" href="#" rel="tooltip" title="Select Player" class="btn btn-small">
                                <i class="icon-ok"></i>
                            </a>
                        </span>';
                }
            ],
        ];

        // Apply Filtering
        $filters = ['`banned` = 0'];
        $value = (int)$_POST['showBots'];
        if ($value == 0) {
            $filters[] = "`is_bot` != 1";
        }

        $filter = (count($filters) == 0) ? '' : implode(' AND ', $filters);
        return DataTables::FetchData($data, $this->pdo, 'eligible_general_view', 'player_id', $columns, $filter);
    }
}
