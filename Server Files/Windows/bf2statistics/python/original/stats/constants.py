# stats keys

import host
import string
from bf2 import g_debug



VEHICLE_TYPE_ARMOR 	= 0
VEHICLE_TYPE_AVIATOR	= 1
VEHICLE_TYPE_AIRDEFENSE	= 2
VEHICLE_TYPE_HELICOPTER	= 3
VEHICLE_TYPE_TRANSPORT	= 4
VEHICLE_TYPE_ARTILLERY	= 5
VEHICLE_TYPE_GRNDDEFENSE= 6

VEHICLE_TYPE_PARACHUTE	= 7
VEHICLE_TYPE_SOLDIER	= 8

VEHICLE_TYPE_NIGHTVISION= 9
VEHICLE_TYPE_GASMASK	= 10

NUM_VEHICLE_TYPES 	= 11
VEHICLE_TYPE_UNKNOWN 	= NUM_VEHICLE_TYPES


WEAPON_TYPE_ASSAULT 	= 0
WEAPON_TYPE_ASSAULTGRN	= 1
WEAPON_TYPE_CARBINE	= 2
WEAPON_TYPE_LMG		= 3
WEAPON_TYPE_SNIPER	= 4
WEAPON_TYPE_PISTOL	= 5
WEAPON_TYPE_ATAA	= 6
WEAPON_TYPE_SMG		= 7
WEAPON_TYPE_SHOTGUN	= 8

WEAPON_TYPE_KNIFE	= 10
WEAPON_TYPE_C4		= 11
WEAPON_TYPE_CLAYMORE	= 12
WEAPON_TYPE_HANDGRENADE = 13
WEAPON_TYPE_SHOCKPAD	= 14
WEAPON_TYPE_ATMINE	= 15
WEAPON_TYPE_TARGETING	= 16

WEAPON_TYPE_GRAPPLINGHOOK=17
WEAPON_TYPE_ZIPLINE	=18

WEAPON_TYPE_TACTICAL	=19

NUM_WEAPON_TYPES 	= 20
WEAPON_TYPE_UNKNOWN	= NUM_WEAPON_TYPES


KIT_TYPE_AT		= 0
KIT_TYPE_ASSAULT	= 1
KIT_TYPE_ENGINEER	= 2
KIT_TYPE_MEDIC		= 3
KIT_TYPE_SPECOPS	= 4
KIT_TYPE_SUPPORT 	= 5
KIT_TYPE_SNIPER 	= 6

NUM_KIT_TYPES		= 7
KIT_TYPE_UNKNOWN	= NUM_KIT_TYPES


ARMY_USA		= 0
ARMY_MEC		= 1
ARMY_CHINESE		= 2
ARMY_SEALS		= 3
ARMY_SAS		= 4
ARMY_SPETZNAS		= 5
ARMY_MECSF		= 6
ARMY_REBELS		= 7
ARMY_INSURGENTS		= 8
ARMY_EURO		= 9

NUM_ARMIES		= 10
ARMY_UNKNOWN		= NUM_ARMIES


