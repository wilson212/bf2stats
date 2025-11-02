from medal_data import *

medal_data += (
#****************************************************************************************
#****************************************************************************************
#****************************************************************************************
#   X P A C K 1      stuff       Ribbons = 3, Medals = 2, Badges = 1
#****************************************************************************************
#****************************************************************************************
#****************************************************************************************
	#Badges
		#Assault Combat Badge
			#Basic
			('1261119_1', 'X1Acb', 1, object_stat ('kits', 'kills', KIT_TYPE_ASSAULT, 6)),

			#Veteran
			('1261119_2', 'X1Acb', 1, 
				f_and(
					has_medal ('1261119_1'),
					global_stat ('ktm-1', 57600),
					object_stat ('kits', 'kills', KIT_TYPE_ASSAULT, 22)
				)
			),

			#Expert
			('1261119_3', 'X1Acb', 1, 
				f_and(
					has_medal ('1261119_2'),
					global_stat ('ktm-1', 360000),
					object_stat ('kits', 'kills', KIT_TYPE_ASSAULT, 44)
				)
			),

		#Anti-Tank Combat Badge
			#Basic
			('1261120_1', 'X1Atcb',	1, object_stat ('kits', 'kills', KIT_TYPE_AT, 11)),

			#Veteran
			('1261120_2', 'X1Atcb',	1, 
				f_and(
					has_medal ('1261120_1'),
					global_stat ('ktm-0', 57600),
					object_stat ('kits', 'kills', KIT_TYPE_AT, 22)
				)
			),

			#Expert
			('1261120_3', 'X1Atcb',	1, 
				f_and(
					has_medal ('1261120_2'),
					global_stat ('ktm-0', 360000),
					object_stat ('kits', 'kills', KIT_TYPE_AT, 44)
				)
			),

		#Sniper Combat Badge
			#Basic
			('1261109_1', 'X1Sncb',	1, object_stat ('kits', 'kills', KIT_TYPE_SNIPER, 11)),

			#Veteran
			('1261109_2', 'X1Sncb',	1, 
				f_and( 
					has_medal ('1261109_1'),
					global_stat ('ktm-6', 57600),
					object_stat ('kits', 'kills', KIT_TYPE_SNIPER, 17)
				)
			),

			#Expert
			('1261109_3', 'X1Sncb',	1, 
				f_and(
					has_medal ('1261109_2'),
					global_stat ('ktm-6', 360000),
					object_stat ('kits', 'kills', KIT_TYPE_SNIPER, 38)
				)
			),

		#Spec Ops Combat Badge
			#Basic
			('1261115_1', 'X1Socb',	1, object_stat ('kits', 'kills', KIT_TYPE_SPECOPS, 6)),

			#Veteran
			('1261115_2', 'X1Socb',	1, 
				f_and(
					has_medal ('1261115_1'),
					global_stat ('ktm-4', 57600),
					object_stat ('kits', 'kills', KIT_TYPE_SPECOPS, 22)
				)
			),

			#Expert
			('1261115_3', 'X1Socb',	1, 
				f_and(
					has_medal ('1261115_2'),
					global_stat ('ktm-4', 360000),
					object_stat ('kits', 'kills', KIT_TYPE_SPECOPS, 44)
				)
			),

		#Support Combat Badge
			#Basic
			('1261121_1', 'X1Sucb',	1, object_stat ('kits', 'kills', KIT_TYPE_SUPPORT, 11)),

			#Veteran
			('1261121_2', 'X1Sucb',	1, 
				f_and(
					has_medal ('1261121_1'),
					global_stat ('ktm-5', 57600),
					object_stat ('kits', 'kills', KIT_TYPE_SUPPORT, 22)
				)
			),

			#Expert
			('1261121_3', 'X1Sucb',	1, 
				f_and(
					has_medal ('1261121_2'),
					global_stat ('ktm-5', 360000),
					object_stat ('kits', 'kills', KIT_TYPE_SUPPORT, 44)
				)
			),

		#Engineer Combat Badge
			#Basic
			('1261105_1', 'X1Ecb', 1, object_stat ('kits', 'kills', KIT_TYPE_ENGINEER, 11)),

			#Veteran
			('1261105_2', 'X1Ecb', 1, 
				f_and( 
					has_medal ('1261105_1'),
					global_stat ('ktm-2', 57600),
					object_stat ('kits', 'kills', KIT_TYPE_ENGINEER, 22)
				)
			),

			#Expert
			('1261105_3', 'X1Ecb', 1, 
				f_and(
					has_medal ('1261105_2'),
					global_stat ('ktm-2', 360000),
					object_stat ('kits', 'kills', KIT_TYPE_ENGINEER, 44)
				)
			),

		#Medic Combat Badge
			#Basic
			('1261113_1', 'X1Mcb', 1, object_stat ('kits', 'kills', KIT_TYPE_MEDIC, 11)),

			#Veteran
			('1261113_2', 'X1Mcb', 1, 
				f_and(
					has_medal ('1261113_1'),
					global_stat ('ktm-3', 57600),
					object_stat ('kits', 'kills', KIT_TYPE_MEDIC, 22)
				)
			),

			#Expert
			('1261113_3', 'X1Mcb', 1, 
				f_and(
					has_medal ('1261113_2'),
					global_stat ('ktm-3', 360000),
					object_stat ('kits', 'kills', KIT_TYPE_MEDIC, 44)
				)
			),
		
		#Tactical Support Combat Badge
			#Basic
			('1260602_1', 'X1fbb', 1, object_stat ('weapons', 'deployed', WEAPON_TYPE_TACTICAL, 10)),

			#Veteran
			('1260602_2', 'X1fbb', 1, 
				f_and(
					has_medal ('1260602_1'),
					f_plus(
						global_stat ('de-6'),
						object_stat ('weapons', 'deployed', WEAPON_TYPE_TACTICAL),
						100
					)
				)
			),

			#Expert	
			('1260602_3', 'X1fbb', 1, 
				f_and(
					has_medal ('1260602_2'),
					f_plus(
						global_stat ('de-6'),
						object_stat ('weapons', 'deployed', WEAPON_TYPE_TACTICAL),
						500
					)
				)
			),

# Added by Chump - nvg time does not register with onEnterVehicle()
		#Night Vision Usage Badge
			#Basic
			#('1261422_1', 'X1nvb',	1, object_stat ('vehicles', 'rtime', VEHICLE_TYPE_NIGHTVISION, 900)),
			#Veteran
			#('1261422_2', 'X1nvb',	1, f_and( 	has_medal ('1261422_1'),
			#					global_stat ('tvn', 57600))),
			#Expert
			#('1261422_3', 'X1nvb',	1, f_and( 	has_medal ('1261422_2'),
			#					global_stat ('tvn', 360000))),

		#Grappling Hook Usage
			#Basic
			('1260708_1', 'X1ghb', 1, object_stat ('weapons', 'deployed', WEAPON_TYPE_GRAPPLINGHOOK, 10)),

			#Veteran
			('1260708_2', 'X1ghb', 1, 
				f_and(
					has_medal ('1260708_1'),
					f_plus(
						global_stat ('de-7'),
						object_stat ('weapons', 'deployed', WEAPON_TYPE_GRAPPLINGHOOK),
						100
					)
				)
			),

			#Expert	
			('1260708_3', 'X1ghb', 1, 
				f_and(
					has_medal ('1260708_2'),
					f_plus(
						global_stat ('de-7'),
						object_stat ('weapons', 'deployed', WEAPON_TYPE_GRAPPLINGHOOK),
						500
					)
				)
			),
	
		#Zip Line Usage
			#Basic
			('1262612_1', 'X1zlb', 1, object_stat ('weapons', 'deployed', WEAPON_TYPE_ZIPLINE, 10)),

			#Veteran
			('1262612_2', 'X1zlb', 1, 
				f_and(
					has_medal ('1262612_1'),
					f_plus(
						global_stat ('de-8'),
						object_stat ('weapons', 'deployed', WEAPON_TYPE_ZIPLINE),
						100
					)
				)
			),

			#Expert	
			('1262612_3', 'X1zlb', 1, 
				f_and(
					has_medal ('1262612_2'),
					f_plus(
						global_stat ('de-8'),
						object_stat ('weapons', 'deployed', WEAPON_TYPE_ZIPLINE),
						500
					)
				)
			),
	
		#Medals

# Added by Chump - for bf2statistics stats
			# Navy Seal Special Service Medal
			# ('2261913',	'X1Nsm',	2, f_and(	global_stat_multiple_times ('atm-3', 180000, '2261913'),
											# global_stat_multiple_times ('abr-3', 100, '2261913'),
											# global_stat_multiple_times ('awn-3', 50, '2261913'))),

			# SAS Special Service Medal
			# ('2261919',	'X1Ssm',	2, f_and(	global_stat_multiple_times ('atm-4', 180000, '2261919'),
											# global_stat_multiple_times ('abr-4', 100, '2261919'),
											# global_stat_multiple_times ('awn-4', 50, '2261919'))),

			# SPETZ Special Service Medal
			# ('2261613',	'X1Spm',	2, f_and(	global_stat_multiple_times ('atm-5', 180000, '2261613'),
											# global_stat_multiple_times ('abr-5', 100, '2261613'),
											# global_stat_multiple_times ('awn-5', 50, '2261613'))),

			# MECSF Special Service Medal
			# ('2261303',	'X1Mcm',	2, f_and(	global_stat_multiple_times ('atm-6', 180000, '2261303'),
											# global_stat_multiple_times ('abr-6', 100, '2261303'),
											# global_stat_multiple_times ('awn-6', 50, '2261303'))),

			# Rebel Special Service Medal
			# ('2261802',	'X1Rbm',	2, f_and(	global_stat_multiple_times ('atm-7', 180000, '2261802'),
											# global_stat_multiple_times ('abr-7', 100, '2261802'),
											# global_stat_multiple_times ('awn-7', 50, '2261802'))),

			# Insurgent Special Service Medal
			# ('2260914',	'X1Inm',	2, f_and(	global_stat_multiple_times ('atm-8', 180000, '2260914'),
											# global_stat_multiple_times ('abr-8', 100, '2260914'),
											# global_stat_multiple_times ('awn-8', 50, '2260914'))),

		#Ribbons

# Added by Chump - for bf2statistics stats
			# Navy Seal Service Ribbon
			# ('3261919',	'X1Nss',	1, f_and(	global_stat_multiple_times ('atm-3', 180000, '3261919'),
											# global_stat('mtm-300', 1),	# SF: devils_perch
											# global_stat('mtm-301', 1),	# SF: iron_gator
											# global_stat('mtm-304', 1),	# SF: leviathan
										# )),

			# SAS Service Ribbon
			# ('3261901',	'X1Sas',	1,  f_and(	global_stat_multiple_times ('atm-4', 180000, '3261919'),
											# global_stat('mtm-302', 1),	# SF: night_flight
											# global_stat('mtm-303', 1),	# SF: warlord
											# global_stat('mtm-307', 1),	# SF: ghost_town
										# )),

			# SPETZNAS Service Ribbon
			# ('3261819',	'X1Rsz',	1,  f_and(	global_stat_multiple_times ('atm-5', 180000, '3261919'),
											# global_stat('mtm-305', 1),	# SF: mass_destruction
											# global_stat('mtm-306', 1),	# SF: surge
											# global_stat('mtm-307', 1),	# SF: ghost_town
										# )),

			# MECSF Service Ribbon
			# ('3261319',	'X1Msf',	1,  f_and(	global_stat_multiple_times ('atm-6', 180000, '3261919'),
											# global_stat('mtm-300', 1),	# SF: devils_perch
											# global_stat('mtm-301', 1),	# SF: iron_gator
											# global_stat('mtm-304', 1),	# SF: leviathan
										# )),

			# Rebel Service Ribbon
			# ('3261805',	'X1Reb',	1,  f_and(	global_stat_multiple_times ('atm-7', 180000, '3261919'),
											# global_stat('mtm-305', 1),	# SF: mass_destruction
											# global_stat('mtm-306', 1),	# SF: surge
										# )),

			# Insurgent Service Ribbon
			# ('3260914',	'X1Ins',	1,  f_and(	global_stat_multiple_times ('atm-8', 180000, '3261919'),
											# global_stat('mtm-302', 1),	# SF: night_flight
											# global_stat('mtm-303', 1),	# SF: warlord
										# )),

			#Crew Service Ribbon
			('3260318',	'X1Csr', 1, 
				f_and(
					player_score ('driverSpecials', 1), 
					player_score ('driverAssists', 20),
					player_score ('kills', 10)
				)
			),

			#Armoured Service Ribbon
			('3260118',	'X1Arr', 1, 
				f_and(
					object_stat ('vehicles', 'rtime', VEHICLE_TYPE_ARMOR, 1200),
					object_stat ('vehicles', 'kills', VEHICLE_TYPE_ARMOR, 22)
				)
			),

			#Aerial Service Ribbon
			('3260105',	'X1Aer', 1, 
				f_and(
					object_stat ('vehicles', 'rtime', VEHICLE_TYPE_AVIATOR, 900),
					object_stat ('vehicles', 'kills', VEHICLE_TYPE_AVIATOR, 22)
				)
			),

			#Helicopter Service Ribbon
			('3260803',	'X1Hsr', 1, 
				f_and(
					object_stat ('vehicles', 'rtime', VEHICLE_TYPE_HELICOPTER, 900),
					object_stat ('vehicles', 'kills', VEHICLE_TYPE_HELICOPTER, 22)
				)
			),

#****************************************************************************************
#****************************************************************************************
#****************************************************************************************
#****************************************************************************************
#****************************************************************************************
#****************************************************************************************

			#end of medals_data
)
