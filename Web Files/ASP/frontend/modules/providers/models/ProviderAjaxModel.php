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

class ProviderAjaxModel
{
    /**
     * @var DbConnection The stats database connection
     */
    protected $pdo;

    /**
     * ProviderAjaxModel constructor.
     */
    public function __construct()
    {
        // Fetch database connection
        $this->pdo = Database::GetConnection('stats');
    }

    /**
     * This method retrieves the played rounds by provider id for DataTables
     *
     * @param int $id the provider ID
     * @param array $data The GET or POSTS data for DataTables
     *
     * @return array
     */
    public function getRoundList($id, $data)
    {
        $columns = [
            ['db' => 'id', 'dt' => 'check',
                'formatter' => fn($d, $row): string => "<input type=\"checkbox\">"
            ],
            ['db' => 'id', 'dt' => 'id'],
            ['db' => 'round_end', 'dt' => 'round_end',
                'formatter' => function( $d, $row ): string {
                    $i = (int)$d;
                    return date('F jS, Y g:i A T', $i);
                }
            ],
            ['db' => 'map', 'dt' => 'map'],
            ['db' => 'server_name', 'dt' => 'server'],
            ['db' => 'provider_id', 'dt' => 'provider_id'],
            ['db' => 'winner', 'dt' => 'winner',
                'formatter' => function( $d, array $row ): string {
                    $id = (int)$d;
                    if ($id == 0) {
                        return "<img class='center' src=\"/ASP/frontend/images/armies/small/-1.png\">";
                    }

                    $w = $row["team{$id}"];
                    return "<img class='center' src=\"/ASP/frontend/images/armies/small/{$w}.png\">";
                }
            ],
            ['db' => 'team1', 'dt' => 'team1',
                'formatter' => fn($d, $row): string => "<img class='center' src=\"/ASP/frontend/images/armies/small/{$d}.png\">"
            ],
            ['db' => 'team2', 'dt' => 'team2',
                'formatter' => fn($d, $row): string => "<img class='center' src=\"/ASP/frontend/images/armies/small/{$d}.png\">"
            ],
            ['db' => 'tickets', 'dt' => 'tickets',
                'formatter' => fn($d, $row): string => number_format($d)
            ],
            ['db' => 'players', 'dt' => 'players'],
            ['db' => 'id', 'dt' => 'actions',
                'formatter' => function( $d, array $row ): string {
                    $id = $row['id'];
                    return '<span class="btn-group">
                            <a href="/ASP/roundinfo/view/'. $id .'"  rel="tooltip" title="View Round Details" class="btn btn-small">
                                <i class="icon-eye-open"></i>
                            </a>
                        </span>';
                }
            ],
        ];

        return DataTables::FetchData($data, $this->pdo, 'round_history_view', 'id', $columns, 'provider_id = '. (int)$id);
    }
}