vehicleTypeMap = {
	"usapc_lav25"		: VEHICLE_TYPE_ARMOR,
	"apc_btr90"		: VEHICLE_TYPE_ARMOR,
	"apc_wz551"		: VEHICLE_TYPE_ARMOR,
	"ustnk_m1a2"		: VEHICLE_TYPE_ARMOR,
	"rutnk_t90"		: VEHICLE_TYPE_ARMOR,
	"tnk_type98"		: VEHICLE_TYPE_ARMOR,
	"usair_f18"		: VEHICLE_TYPE_AVIATOR,
	"ruair_mig29"		: VEHICLE_TYPE_AVIATOR,
	"air_j10"		: VEHICLE_TYPE_AVIATOR,
	"usair_f15"		: VEHICLE_TYPE_AVIATOR,
	"ruair_su34"		: VEHICLE_TYPE_AVIATOR,
	"air_su30mkk"		: VEHICLE_TYPE_AVIATOR,
	"air_f35b"		: VEHICLE_TYPE_AVIATOR,
	"usaav_m6"		: VEHICLE_TYPE_AIRDEFENSE,
	"aav_tunguska"		: VEHICLE_TYPE_AIRDEFENSE,
	"aav_type95"		: VEHICLE_TYPE_AIRDEFENSE,
	"usaas_stinger"		: VEHICLE_TYPE_AIRDEFENSE,
	"igla_djigit"		: VEHICLE_TYPE_AIRDEFENSE,
	"wasp_defence_front"	: VEHICLE_TYPE_AIRDEFENSE,
	"wasp_defence_back"	: VEHICLE_TYPE_AIRDEFENSE,
	"usthe_uh60"		: VEHICLE_TYPE_HELICOPTER,
	"the_mi17"		: VEHICLE_TYPE_HELICOPTER,
	"chthe_z8"		: VEHICLE_TYPE_HELICOPTER,
	"ahe_ah1z"		: VEHICLE_TYPE_HELICOPTER,
	"ahe_havoc"		: VEHICLE_TYPE_HELICOPTER,
	"ahe_z10"		: VEHICLE_TYPE_HELICOPTER,
	"jeep_faav"		: VEHICLE_TYPE_TRANSPORT,
	"usjep_hmmwv"		: VEHICLE_TYPE_TRANSPORT,
	"jep_paratrooper"	: VEHICLE_TYPE_TRANSPORT,
	"jep_mec_paratrooper"	: VEHICLE_TYPE_TRANSPORT,
	"jep_vodnik"		: VEHICLE_TYPE_TRANSPORT,
	"jep_nanjing"		: VEHICLE_TYPE_TRANSPORT,
	"uslcr_lcac"		: VEHICLE_TYPE_TRANSPORT,
	"boat_rib"		: VEHICLE_TYPE_TRANSPORT,
	"usart_lw155"		: VEHICLE_TYPE_ARTILLERY,
	"ars_d30"		: VEHICLE_TYPE_ARTILLERY,
	"ats_tow"		: VEHICLE_TYPE_GRNDDEFENSE,
	"ats_hj8"		: VEHICLE_TYPE_GRNDDEFENSE,
	"hmg_m2hb"		: VEHICLE_TYPE_GRNDDEFENSE,
	"chhmg_kord"		: VEHICLE_TYPE_GRNDDEFENSE,
	"mec_bipod"		: VEHICLE_TYPE_GRNDDEFENSE,
	"us_bipod"		: VEHICLE_TYPE_GRNDDEFENSE,
	"ch_bipod"		: VEHICLE_TYPE_GRNDDEFENSE,
	"us_soldier"		: VEHICLE_TYPE_SOLDIER,
	"us_heavy_soldier"	: VEHICLE_TYPE_SOLDIER,
	"us_light_soldier"	: VEHICLE_TYPE_SOLDIER,
	"mec_soldier"		: VEHICLE_TYPE_SOLDIER,
	"mec_light_soldier"	: VEHICLE_TYPE_SOLDIER,
	"mec_heavy_soldier"	: VEHICLE_TYPE_SOLDIER,
	"ch_soldier"		: VEHICLE_TYPE_SOLDIER,
	"ch_light_soldier"	: VEHICLE_TYPE_SOLDIER,
	"ch_heavy_soldier"	: VEHICLE_TYPE_SOLDIER,
	"parachute"		: VEHICLE_TYPE_PARACHUTE,
#xpack1 stuff	
	"seal_soldier"		: VEHICLE_TYPE_SOLDIER,
	"seal_heavy_soldier"	: VEHICLE_TYPE_SOLDIER,
	"sas_soldier"		: VEHICLE_TYPE_SOLDIER,
	"sas_heavy_soldier"	: VEHICLE_TYPE_SOLDIER,
	"spetz_soldier"		: VEHICLE_TYPE_SOLDIER,
	"spetz_heavy_soldier"	: VEHICLE_TYPE_SOLDIER,
	"mecsf_soldier"		: VEHICLE_TYPE_SOLDIER,
	"mecsf_heavy_soldier"	: VEHICLE_TYPE_SOLDIER,
	"chinsurgent_soldier"		: VEHICLE_TYPE_SOLDIER,
	"chinsurgent_heavy_soldier"	: VEHICLE_TYPE_SOLDIER,
	"meinsurgent_soldier"		: VEHICLE_TYPE_SOLDIER,
	"meinsurgent_heavy_soldier"	: VEHICLE_TYPE_SOLDIER,

	"xpak_bmp3"		: VEHICLE_TYPE_ARMOR,
	"xpak_forklift"		: VEHICLE_TYPE_TRANSPORT,
	"xpak_atv"		: VEHICLE_TYPE_TRANSPORT,
	"xpak_civ1"		: VEHICLE_TYPE_TRANSPORT,
	"xpak_civ2"		: VEHICLE_TYPE_TRANSPORT,
	"xpak_jetski"		: VEHICLE_TYPE_TRANSPORT,
	"xpak_ailraider"	: VEHICLE_TYPE_TRANSPORT,
	"xpak_apache"		: VEHICLE_TYPE_HELICOPTER,
	"xpak_hind"		: VEHICLE_TYPE_HELICOPTER,
	"xpak_hummertow"	: VEHICLE_TYPE_TRANSPORT,

# booster pack 1
	"xpak2_vbl"		: VEHICLE_TYPE_TRANSPORT,
	"xpak2_tnkl2a6"		: VEHICLE_TYPE_ARMOR,
	"xpak2_tnkc2"		: VEHICLE_TYPE_ARMOR,
	"xpak2_tiger"		: VEHICLE_TYPE_HELICOPTER,
	"xpak2_lynx"		: VEHICLE_TYPE_HELICOPTER,
	"xpak2_eurofighter"	: VEHICLE_TYPE_AVIATOR,
	"xpak2_harrier"		: VEHICLE_TYPE_AVIATOR,
	"eu_soldier"		: VEHICLE_TYPE_SOLDIER,
	"eu_heavy_soldier"	: VEHICLE_TYPE_SOLDIER,
	
# booster pack 2
	"air_a10"		: VEHICLE_TYPE_AVIATOR,
	"air_su39"		: VEHICLE_TYPE_AVIATOR,
	"xpak2_fantan"		: VEHICLE_TYPE_AVIATOR,
	"che_wz11"		: VEHICLE_TYPE_HELICOPTER,
	"she_ec635"		: VEHICLE_TYPE_HELICOPTER,
	"she_littlebird"	: VEHICLE_TYPE_HELICOPTER,
	"xpak2_musclecar"	: VEHICLE_TYPE_TRANSPORT,
	"xpak2_semi"		: VEHICLE_TYPE_TRANSPORT,
}

