<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2019, BF2statistics.com
 * License:      GNU GPL v3
 *
 */

/**
 * Provides a mapping of army abbreviations from a parsed server responses to
 * it's full name representation, and flag ID. Modded content will need to add
 * abbreviations to display correctly.
 *
 * This mapping is used by the Server module and view method (/ASP/servers/view/[id])
 */
return [
    // Vanilla BF2
    'us' => [
        'flag' => 0,
        'name' => 'United States Marine Corps'
    ],
    'mec' => [
        'flag' => 1,
        'name' => 'Middle Eastern Coalition'
    ],
    'ch' => [
        'flag' => 2,
        'name' => 'People\'s Liberation Army'
    ],

    // Xpack 1 Special Forces
    'seal' => [
        'flag' => 0,
        'name' => 'United States Navy Seals'
    ],
    'mecsf' => [
        'flag' => 1,
        'name' => 'Middle Eastern Coalition Special Forces'
    ],
    'sas' => [
        'flag' => 4,
        'name' => 'British Special Air Service'
    ],
    'spetz' => [
        'flag' => 5,
        'name' => 'Russian Spetsnaz'
    ],
    'chinsurgent' => [
        'flag' => 7,
        'name' => 'Rebels'
    ],
    'meinsurgent' => [
        'flag' => 8,
        'name' => 'Insurgents'
    ],

    // Booster Pack 1 EuroForce
    'eu' => [
        'flag' => 9,
        'name' => 'European Union'
    ],

    // POE2
    'ger' => [
        'flag' => 10,
        'name' => 'German Forces'
    ],
    'ukr' => [
        'flag' => 12,
        'name' => 'Ukrainian Forces'
    ],

    // AIX
    'un' => [
        'flag' => 13,
        'name' => 'United Nations'
    ],

    // Hard Justice
    'us2' => [
        'flag' => 0,
        'name' => 'United States Marine Corps'
    ],
    'us3' => [
        'flag' => 0,
        'name' => 'United States Marine Corps'
    ],
    'mec2' => [
        'flag' => 1,
        'name' => 'Middle Eastern Coalition'
    ],
    'mec3' => [
        'flag' => 1,
        'name' => 'Middle Eastern Coalition'
    ],
    'ch2' => [
        'flag' => 2,
        'name' => 'People\'s Liberation Army'
    ],
    'ch3' => [
        'flag' => 2,
        'name' => 'People\'s Liberation Army'
    ],
    'ca' => [
        'flag' => 13,
        'name' => 'Canadian Forces'
    ],

    // Nations at war 8.0
    'blackwater' => [
        'flag' => 14,
        'name' => 'Blackwater Military Contractors'
    ],
    'taliban' => [
        'flag' => 15,
        'name' => 'Taliban Forces'
    ],
    'au' => [
        'flag' => 16,
        'name' => 'Australian Forces'
    ],
    'ru' => [
        'flag' => 17,
        'name' => 'Russian Forces'
    ],
    'gb' => [
        'flag' => 18,
        'name' => 'British Forces'
    ],
    'nato' => [
        'flag' => 19,
        'name' => 'NATO Forces'
    ],
    'isis' => [
        'flag' => 20,
        'name' => 'ISIS Forces'
    ],
    'iraqa' => [
        'flag' => 21,
        'name' => 'Iraqi Forces'
    ],
    'usmc' => [
        'flag' => 0,
        'name' => 'United States Marines Corps'
    ],
    'somalia' => [
        'flag' => 23,
        'name' => 'Somalian Forces'
    ],
    'rangers' => [
        'flag' => 0,
        'name' => 'U.S Army Rangers'
    ],
    'idf' => [
        'flag' => 25,
        'name' => 'Israel Defense Force'
    ],
    'chsf' => [
        'flag' => 2,
        'name' => 'Chinese Special Forces'
    ],
    'paras' => [
        'flag' => 27,
        'name' => 'British Paratroop Regiment'
    ],
    'casf' => [
        'flag' => 28,
        'name' => 'Canadian Special Forces'
    ],
    'hamas' => [
        'flag' => 29,
        'name' => 'Hamas Forces'
    ],
    'hezbollah' => [
        'flag' => 30,
        'name' => 'Hezbollah Forces'
    ],
    'iran' => [
        'flag' => 31,
        'name' => 'Iran Forces'
    ],
    'saudi_arabia' => [
        'flag' => 32,
        'name' => 'Saudi Arabia Forces'
    ],
    'syria' => [
        'flag' => 33,
        'name' => 'Syrian Forces'
    ],
    'egypt' => [
        'flag' => 34,
        'name' => 'Egyptian Army'
    ],
    'pakistan' => [
        'flag' => 35,
        'name' => 'Pakistan Army'
    ],
    'india' => [
        'flag' => 36,
        'name' => 'Indian Armed forces'
    ],
];