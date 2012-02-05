<?php

/*
	Copyright (C) 2006  BF2Statistics

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
	
	MY EDIT LINE: 190
*/

/*******************************************
* 14/08/06 v0.0.1 - Initial build           *
*******************************************/

// Where clause Substitution String
$awards_substr = "###";

function buildAwardsData($mod) 
{

	$awardsdata = array();
	
	// Data: array(<short name>, <0 = badges, 1 = Other>)
	
	#Badges
	$awardsdata[] = array(1031406, "kcb", 0);
	$awardsdata[] = array(1031619, "pcb", 0);
	$awardsdata[] = array(1031119, "Acb", 0);
	$awardsdata[] = array(1031120, "Atcb", 0);
	$awardsdata[] = array(1031109, "Sncb", 0);
	$awardsdata[] = array(1031115, "Socb", 0);
	$awardsdata[] = array(1031121, "Sucb", 0);
	$awardsdata[] = array(1031105, "Ecb", 0);
	$awardsdata[] = array(1031113, "Mcb", 0);
	$awardsdata[] = array(1032415, "Eob", 0);
	$awardsdata[] = array(1190601, "Fab", 0);
	$awardsdata[] = array(1190507, "Eb", 0);
	$awardsdata[] = array(1191819, "Rb", 0);
	$awardsdata[] = array(1190304, "Cb", 0);
	$awardsdata[] = array(1220118, "Ab", 0);
	$awardsdata[] = array(1222016, "Tb", 0);
	$awardsdata[] = array(1220803, "Hb", 0);
	$awardsdata[] = array(1220122, "Avb", 0);
	$awardsdata[] = array(1220104, "adb", 0);
	$awardsdata[] = array(1031923, "Swb", 0);
	
	#Ribbons
	$awardsdata[] = array(3240301, "Car", 1);
	$awardsdata[] = array(3211305, "Mur", 1);
	$awardsdata[] = array(3150914, "Ior", 1);
	$awardsdata[] = array(3151920, "Sor", 1);
	$awardsdata[] = array(3190409, "Dsr", 1);
	$awardsdata[] = array(3242303, "Wcr", 1);
	$awardsdata[] = array(3212201, "Vur", 1);
	$awardsdata[] = array(3241213, "Lmr", 1);
	$awardsdata[] = array(3190318, "Csr", 1);
	$awardsdata[] = array(3190118, "Arr", 1);
	$awardsdata[] = array(3190105, "Aer", 1);
	$awardsdata[] = array(3190803, "Hsr", 1);
	$awardsdata[] = array(3040109, "Adr", 1);
	$awardsdata[] = array(3040718, "Gdr", 1);
	$awardsdata[] = array(3240102, "Ar", 1);
	$awardsdata[] = array(3240703, "gcr", 1);
	$awardsdata[] = array(3191305, "Msr", 1);
	$awardsdata[] = array(3190605, "Fsr", 1);
	
	// EF ribbon added by Wolverine
	$awardsdata[] = array(3270519, "Esr", 1);
	// AF ribbon added by Wolverine
	$awardsdata[] = array(3271401, "Nas", 1);
	
	#medals
	$awardsdata[] = array(2051907, "erg", 1);
	$awardsdata[] = array(2051919, "ers", 1);
	$awardsdata[] = array(2051902, "erb", 1);
	$awardsdata[] = array(2191608, "ph", 1);
	$awardsdata[] = array(2191319, "Msm", 1);
	$awardsdata[] = array(2190303, "Cam", 1);
	$awardsdata[] = array(2190309, "Acm", 1);
	$awardsdata[] = array(2190318, "Arm", 1);
	$awardsdata[] = array(2190308, "Hcm", 1);
	$awardsdata[] = array(2190703, "gcm", 1);
	$awardsdata[] = array(2020903, "Cim", 1);
	$awardsdata[] = array(2020913, "Mim", 1);
	$awardsdata[] = array(2020919, "Sim", 1);
	$awardsdata[] = array(2021322, "Mvm", 1);
	$awardsdata[] = array(2020419, "Dsm", 1);
	$awardsdata[] = array(2021403, "Ncm", 1);
	$awardsdata[] = array(2020719, "Gsm", 1);
	$awardsdata[] = array(2021613, "pmm", 1);
	
	// EF medal added by Wolverine
	$awardsdata[] = array(2270521, "Eum", 1);
	
	// SF
	if ($mod == 'xpack')
	{
		#badges
		$awardsdata[] = array(1261119, "X1Acb", 0);
		$awardsdata[] = array(1261120, "X1Atcb", 0);
		$awardsdata[] = array(1261109, "X1Sncb", 0);
		$awardsdata[] = array(1261115, "X1Socb", 0);
		$awardsdata[] = array(1261121, "X1Sucb", 0);
		$awardsdata[] = array(1261105, "X1Ecb", 0);
		$awardsdata[] = array(1261113, "X1Mcb", 0);
		$awardsdata[] = array(1260602, "X1fbb", 0);
		$awardsdata[] = array(1260708, "X1ghb", 0);
		$awardsdata[] = array(1262612, "X1zlb", 0);
		
		#ribbons
		$awardsdata[] = array(3261919, "X1Nss", 1);
		$awardsdata[] = array(3261901, "X1Sas", 1);
		$awardsdata[] = array(3261819, "X1Rsz", 1);
		$awardsdata[] = array(3261319, "X1Msf", 1);
		$awardsdata[] = array(3261805, "X1Reb", 1);
		$awardsdata[] = array(3260914, "X1Ins", 1);
		$awardsdata[] = array(3260318, "X1Csr", 1);
		$awardsdata[] = array(3260118, "X1Arr", 1);
		$awardsdata[] = array(3260105, "X1Aer", 1);
		$awardsdata[] = array(3260803, "X1Hsr", 1);
		
		#medals
		$awardsdata[] = array(2261913, "X1Nsm", 1);
		$awardsdata[] = array(2261919, "X1Ssm", 1);
		$awardsdata[] = array(2261613, "X1Spm", 1);
		$awardsdata[] = array(2261303, "X1Mcm", 1);
		$awardsdata[] = array(2261802, "X1Rbm", 1);
		$awardsdata[] = array(2260914, "X1Inm", 1);
	}
	
	return $awardsdata;
}