weaponTypeMap = {
	"usrif_m16a2"		: WEAPON_TYPE_ASSAULT,
	"rurif_ak101"		: WEAPON_TYPE_ASSAULT,
	"rurif_ak47"		: WEAPON_TYPE_ASSAULT,
	"usrif_sa80"		: WEAPON_TYPE_ASSAULT,
	"usrif_g3a3"		: WEAPON_TYPE_ASSAULT,
	"usrif_m203"		: WEAPON_TYPE_ASSAULT,
	"rurif_gp30"		: WEAPON_TYPE_ASSAULT,
	"rurif_gp25"		: WEAPON_TYPE_ASSAULT,
	"usrgl_m203"		: WEAPON_TYPE_ASSAULTGRN,
	"rurgl_gp30"		: WEAPON_TYPE_ASSAULTGRN,
	"rurgl_gp25"		: WEAPON_TYPE_ASSAULTGRN,
	"rurrif_ak74u"		: WEAPON_TYPE_CARBINE,
	"usrif_m4"		: WEAPON_TYPE_CARBINE,
	"rurif_ak74u"		: WEAPON_TYPE_CARBINE,
	"chrif_type95"		: WEAPON_TYPE_CARBINE,
	"usrif_g36c"		: WEAPON_TYPE_CARBINE,
	"uslmg_m249saw"		: WEAPON_TYPE_LMG,
	"rulmg_rpk74"		: WEAPON_TYPE_LMG,
	"chlmg_type95"		: WEAPON_TYPE_LMG,
	"rulmg_pkm"		: WEAPON_TYPE_LMG,
	"usrif_m24"		: WEAPON_TYPE_SNIPER,
	"rurif_dragunov"	: WEAPON_TYPE_SNIPER,
	"chsni_type88"		: WEAPON_TYPE_SNIPER,
	"ussni_m82a1"		: WEAPON_TYPE_SNIPER,
	"ussni_m95_barret"	: WEAPON_TYPE_SNIPER,
	"uspis_92fs"		: WEAPON_TYPE_PISTOL,
	"uspis_92fs_silencer"	: WEAPON_TYPE_PISTOL,
	"rupis_baghira"		: WEAPON_TYPE_PISTOL,
	"rupis_baghira_silencer": WEAPON_TYPE_PISTOL,
	"chpis_qsz92"		: WEAPON_TYPE_PISTOL,
	"chpis_qsz92_silencer"	: WEAPON_TYPE_PISTOL,
	"usatp_predator"	: WEAPON_TYPE_ATAA,
	"chat_eryx"		: WEAPON_TYPE_ATAA,
	"usrif_mp5_a3"		: WEAPON_TYPE_SMG,
	"rurif_bizon"		: WEAPON_TYPE_SMG,
	"chrif_type85"		: WEAPON_TYPE_SMG,
	"usrif_remington11-87"	: WEAPON_TYPE_SHOTGUN,
	"rusht_saiga12"		: WEAPON_TYPE_SHOTGUN,
	"chsht_norinco982"	: WEAPON_TYPE_SHOTGUN,
	"chsht_protecta"	: WEAPON_TYPE_SHOTGUN,
	"ussht_jackhammer"	: WEAPON_TYPE_SHOTGUN,
	"kni_knife"		: WEAPON_TYPE_KNIFE,
	"c4_explosives"		: WEAPON_TYPE_C4,
	"ushgr_m67"		: WEAPON_TYPE_HANDGRENADE,
	"usmin_claymore"	: WEAPON_TYPE_CLAYMORE,
	"defibrillator"		: WEAPON_TYPE_SHOCKPAD,
	"at_mine"		: WEAPON_TYPE_ATMINE,
	"simrad"		: WEAPON_TYPE_TARGETING,
	
# xpack1 stuff
	"nshgr_flashbang"	: WEAPON_TYPE_TACTICAL,
	"sasrif_teargas"	: WEAPON_TYPE_TACTICAL,
	"insgr_rpg"		: WEAPON_TYPE_ATAA,
	"nsrif_crossbow"	: WEAPON_TYPE_ZIPLINE,
	"rurif_oc14"		: WEAPON_TYPE_ASSAULT,
	"sasrif_fn2000"		: WEAPON_TYPE_ASSAULT,
	"sasgr_fn2000"		: WEAPON_TYPE_ASSAULTGRN,
	"sasrif_g36e"		: WEAPON_TYPE_ASSAULT,
	"sasrif_g36k"		: WEAPON_TYPE_ASSAULT,
	"sasrif_mg36"		: WEAPON_TYPE_LMG,
	"sasrif_mp7"		: WEAPON_TYPE_SMG,
	"spzrif_aps"		: WEAPON_TYPE_ASSAULT,
	"usrif_fnscarh"		: WEAPON_TYPE_ASSAULT,
	"usrif_fnscarl"		: WEAPON_TYPE_CARBINE,
	
# xpack1 unlocks
	"insgr_rpg"		: WEAPON_TYPE_ATAA,
	"rurif_oc14"		: WEAPON_TYPE_ASSAULT,
	"sasrif_fn2000"		: WEAPON_TYPE_ASSAULT,
	"sasgr_fn2000"		: WEAPON_TYPE_ASSAULTGRN,
	"sasrif_g36e"		: WEAPON_TYPE_ASSAULT,
	"sasrif_g36k"		: WEAPON_TYPE_ASSAULT,
	"sasrif_mg36"		: WEAPON_TYPE_LMG,
	"sasrif_mp7"		: WEAPON_TYPE_SMG,
	"spzrif_aps"		: WEAPON_TYPE_ASSAULT,
	"usrif_fnscarh"		: WEAPON_TYPE_ASSAULT,
	"usrif_fnscarl"		: WEAPON_TYPE_CARBINE,

# booster pack 1
	"eurif_fnp90"		: WEAPON_TYPE_SMG,
	"eurif_hk53a3"		: WEAPON_TYPE_CARBINE,
	"gbrif_benelli_m4"	: WEAPON_TYPE_SHOTGUN,
	"gbrif_l96a1"		: WEAPON_TYPE_SNIPER,
	"eurif_famas"		: WEAPON_TYPE_ASSAULT,
	"gbrif_sa80a2_l85"	: WEAPON_TYPE_ASSAULT,
	"gbgr_sa80a2_l85"	: WEAPON_TYPE_ASSAULTGRN,
	"gbrif_hk21"		: WEAPON_TYPE_LMG

}


