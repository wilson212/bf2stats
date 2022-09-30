<?php

/*
	Copyright (C) 2006-2012  BF2Statistics

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
|
| NOTE: for special ranks, set points to -1. This will make the rank
| un-obtainable via rank validation.
|
| 'has_awards' => array( <award_id> => <level> )
|
*/ 

$ranks = array(
	1 => array(
		'title' => 'Private First Class',
		'points' => 150,
		'has_rank' => 0,
		'has_awards' => array()
	),
	2 => array(
		'title' => 'Lance Corporal',
		'points' => 500,
		'has_rank' => 1,
		'has_awards' => array()
	),
	3 => array(
		'title' => 'Coporal',
		'points' => 800,
		'has_rank' => 2,
		'has_awards' => array()
	),
	4 => array(
		'title' => 'Sergeant',
		'points' => 2500,
		'has_rank' => 3,
		'has_awards' => array()
	),
	5 => array(
		'title' => 'Staff Sergeant',
		'points' => 5000,
		'has_rank' => 4,
		'has_awards' => array()
	),
	6 => array(
		'title' => 'Gunnery Sergeant',
		'points' => 8000,
		'has_rank' => 5,
		'has_awards' => array()
	),
	7 => array(
		'title' => 'Master Sergeant',
		'points' => 20000,
		'has_rank' => 6,
		'has_awards' => array()
	),
	8 => array(
		'title' => 'First Sergeant',
		'points' => 20000,
		'has_rank' => 6,
		'has_awards' => array(
			'1031105' => 1, // Engineer Combat Badge
			'1031109' => 1, // Sniper Combat Badge
			'1031113' => 1, // Medic Combat Badge
			'1031115' => 1, // Spec Ops Combat Badge
			'1031119' => 1, // Assault Combat Badge
			'1031120' => 1, // Anti-tank Combat Badge
			'1031121' => 1, // Support Combat Badge
			'1031406' => 1, // Knife Combat Badge
			'1031619' => 1  // Pistol Combat Badge
		)
	),
	9 => array(
		'title' => 'Master Gunnery Sergeant',
		'points' => 50000,
		'has_rank' => array(7, 8),
		'has_awards' => array()
	),
	10 => array(
		'title' => 'Sergeant Major',
		'points' => 50000,
		'has_rank' => array(7, 8),
		'has_awards' => array(
			'1031923' => 1, // Ground Defense
			'1220104' => 1, // Air Defense
			'1220118' => 1, // Armor Badge
			'1220122' => 1, // Aviator Badge
			'1220803' => 1, // Helicopter Badge
			'1222016' => 1  // Transport Badge
		)
	),
    11 => array(
		'title' => 'Sergeant Major of the Corp',
		'points' => -1,
		'has_rank' => 10,
		'has_awards' => array()
	),
	12 => array(
		'title' => '2nd Lieutenant',
		'points' => 60000,
		'has_rank' => array(9, 10, 11),
		'has_awards' => array()
	),
	13 => array(
		'title' => '1st Lieutenant',
		'points' => 75000,
		'has_rank' => 12,
		'has_awards' => array()
	),
	14 => array(
		'title' => 'Captain',
		'points' => 90000,
		'has_rank' => 13,
		'has_awards' => array()
	),
	15 => array(
		'title' => 'Major',
		'points' => 115000,
		'has_rank' => 14,
		'has_awards' => array()
	),
	16 => array(
		'title' => 'Lieutenant Colonel',
		'points' => 125000,
		'has_rank' => 15,
		'has_awards' => array()
	),
	17 => array(
		'title' => 'Colonel',
		'points' => 150000,
		'has_rank' => 16,
		'has_awards' => array()
	),
	18 => array(
		'title' => 'Brigadier General',
		'points' => 180000,
		'has_rank' => 17,
		'has_awards' => array(
			'1031105' => 2, // Engineer Combat Badge
			'1031109' => 2, // Sniper Combat Badge
			'1031113' => 2, // Medic Combat Badge
			'1031115' => 2, // Spec Ops Combat Badge
			'1031119' => 2, // Assault Combat Badge
			'1031120' => 2, // Anti-tank Combat Badge
			'1031121' => 2, // Support Combat Badge
			'1031406' => 2, // Knife Combat Badge
			'1031619' => 2  // Pistol Combat Badge
		)
	),
	19 => array(
		'title' => 'Major General',
		'points' => 180000,
		'has_rank' => 18,
		'has_awards' => array(
			'1031923' => 2, // Ground Defense
			'1220104' => 2, // Air Defense
			'1220118' => 2, // Armor Badge
			'1220122' => 2, // Aviator Badge
			'1220803' => 2, // Helicopter Badge
			'1222016' => 2  // Transport Badge
		)
	),
	20 => array(
		'title' => 'Lieutenant General',
		'points' => 200000,
		'has_rank' => 19,
		'has_awards' => array()
	),
    21 => array(
		'title' => 'General',
		'points' => -1,
		'has_rank' => 20,
		'has_awards' => array()
	),
);