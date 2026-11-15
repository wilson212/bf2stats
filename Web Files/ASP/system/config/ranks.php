<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */

/**
 * ------------------------------------------------------------------
 * Return rank structure array
 * ------------------------------------------------------------------
 *
 * Defines information to obtain each rank, which is used by the
 * System\BF2\RankCalculator class to display the next player rank ups,
 * as well as the BattleSpy anti-cheat when verifying promotions.
 */
return [
    0 => [
        'title' => 'Private',
        'points' => 0,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 0,
        'has_awards' => []
    ],
    1 => [
        'title' => 'Private First Class',
        'points' => 150,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 0,
        'has_awards' => []
    ],
    2 => [
        'title' => 'Lance Corporal',
        'points' => 500,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 1,
        'has_awards' => []
    ],
    3 => [
        'title' => 'Corporal',
        'points' => 800,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 2,
        'has_awards' => []
    ],
    4 => [
        'title' => 'Sergeant',
        'points' => 2500,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 3,
        'has_awards' => []
    ],
    5 => [
        'title' => 'Staff Sergeant',
        'points' => 5000,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 4,
        'has_awards' => []
    ],
    6 => [
        'title' => 'Gunnery Sergeant',
        'points' => 8000,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 5,
        'has_awards' => []
    ],
    7 => [
        'title' => 'Master Sergeant',
        'points' => 20000,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 6,
        'has_awards' => []
    ],
    8 => [
        'title' => 'First Sergeant',
        'points' => 20000,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 6,
        'has_awards' => [
            '1031105' => 1, // Engineer Combat Badge
            '1031109' => 1, // Sniper Combat Badge
            '1031113' => 1, // Medic Combat Badge
            '1031115' => 1, // Spec Ops Combat Badge
            '1031119' => 1, // Assault Combat Badge
            '1031120' => 1, // Anti-tank Combat Badge
            '1031121' => 1, // Support Combat Badge
            '1031406' => 1, // Knife Combat Badge
            '1031619' => 1  // Pistol Combat Badge
        ]
    ],
    9 => [
        'title' => 'Master Gunnery Sergeant',
        'points' => 50000,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => [7, 8],
        'has_awards' => []
    ],
    10 => [
        'title' => 'Sergeant Major',
        'points' => 50000,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => [7, 8],
        'has_awards' => [
            '1031923' => 1, // Ground Defense
            '1220104' => 1, // Air Defense
            '1220118' => 1, // Armor Badge
            '1220122' => 1, // Aviator Badge
            '1220803' => 1, // Helicopter Badge
            '1222016' => 1  // Transport Badge
        ]
    ],
    11 => [
        'title' => 'Sergeant Major of the Corp',
        'points' => 50000,
        'time' => 0,
        'skip' => true,
        'backend' => true,
        'has_rank' => 10,
        'has_awards' => []
    ],
    12 => [
        'title' => '2nd Lieutenant',
        'points' => 60000,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => [9, 10, 11],
        'has_awards' => []
    ],
    13 => [
        'title' => '1st Lieutenant',
        'points' => 75000,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 12,
        'has_awards' => []
    ],
    14 => [
        'title' => 'Captain',
        'points' => 90000,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 13,
        'has_awards' => []
    ],
    15 => [
        'title' => 'Major',
        'points' => 115000,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 14,
        'has_awards' => []
    ],
    16 => [
        'title' => 'Lieutenant Colonel',
        'points' => 125000,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 15,
        'has_awards' => []
    ],
    17 => [
        'title' => 'Colonel',
        'points' => 150000,
        'time' => 0,
        'skip' => false,
        'backend' => false,
        'has_rank' => 16,
        'has_awards' => []
    ],
    18 => [
        'title' => 'Brigadier General',
        'points' => 180000,
        'time' => 3888000,  // In seconds
        'skip' => false,
        'backend' => false,
        'has_rank' => 17,
        'has_awards' => [
            '1031105' => 2, // Engineer Combat Badge
            '1031109' => 2, // Sniper Combat Badge
            '1031113' => 2, // Medic Combat Badge
            '1031115' => 2, // Spec Ops Combat Badge
            '1031119' => 2, // Assault Combat Badge
            '1031120' => 2, // Anti-tank Combat Badge
            '1031121' => 2, // Support Combat Badge
            '1031406' => 2, // Knife Combat Badge
            '1031619' => 2  // Pistol Combat Badge
        ]
    ],
    19 => [
        'title' => 'Major General',
        'points' => 180000,
        'time' => 4500000,  // In seconds
        'skip' => false,
        'backend' => false,
        'has_rank' => 18,
        'has_awards' => [
            '1031923' => 2, // Ground Defense
            '1220104' => 2, // Air Defense
            '1220118' => 2, // Armor Badge
            '1220122' => 2, // Aviator Badge
            '1220803' => 2, // Helicopter Badge
            '1222016' => 2  // Transport Badge
        ]
    ],
    20 => [
        'title' => 'Lieutenant General',
        'points' => 200000,
        'time' => 5184000,  // In seconds
        'skip' => false,
        'backend' => false,
        'has_rank' => 19,
        'has_awards' => []
    ],
    21 => [
        'title' => 'General',
        'points' => 200000,
        'time' => 0,
        'skip' => false,
        'backend' => true,
        'has_rank' => 20,
        'has_awards' => []
    ],
];