kitTypeMap = {
	"us_at"		: KIT_TYPE_AT,
	"us_assault"	: KIT_TYPE_ASSAULT,
	"us_engineer"	: KIT_TYPE_ENGINEER,
	"us_medic"	: KIT_TYPE_MEDIC,
	"us_specops"	: KIT_TYPE_SPECOPS,
	"us_support"	: KIT_TYPE_SUPPORT,
	"us_sniper"	: KIT_TYPE_SNIPER,
	"mec_at" 	: KIT_TYPE_AT,
	"mec_assault"	: KIT_TYPE_ASSAULT,
	"mec_engineer"	: KIT_TYPE_ENGINEER,
	"mec_medic"	: KIT_TYPE_MEDIC,
	"mec_specops"	: KIT_TYPE_SPECOPS,
	"mec_support"	: KIT_TYPE_SUPPORT,
	"mec_sniper"	: KIT_TYPE_SNIPER,
	"ch_at" 	: KIT_TYPE_AT,
	"ch_assault"	: KIT_TYPE_ASSAULT,
	"ch_engineer"	: KIT_TYPE_ENGINEER,
	"ch_medic"	: KIT_TYPE_MEDIC,
	"ch_specops"	: KIT_TYPE_SPECOPS,
	"ch_support"	: KIT_TYPE_SUPPORT,
	"ch_sniper"	: KIT_TYPE_SNIPER,
# xpack1
	"seal_at" 	: KIT_TYPE_AT,
	"seal_assault"	: KIT_TYPE_ASSAULT,
	"seal_engineer": KIT_TYPE_ENGINEER,
	"seal_medic"	: KIT_TYPE_MEDIC,
	"seal_specops"	: KIT_TYPE_SPECOPS,
	"seal_support"	: KIT_TYPE_SUPPORT,
	"seal_sniper"	: KIT_TYPE_SNIPER,
	"sas_at" 	: KIT_TYPE_AT,
	"sas_assault"	: KIT_TYPE_ASSAULT,
	"sas_engineer"	: KIT_TYPE_ENGINEER,
	"sas_medic"	: KIT_TYPE_MEDIC,
	"sas_specops"	: KIT_TYPE_SPECOPS,
	"sas_support"	: KIT_TYPE_SUPPORT,
	"sas_sniper"	: KIT_TYPE_SNIPER,
	"spetsnaz_at" 	: KIT_TYPE_AT,
	"spetsnaz_assault"	: KIT_TYPE_ASSAULT,
	"spetsnaz_engineer": KIT_TYPE_ENGINEER,
	"spetsnaz_medic"	: KIT_TYPE_MEDIC,
	"spetsnaz_specops"	: KIT_TYPE_SPECOPS,
	"spetsnaz_support"	: KIT_TYPE_SUPPORT,
	"spetsnaz_sniper"	: KIT_TYPE_SNIPER,
	"mecsf_at" 	: KIT_TYPE_AT,
	"mecsf_assault"	: KIT_TYPE_ASSAULT,
	"mecsf_engineer": KIT_TYPE_ENGINEER,
	"mecsf_medic"	: KIT_TYPE_MEDIC,
	"mecsf_specops"	: KIT_TYPE_SPECOPS,
	"mecsf_support"	: KIT_TYPE_SUPPORT,
	"mecsf_sniper"	: KIT_TYPE_SNIPER,
	"chinsurgent_at" 	: KIT_TYPE_AT,
	"chinsurgent_assault"	: KIT_TYPE_ASSAULT,
	"chinsurgent_engineer"	: KIT_TYPE_ENGINEER,
	"chinsurgent_medic"	: KIT_TYPE_MEDIC,
	"chinsurgent_specops"	: KIT_TYPE_SPECOPS,
	"chinsurgent_support"	: KIT_TYPE_SUPPORT,
	"chinsurgent_sniper"	: KIT_TYPE_SNIPER,
	"meinsurgent_at" 	: KIT_TYPE_AT,
	"meinsurgent_assault"	: KIT_TYPE_ASSAULT,
	"meinsurgent_engineer"	: KIT_TYPE_ENGINEER,
	"meinsurgent_medic"	: KIT_TYPE_MEDIC,
	"meinsurgent_specops"	: KIT_TYPE_SPECOPS,
	"meinsurgent_support"	: KIT_TYPE_SUPPORT,
	"meinsurgent_sniper"	: KIT_TYPE_SNIPER,
	"mecsf_at_special" 	: KIT_TYPE_AT,
	"mecsf_assault_special"	: KIT_TYPE_ASSAULT,
	"mecsf_specops_special"	: KIT_TYPE_SPECOPS,
	"mecsf_sniper_special"	: KIT_TYPE_SNIPER,
	"sas_at_special" 	: KIT_TYPE_AT,
	"sas_assault_special"	: KIT_TYPE_ASSAULT,
	"sas_specops_special"	: KIT_TYPE_SPECOPS,
	"sas_sniper_special"	: KIT_TYPE_SNIPER,
# booster pack 1
	"eu_at" 	: KIT_TYPE_AT,
	"eu_assault"	: KIT_TYPE_ASSAULT,
	"eu_engineer"	: KIT_TYPE_ENGINEER,
	"eu_medic"	: KIT_TYPE_MEDIC,
	"eu_specops"	: KIT_TYPE_SPECOPS,
	"eu_support"	: KIT_TYPE_SUPPORT,
	"eu_sniper"	: KIT_TYPE_SNIPER
}