function buildBackendAwardsData($mod) 
{
	
	// Build Awards Data Array
	$awardsdata = array();
	
	// Criteria data:
	//	array(<table>, <field>, <expected result>, <where clause>)
	
	#Mid-East Service
	$awardsdata[] = array(3191305, "Msr", 1,
						array(
							array('maps', 'count(*)', 7, 'mapid IN (0,1,2,3,4,5,6) AND `time` >= 1')
						)
					);
	
	#Far-East Service
	$awardsdata[] = array(3190605, "Fsr", 1,
						array(
							array('maps', 'count(*)', 6, 'mapid IN (100,101,102,103,105,601) AND `time` >= 1')
						)
					);
	
	#Navy Cross
	$awardsdata[] = array(2021403, "Ncm", 2,
						array(
							array('army', 'count(*)', 1, 'time0 >= 360000*### AND best0 >= 100*### AND win0 >= 100*###')
						)
					);
	
	#Golden Scimitar
	$awardsdata[] = array(2020719, "Gsm", 2,
						array(
							array('army', 'count(*)', 1, 'time1 >= 360000*### AND best1 >= 100*### AND win1 >= 100*###')
						)
					);
	
	#People's Medallion
	$awardsdata[] = array(2021613, "pmm", 2,
						array(
							array('army', 'count(*)', 1, 'time2 >= 360000*### AND best2 >= 100*### AND win2 >= 100*###')
						)
					);
	
	#European Union Special Service Medal
	$awardsdata[] = array(2270521, "Esr", 2,
						array(
							array('maps', 'count(*)', 3, 'mapid IN (10,11,110) AND `time` >= 1'),
							array('maps', 'sum(`time`)', 180000, 'mapid IN (10,11,110) AND `time` >= 1')
						)
					);
	
	#European Union Service ribbon
	$awardsdata[] = array(3270519, "Eum", 1,
						array(
							array('maps', 'count(*)', 3, 'mapid IN (200,201,202) AND `time` >= 1'),
							array('maps', 'sum(`time`)', 90000, 'mapid IN (200,201,202) AND `time` >= 1')
						)
					);
	
	#North American Service Ribbon
	$awardsdata[] = array(3271401, "Nas", 1,
						array(
							array('maps', 'count(*)', 3, 'mapid IN (200,201,202) AND `time` >= 1'),
							array('maps', 'sum(`time`)', 54000, 'mapid IN (200,201,202) AND `time` >= 1')
						)
					);
	
	### Special Forces ###
	// SF
	if ($mod == 'xpack') 
	{
		# Navy Seal Special Service Medal
		$awardsdata[] = array(2261913, "X1Nsm", 2,
							array(
								array('army', 'count(*)', 1, 'time3 >= 180000*### AND best3 >= 100*### AND win3 >= 50*###')
							)
						);
		# SAS Special Service Medal
		$awardsdata[] = array(2261919, "X1Ssm", 2,
							array(
								array('army', 'count(*)', 1, 'time4 >= 180000*### AND best4 >= 100*### AND win4 >= 50*###')
							)
						);
		# SPETZ Special Service Medal
		$awardsdata[] = array(2261613, "X1Spm", 2,
							array(
								array('army', 'count(*)', 1, 'time5 >= 180000*### AND best5 >= 100*### AND win5 >= 50*###')
							)
						);
		# MECSF Special Service Medal
		$awardsdata[] = array(2261303, "X1Mcm", 2,
							array(
								array('army', 'count(*)', 1, 'time6 >= 180000*### AND best6 >= 100*### AND win6 >= 50*###')
							)
						);
		# Rebel Special Service Medal
		$awardsdata[] = array(2261802, "X1Rbm", 2,
							array(
								array('army', 'count(*)', 1, 'time7 >= 180000*### AND best7 >= 100*### AND win7 >= 50*###')
							)
						);
		# Insurgent Special Service Medal
		$awardsdata[] = array(2260914, "X1Inm", 2,
							array(
								array('army', 'count(*)', 1, 'time8 >= 180000*### AND best8 >= 100*### AND win8 >= 50*###')
							)
						);
		# Navy Seal Service Ribbon
		$awardsdata[] = array(3261919, "X1Nss", 1,
							array(
								array('army', 'count(*)', 1, 'time3 >= 180000'),
								array('maps', 'count(*)', 3, 'mapid IN (300,301,304) AND `time` >= 1')
							)
						);
		# SAS Service Ribbon
		$awardsdata[] = array(3261901, "X1Sas", 1,
							array(
								array('army', 'count(*)', 1, 'time4 >= 180000'),
								array('maps', 'count(*)', 3, 'mapid IN (302,303,307) AND `time` >= 1')
							)
						);
		# SPETZNAS Service Ribbon
		$awardsdata[] = array(3261819, "X1Rsz", 1,
							array(
								array('army', 'count(*)', 1, 'time5 >= 180000'),
								array('maps', 'count(*)', 3, 'mapid IN (305,306,307) AND `time` >= 1')
							)
						);
		# MECSF Service Ribbon
		$awardsdata[] = array(3261319, "X1Msf", 1,
							array(
								array('army', 'count(*)', 1, 'time6 >= 180000'),
								array('maps', 'count(*)', 3, 'mapid IN (300,301,304) AND `time` >= 1')
							)
						);
		# Rebel Service Ribbon
		$awardsdata[] = array(3261805, "X1Reb", 1,
							array(
								array('army', 'count(*)', 1, 'time7 >= 180000'),
								array('maps', 'count(*)', 2, 'mapid IN (305,306) AND `time` >= 1')
							)
						);
		# Insurgent Service Ribbon
		$awardsdata[] = array(3260914, "X1Ins", 1,
							array(
								array('army', 'count(*)', 1, 'time8 >= 180000'),
								array('maps', 'count(*)', 2, 'mapid IN (302,303) AND `time` >= 1')
							)
						);
	}
	
	return $awardsdata;
}

?>