armyMap = {
	"us"		: ARMY_USA,
	"mec"		: ARMY_MEC,
	"ch"		: ARMY_CHINESE,
# xpack1 
	"seal"		: ARMY_SEALS,
	"sas"		: ARMY_SAS,
	"spetz"		: ARMY_SPETZNAS,
	"mecsf"		: ARMY_MECSF,
	"chinsurgent"	: ARMY_REBELS,
	"meinsurgent"   : ARMY_INSURGENTS,
# booster pack 1
	"eu"		: ARMY_EURO
}

mapMap = {
	# middle eastern theater
	"kubra_dam"		: "0",
	"mashtuur_city"		: "1",
	"operation_clean_sweep" : "2",
	"zatar_wetlands"	: "3",
	"strike_at_karkand"	: "4",
	"sharqi_peninsula"	: "5",
	"gulf_of_oman"		: "6",
	"operationsmokescreen"  : "10",
	"taraba_quarry"		: "11",
	"road_to_jalalabad"	: "12",

	# Asian Theater
	"daqing_oilfields"	: "100",
	"dalian_plant"		: "101",
	"dragon_valley"		: "102",
	"fushe_pass"		: "103",
	"hingan_hills"		: "104",
	"songhua_stalemate"	: "105",
	"greatwall"		: "110",
	"operation_blue_pearl"		: "120",
	
	# US Theatre
	"midnight_sun"		: "200",
	"operationroadrage"	: "201",
	"operationharvest"	: "202",

	#xpack 1
	"devils_perch"		: "300",
	"iron_gator"		: "301",
	"night_flight"		: "302",
	"warlord"		: "303",
	"leviathan"		: "304",
	"mass_destruction"	: "305",
	"surge"			: "306",
	"ghost_town"		: "307",

	# Special maps
	"wake_island_2007"	: "601",
	"highway_tampa"	: "602",
}
UNKNOWN_MAP = 99

gameModeMap = {
	"gpm_cq"		: 0,
	"gpm_sl"		: 1,
}
UNKNOWN_GAMEMODE = 99



def getVehicleType(templateName):
	try:
		vehicleType = vehicleTypeMap[string.lower(templateName)]
	except KeyError:
		return VEHICLE_TYPE_UNKNOWN
	
	return vehicleType


	
def getWeaponType(templateName):
	try:
		weaponType = weaponTypeMap[string.lower(templateName)]
	except KeyError:
		return WEAPON_TYPE_UNKNOWN
	
	return weaponType	
	
	
	
def getKitType(templateName):	
	try:
		kitType = kitTypeMap[string.lower(templateName)]
	except KeyError:
		return KIT_TYPE_UNKNOWN
	
	return kitType	
	
	
	
def getArmy(templateName):
	try:
		army = armyMap[string.lower(templateName)]
	except KeyError:
		return ARMY_UNKNOWN
	
	return army



def getMapId(mapName):
	try:
		mapId = mapMap[string.lower(mapName)]
	except KeyError:
		return UNKNOWN_MAP
	
	return mapId



def getGameModeId(gameMode):
	try:
		gameModeId = gameModeMap[string.lower(gameMode)]
	except KeyError:
		return UNKNOWN_GAMEMODE
	
	return gameModeId



def getRootParent(obj):
	parent = obj.getParent()
	
	if parent == None:
		return obj
		
	return getRootParent(parent)



if g_debug: print "Stat constants loaded"
