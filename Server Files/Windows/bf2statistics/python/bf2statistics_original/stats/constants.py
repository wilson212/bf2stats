##########################################################################################
#
# constants.py 2010-03-10
# von http://wiki.sgiersch.de/index.php/BF2Statistics_constants.py#
#
# Erstellt von
# ++ Thinner - http://www.bf2statistics.com/user.php?id.2900 ++
# ++ Leon_tbk - http://www.bf2statistics.com/user.php?id.4870 ++
#
# Informationen ueber die Veraenderungen gibts hier:
# http://wiki.sgiersch.de/index.php/Diskussion:BF2Statistics_constants.py
# 
##########################################################################################
# Basiert auf:
# stats keys
# aus der Original Datei von dem Packet BF2Statistics Update v1.4.2 
########################################################################################## 


import host
from bf2 import g_debug


VEHICLE_TYPE_ARMOR          = 0
VEHICLE_TYPE_AVIATOR        = 1
VEHICLE_TYPE_AIRDEFENSE     = 2
VEHICLE_TYPE_HELICOPTER     = 3
VEHICLE_TYPE_TRANSPORT      = 4
VEHICLE_TYPE_ARTILLERY      = 5
VEHICLE_TYPE_GRNDDEFENSE    = 6
VEHICLE_TYPE_PARACHUTE      = 7
VEHICLE_TYPE_SOLDIER        = 8
VEHICLE_TYPE_NIGHTVISION    = 9
VEHICLE_TYPE_GASMASK        = 10
NUM_VEHICLE_TYPES           = 11
VEHICLE_TYPE_UNKNOWN        = NUM_VEHICLE_TYPES


WEAPON_TYPE_ASSAULT         = 0
WEAPON_TYPE_ASSAULTGRN      = 1
WEAPON_TYPE_CARBINE         = 2
WEAPON_TYPE_LMG             = 3
WEAPON_TYPE_SNIPER          = 4
WEAPON_TYPE_PISTOL          = 5
WEAPON_TYPE_ATAA            = 6
WEAPON_TYPE_SMG             = 7
WEAPON_TYPE_SHOTGUN         = 8
WEAPON_TYPE_KNIFE           = 10
WEAPON_TYPE_C4              = 11
WEAPON_TYPE_CLAYMORE        = 12
WEAPON_TYPE_HANDGRENADE     = 13
WEAPON_TYPE_SHOCKPAD        = 14
WEAPON_TYPE_ATMINE          = 15
WEAPON_TYPE_TARGETING       = 16
WEAPON_TYPE_GRAPPLINGHOOK   = 17
WEAPON_TYPE_ZIPLINE         = 18
WEAPON_TYPE_TACTICAL        = 19
# Hard Justice
#WEAPON_TYPE_APMINE         = 20
#WEAPON_TYPE_AIRDEFENSE     = 21
#WEAPON_TYPE_POISIONGAS     = 22
NUM_WEAPON_TYPES            = 20
WEAPON_TYPE_UNKNOWN         = NUM_WEAPON_TYPES


KIT_TYPE_AT             = 0
KIT_TYPE_ASSAULT        = 1
KIT_TYPE_ENGINEER       = 2
KIT_TYPE_MEDIC          = 3
KIT_TYPE_SPECOPS        = 4
KIT_TYPE_SUPPORT        = 5
KIT_TYPE_SNIPER         = 6
NUM_KIT_TYPES           = 7
KIT_TYPE_UNKNOWN        = NUM_KIT_TYPES


# Battlefield2
ARMY_USA            = 0
ARMY_MEC            = 1
ARMY_CHINESE        = 2
# xpack1 - SpecialForces
ARMY_SEALS          = 3
ARMY_SAS            = 4
ARMY_SPETZNAS       = 5
ARMY_MECSF          = 6
ARMY_REBELS         = 7
ARMY_INSURGENTS     = 8
# booster pack 1 - Euroforces
ARMY_EURO           = 9
# POE
ARMY_GER            = 10
ARMY_UKR            = 11
# AIX
ARMY_UN             = 12
# Hard Justice
ARMY_CANADIAN       = 13
NUM_ARMIES          = 14
ARMY_UNKNOWN        = NUM_ARMIES


vehicleTypeMap = {
# Battlefield2
    "usapc_lav25"                   : VEHICLE_TYPE_ARMOR,
    "apc_btr90"                     : VEHICLE_TYPE_ARMOR,
    "apc_wz551"                     : VEHICLE_TYPE_ARMOR,
    "ustnk_m1a2"                    : VEHICLE_TYPE_ARMOR,
    "rutnk_t90"                     : VEHICLE_TYPE_ARMOR,
    "tnk_type98"                    : VEHICLE_TYPE_ARMOR,
    "usair_f18"                     : VEHICLE_TYPE_AVIATOR,
    "ruair_mig29"                   : VEHICLE_TYPE_AVIATOR,
    "air_j10"                       : VEHICLE_TYPE_AVIATOR,
    "usair_f15"                     : VEHICLE_TYPE_AVIATOR,
    "ruair_su34"                    : VEHICLE_TYPE_AVIATOR,
    "air_su30mkk"                   : VEHICLE_TYPE_AVIATOR,
    "air_f35b"                      : VEHICLE_TYPE_AVIATOR,
    "usaav_m6"                      : VEHICLE_TYPE_AIRDEFENSE,
    "aav_tunguska"                  : VEHICLE_TYPE_AIRDEFENSE,
    "aav_type95"                    : VEHICLE_TYPE_AIRDEFENSE,
    "usaas_stinger"                 : VEHICLE_TYPE_AIRDEFENSE,
    "igla_djigit"                   : VEHICLE_TYPE_AIRDEFENSE,
    "wasp_defence_front"            : VEHICLE_TYPE_AIRDEFENSE,
    "wasp_defence_back"             : VEHICLE_TYPE_AIRDEFENSE,
    "usthe_uh60"                    : VEHICLE_TYPE_HELICOPTER,
    "the_mi17"                      : VEHICLE_TYPE_HELICOPTER,
    "chthe_z8"                      : VEHICLE_TYPE_HELICOPTER,
    "ahe_ah1z"                      : VEHICLE_TYPE_HELICOPTER,
    "ahe_havoc"                     : VEHICLE_TYPE_HELICOPTER,
    "ahe_z10"                       : VEHICLE_TYPE_HELICOPTER,
    "jeep_faav"                     : VEHICLE_TYPE_TRANSPORT,
    "usjep_hmmwv"                   : VEHICLE_TYPE_TRANSPORT,
    "jep_paratrooper"               : VEHICLE_TYPE_TRANSPORT,
    "jep_mec_paratrooper"           : VEHICLE_TYPE_TRANSPORT,
    "jep_vodnik"                    : VEHICLE_TYPE_TRANSPORT,
    "jep_nanjing"                   : VEHICLE_TYPE_TRANSPORT,
    "uslcr_lcac"                    : VEHICLE_TYPE_TRANSPORT,
    "boat_rib"                      : VEHICLE_TYPE_TRANSPORT,
    "usart_lw155"                   : VEHICLE_TYPE_ARTILLERY,
    "ars_d30"                       : VEHICLE_TYPE_ARTILLERY,
    "ats_tow"                       : VEHICLE_TYPE_GRNDDEFENSE,
    "ats_hj8"                       : VEHICLE_TYPE_GRNDDEFENSE,
    "hmg_m2hb"                      : VEHICLE_TYPE_GRNDDEFENSE,
    "chhmg_kord"                    : VEHICLE_TYPE_GRNDDEFENSE,
    "mec_bipod"                     : VEHICLE_TYPE_GRNDDEFENSE,
    "us_bipod"                      : VEHICLE_TYPE_GRNDDEFENSE,
    "ch_bipod"                      : VEHICLE_TYPE_GRNDDEFENSE,
    "us_soldier"                    : VEHICLE_TYPE_SOLDIER,
    "us_heavy_soldier"              : VEHICLE_TYPE_SOLDIER,
    "us_light_soldier"              : VEHICLE_TYPE_SOLDIER,
    "mec_soldier"                   : VEHICLE_TYPE_SOLDIER,
    "mec_light_soldier"             : VEHICLE_TYPE_SOLDIER,
    "mec_heavy_soldier"             : VEHICLE_TYPE_SOLDIER,
    "ch_soldier"                    : VEHICLE_TYPE_SOLDIER,
    "ch_light_soldier"              : VEHICLE_TYPE_SOLDIER,
    "ch_heavy_soldier"              : VEHICLE_TYPE_SOLDIER,
    "parachute"                     : VEHICLE_TYPE_PARACHUTE,
# xpack1 - SpecialForces
    "seal_soldier"                  : VEHICLE_TYPE_SOLDIER,
    "seal_heavy_soldier"            : VEHICLE_TYPE_SOLDIER,
    "sas_soldier"                   : VEHICLE_TYPE_SOLDIER,
    "sas_heavy_soldier"             : VEHICLE_TYPE_SOLDIER,
    "spetz_soldier"                 : VEHICLE_TYPE_SOLDIER,
    "spetz_heavy_soldier"           : VEHICLE_TYPE_SOLDIER,
    "mecsf_soldier"                 : VEHICLE_TYPE_SOLDIER,
    "mecsf_heavy_soldier"           : VEHICLE_TYPE_SOLDIER,
    "chinsurgent_soldier"           : VEHICLE_TYPE_SOLDIER,
    "chinsurgent_heavy_soldier"     : VEHICLE_TYPE_SOLDIER,
    "meinsurgent_soldier"           : VEHICLE_TYPE_SOLDIER,
    "meinsurgent_heavy_soldier"     : VEHICLE_TYPE_SOLDIER,
    "xpak_bmp3"                     : VEHICLE_TYPE_ARMOR,
    "xpak_forklift"                 : VEHICLE_TYPE_TRANSPORT,
    "xpak_atv"                      : VEHICLE_TYPE_TRANSPORT,
    "xpak_civ1"                     : VEHICLE_TYPE_TRANSPORT,
    "xpak_civ2"                     : VEHICLE_TYPE_TRANSPORT,
    "xpak_jetski"                   : VEHICLE_TYPE_TRANSPORT,
    "xpak_ailraider"                : VEHICLE_TYPE_TRANSPORT,
    "xpak_apache"                   : VEHICLE_TYPE_HELICOPTER,
    "xpak_hind"                     : VEHICLE_TYPE_HELICOPTER,
    "xpak_hummertow"                : VEHICLE_TYPE_TRANSPORT,
# booster pack 1 - Euroforces
    "xpak2_vbl"                     : VEHICLE_TYPE_TRANSPORT,
    "xpak2_tnkl2a6"                 : VEHICLE_TYPE_ARMOR,
    "xpak2_tnkc2"                   : VEHICLE_TYPE_ARMOR,
    "xpak2_tiger"                   : VEHICLE_TYPE_HELICOPTER,
    "xpak2_lynx"                    : VEHICLE_TYPE_HELICOPTER,
    "xpak2_eurofighter"             : VEHICLE_TYPE_AVIATOR,
    "xpak2_harrier"                 : VEHICLE_TYPE_AVIATOR,
    "eu_soldier"                    : VEHICLE_TYPE_SOLDIER,
    "eu_heavy_soldier"              : VEHICLE_TYPE_SOLDIER,
# booster pack 2 - ArmoredFury
    "air_a10"                       : VEHICLE_TYPE_AVIATOR,
    "air_su39"                      : VEHICLE_TYPE_AVIATOR,
    "xpak2_fantan"                  : VEHICLE_TYPE_AVIATOR,
    "che_wz11"                      : VEHICLE_TYPE_HELICOPTER,
    "she_ec635"                     : VEHICLE_TYPE_HELICOPTER,
    "she_littlebird"                : VEHICLE_TYPE_HELICOPTER,
    "xpak2_musclecar"               : VEHICLE_TYPE_TRANSPORT,
    "xpak2_semi"                    : VEHICLE_TYPE_TRANSPORT,
# POE2
    "gerair_ef2000"                 : VEHICLE_TYPE_AVIATOR,
    "gerair_tornado"                : VEHICLE_TYPE_AVIATOR,
    "gerhe_eurotigerarh"            : VEHICLE_TYPE_HELICOPTER,
    "gerhe_nh90"                    : VEHICLE_TYPE_TRANSPORT,
    "ufo"                           : VEHICLE_TYPE_HELICOPTER,
    "ukrair_mig25"                  : VEHICLE_TYPE_AVIATOR,
    "ukrair_su24"                   : VEHICLE_TYPE_AVIATOR,
    "ukrair_su25"                   : VEHICLE_TYPE_AVIATOR,
    "ukrhe_mi24p"                   : VEHICLE_TYPE_HELICOPTER, 
    "civsctr"                       : VEHICLE_TYPE_ARMOR,
    "geraav_gepard"                 : VEHICLE_TYPE_AIRDEFENSE,
    "gerapc_boxerGTK"               : VEHICLE_TYPE_TRANSPORT,
    "gerapc_marder1a5"              : VEHICLE_TYPE_ARMOR,
    "gerartil_pzh2000"              : VEHICLE_TYPE_ARTILLERY,
    "gerjeep_dingo"                 : VEHICLE_TYPE_TRANSPORT,
    "gerjeep_wolf"                  : VEHICLE_TYPE_TRANSPORT,
    "gerjeep_wolfsoft"              : VEHICLE_TYPE_TRANSPORT,
    "gertnk_leopard"                : VEHICLE_TYPE_ARMOR,
    "snowmobile"                    : VEHICLE_TYPE_TRANSPORT,
    "ukraav_mtlb_sa13_v2"           : VEHICLE_TYPE_ARMOR,
    "ukraav_shilka"                 : VEHICLE_TYPE_AIRDEFENSE,
    "ukrapc_bmp2"                   : VEHICLE_TYPE_TRANSPORT,
    "ukrapc_mtlb"                   : VEHICLE_TYPE_TRANSPORT,
    "ukrartil_m1974"                : VEHICLE_TYPE_ARTILLERY,
    "ukrartil_msta"                 : VEHICLE_TYPE_ARTILLERY,
    "ukrjeep_dozer"                 : VEHICLE_TYPE_TRANSPORT,
    "ukrjeep_uaz"                   : VEHICLE_TYPE_TRANSPORT,
    "ukrtnk_oplot"                  : VEHICLE_TYPE_ARMOR,
    "ukrtnk_t55"                    : VEHICLE_TYPE_ARMOR,
    "ger_heavy_soldier"             : VEHICLE_TYPE_SOLDIER,
    "ger_light_soldier"             : VEHICLE_TYPE_SOLDIER,
    "ukr_heavy_soldier"             : VEHICLE_TYPE_SOLDIER,
    "ukr_light_soldier"             : VEHICLE_TYPE_SOLDIER,
    "aa_zu23"                       : VEHICLE_TYPE_GRNDDEFENSE,
    "gerartil_fh70"                 : VEHICLE_TYPE_GRNDDEFENSE,
    "mg3_coax"                      : VEHICLE_TYPE_GRNDDEFENSE,
    "remote_kord"                   : VEHICLE_TYPE_GRNDDEFENSE,
    "remote_mg3"                    : VEHICLE_TYPE_GRNDDEFENSE,
#AIX 1.0
    "ahe_ah1x"                      : VEHICLE_TYPE_HELICOPTER,
    "ahe_ghost"                     : VEHICLE_TYPE_HELICOPTER,
    "ahe_roc"                       : VEHICLE_TYPE_HELICOPTER,
    "ahe_storm"                     : VEHICLE_TYPE_HELICOPTER,
    "ahe_v10"                       : VEHICLE_TYPE_HELICOPTER,
    "aix_ah64"                      : VEHICLE_TYPE_HELICOPTER,
    "aix_ah64gunship"               : VEHICLE_TYPE_HELICOPTER,
    "aix_ka50"                      : VEHICLE_TYPE_HELICOPTER,
    "aix_notar_littlebird"          : VEHICLE_TYPE_HELICOPTER,
    "aix_notar_littlebird_trans"    : VEHICLE_TYPE_HELICOPTER,
    "blizzard"                      : VEHICLE_TYPE_HELICOPTER,
    "chahe_a8"                      : VEHICLE_TYPE_HELICOPTER,
    "usahe_ah60"                    : VEHICLE_TYPE_HELICOPTER,
    "aix_a10"                       : VEHICLE_TYPE_AVIATOR,
    "aix_a10b"                      : VEHICLE_TYPE_AVIATOR,
    "aix_av8b"                      : VEHICLE_TYPE_AVIATOR,
    "aix_draken"                    : VEHICLE_TYPE_AVIATOR,
    "aix_f117a"                     : VEHICLE_TYPE_AVIATOR,
    "aix_f16"                       : VEHICLE_TYPE_AVIATOR,
    "aix_f16lg"                     : VEHICLE_TYPE_AVIATOR,
    "aix_f5tiger"                   : VEHICLE_TYPE_AVIATOR,
    "aix_gr7"                       : VEHICLE_TYPE_AVIATOR,
    "aix_mig19"                     : VEHICLE_TYPE_AVIATOR,
    "aix_mig21"                     : VEHICLE_TYPE_AVIATOR,
    "aix_mig23"                     : VEHICLE_TYPE_AVIATOR,
    "aix_mirage2k"                  : VEHICLE_TYPE_AVIATOR,
    "aix_mirage_iii"                : VEHICLE_TYPE_AVIATOR,
    "aix_su21"                      : VEHICLE_TYPE_AVIATOR,
    "albatros_diii"                 : VEHICLE_TYPE_AVIATOR,
    "fokker_dr1"                    : VEHICLE_TYPE_AVIATOR,
    "fokker_eiii"                   : VEHICLE_TYPE_AVIATOR,
    "mig21m"                        : VEHICLE_TYPE_AVIATOR,
    "spad_xiii"                     : VEHICLE_TYPE_AVIATOR,
    "aix_atv"                       : VEHICLE_TYPE_TRANSPORT,
    "asset_pco"                     : VEHICLE_TYPE_ARMOR,
    "bradley"                       : VEHICLE_TYPE_ARMOR,
    "maws"                          : VEHICLE_TYPE_ARMOR,
    "rms"                           : VEHICLE_TYPE_ARMOR,
    "usaas_stinger_no_exit"         : VEHICLE_TYPE_AIRDEFENSE,
    "ch_hmg"                        : VEHICLE_TYPE_GRNDDEFENSE,
    "mec_hmg"                       : VEHICLE_TYPE_GRNDDEFENSE,
    "us_hmg"                        : VEHICLE_TYPE_GRNDDEFENSE,
    "m224_mortar"                   : VEHICLE_TYPE_ARTILLERY,
    "art_fieldcannon"               : VEHICLE_TYPE_ARTILLERY,
    "art_truckcannon"               : VEHICLE_TYPE_ARTILLERY,
    "ch_assault_soldier"            : VEHICLE_TYPE_SOLDIER,
    "ch_at_soldier"                 : VEHICLE_TYPE_SOLDIER,
    "ch_engineer_soldier"           : VEHICLE_TYPE_SOLDIER,
    "ch_medic_soldier"              : VEHICLE_TYPE_SOLDIER,
    "ch_sniper_soldier"             : VEHICLE_TYPE_SOLDIER,
    "ch_specops_soldier"            : VEHICLE_TYPE_SOLDIER,
    "ch_support_soldier"            : VEHICLE_TYPE_SOLDIER,
    "mec_assault_soldier"           : VEHICLE_TYPE_SOLDIER,
    "mec_at_soldier"                : VEHICLE_TYPE_SOLDIER,
    "mec_engineer_soldier"          : VEHICLE_TYPE_SOLDIER,
    "mec_medic_soldier"             : VEHICLE_TYPE_SOLDIER,
    "mec_sniper_soldier"            : VEHICLE_TYPE_SOLDIER,
    "mec_specops_soldier"           : VEHICLE_TYPE_SOLDIER,
    "mec_support_soldier"           : VEHICLE_TYPE_SOLDIER,
    "un_assault_soldier"            : VEHICLE_TYPE_SOLDIER,
    "un_at_soldier"                 : VEHICLE_TYPE_SOLDIER,
    "un_engineer_soldier"           : VEHICLE_TYPE_SOLDIER,
    "un_medic_soldier"              : VEHICLE_TYPE_SOLDIER,
    "un_sniper_soldier"             : VEHICLE_TYPE_SOLDIER,
    "un_specops_soldier"            : VEHICLE_TYPE_SOLDIER,
    "un_support_soldier"            : VEHICLE_TYPE_SOLDIER,
    "us_assault_soldier"            : VEHICLE_TYPE_SOLDIER,
    "us_at_soldier"                 : VEHICLE_TYPE_SOLDIER,
    "us_engineer_soldier"           : VEHICLE_TYPE_SOLDIER,
    "us_medic_soldier"              : VEHICLE_TYPE_SOLDIER,
    "us_sniper_soldier"             : VEHICLE_TYPE_SOLDIER,
    "us_specops_soldier"            : VEHICLE_TYPE_SOLDIER,
    "us_support_soldier"            : VEHICLE_TYPE_SOLDIER,
#AIX 2.0
    "aix_f16-ns"                    : VEHICLE_TYPE_AVIATOR,
    "hawkextras"                    : VEHICLE_TYPE_AVIATOR,
    "aix_be12"                      : VEHICLE_TYPE_AVIATOR,
    "aix_su47"                      : VEHICLE_TYPE_AVIATOR,
    "aix_su47-ns"                   : VEHICLE_TYPE_AVIATOR,
    "aix_yak38"                     : VEHICLE_TYPE_AVIATOR,
    "aix_mig19-ns"                  : VEHICLE_TYPE_AVIATOR,
    "aix_a10-ns"                    : VEHICLE_TYPE_AVIATOR,
    "mig21"                         : VEHICLE_TYPE_AVIATOR,
    "mirage"                        : VEHICLE_TYPE_AVIATOR,
    "aix_viggen"                    : VEHICLE_TYPE_AVIATOR,
    "aix_firefox"                   : VEHICLE_TYPE_AVIATOR,
    "aix_f12x"                      : VEHICLE_TYPE_AVIATOR,
    "a8_extras"                     : VEHICLE_TYPE_HELICOPTER,
    "blizzardextras"                : VEHICLE_TYPE_HELICOPTER,
    "blizzardextras"                : VEHICLE_TYPE_HELICOPTER,
    "aix_mh53j"                     : VEHICLE_TYPE_HELICOPTER,
    "aix_mi24"                      : VEHICLE_TYPE_HELICOPTER,
    "jeep_faav_hf"                  : VEHICLE_TYPE_TRANSPORT,
    "aix_atv2"                      : VEHICLE_TYPE_TRANSPORT,
    "jeep_technical"                : VEHICLE_TYPE_TRANSPORT,
    "us_minigun"                    : VEHICLE_TYPE_GRNDDEFENSE,
#Hard Justice
    "us2_soldier"                   : VEHICLE_TYPE_SOLDIER,
    "us2_heavy_soldier"             : VEHICLE_TYPE_SOLDIER,
    "us2_light_soldier"             : VEHICLE_TYPE_SOLDIER,
    "mec2_soldier"                  : VEHICLE_TYPE_SOLDIER,
    "mec2_light_soldier"            : VEHICLE_TYPE_SOLDIER,
    "mec2_heavy_soldier"            : VEHICLE_TYPE_SOLDIER,
    "ch2_soldier"                   : VEHICLE_TYPE_SOLDIER,
    "ch2_light_soldier"             : VEHICLE_TYPE_SOLDIER,
    "ch2_heavy_soldier"             : VEHICLE_TYPE_SOLDIER,
    "us3_soldier"                   : VEHICLE_TYPE_SOLDIER,
    "us3_heavy_soldier"             : VEHICLE_TYPE_SOLDIER,
    "us3_light_soldier"             : VEHICLE_TYPE_SOLDIER,
    "mec3_soldier"                  : VEHICLE_TYPE_SOLDIER,
    "mec3_light_soldier"            : VEHICLE_TYPE_SOLDIER,
    "mec3_heavy_soldier"            : VEHICLE_TYPE_SOLDIER,
    "ch3_soldier"                   : VEHICLE_TYPE_SOLDIER,
    "ch3_light_soldier"             : VEHICLE_TYPE_SOLDIER,
    "ch3_heavy_soldier"             : VEHICLE_TYPE_SOLDIER,
    "ca_soldier"                    : VEHICLE_TYPE_SOLDIER,
    "ca_heavy_soldier"              : VEHICLE_TYPE_SOLDIER,
    "rah66a"                        : VEHICLE_TYPE_HELICOPTER,
    "ah6c"                          : VEHICLE_TYPE_HELICOPTER,
    "ah6j"                          : VEHICLE_TYPE_HELICOPTER,
    "sa342f"                        : VEHICLE_TYPE_HELICOPTER,
    "sa342a"                        : VEHICLE_TYPE_HELICOPTER,
    "m270"                          : VEHICLE_TYPE_ARTILLERY,
    "tos1"                          : VEHICLE_TYPE_ARTILLERY,
    "f22a"                          : VEHICLE_TYPE_AVIATOR,
    "bradly"                        : VEHICLE_TYPE_ARMOR,
    "m270_m109h"                    : VEHICLE_TYPE_ARTILLERY,
    "apc_cobra"                     : VEHICLE_TYPE_ARMOR,
    "apc_cobraat"                   : VEHICLE_TYPE_ARMOR,
    "a10w"                          : VEHICLE_TYPE_AVIATOR,
    "a10w1"                         : VEHICLE_TYPE_AVIATOR,
    "a10w2"                         : VEHICLE_TYPE_AVIATOR,
    "oelikonaa"                     : VEHICLE_TYPE_AIRDEFENSE,
    "rh202_aa"                      : VEHICLE_TYPE_AIRDEFENSE,
    "humvee_aaag"                   : VEHICLE_TYPE_TRANSPORT,
    "dirtbike"                      : VEHICLE_TYPE_TRANSPORT,
    "naw_apache"                    : VEHICLE_TYPE_HELICOPTER,
    "su25"                          : VEHICLE_TYPE_AVIATOR,
    "su25sc"                        : VEHICLE_TYPE_AVIATOR,
    "baja_bug"                      : VEHICLE_TYPE_TRANSPORT,
    "challenger"                    : VEHICLE_TYPE_TRANSPORT,
    "zero_quad"                     : VEHICLE_TYPE_TRANSPORT,
    "zero_quad125cc"                : VEHICLE_TYPE_TRANSPORT,
    "zero_quad250cc"                : VEHICLE_TYPE_TRANSPORT,
    "aa_technical"                  : VEHICLE_TYPE_TRANSPORT,
    "tow_technical"                 : VEHICLE_TYPE_TRANSPORT,
    "usjep_amrpr"                   : VEHICLE_TYPE_TRANSPORT,
    "woodyswagon"                   : VEHICLE_TYPE_ARTILLERY,
    "defense_gun"                   : VEHICLE_TYPE_ARTILLERY,
    "humvee_aa_ag"                  : VEHICLE_TYPE_ARMOR,
    "civ2_tow"                      : VEHICLE_TYPE_GRNDDEFENSE,
# AIX 2.0 TNG Maps
    "aix_su22"                      : VEHICLE_TYPE_AVIATOR,
    "aix_phantom_ii_v2_un_wso"      : VEHICLE_TYPE_AVIATOR,
    "aix_mirage2k_v2"               : VEHICLE_TYPE_AVIATOR,
    "aix_draken_v2"                 : VEHICLE_TYPE_AVIATOR,
    "aix_av8b_un"                   : VEHICLE_TYPE_AVIATOR,
    "aix_a7"                        : VEHICLE_TYPE_AVIATOR,
    "aix_a10_v2"                    : VEHICLE_TYPE_AVIATOR,
    "aix_a10b_v2"                   : VEHICLE_TYPE_AVIATOR,
    "aix_av8b_v2"                   : VEHICLE_TYPE_AVIATOR,
    "aix_f14"                       : VEHICLE_TYPE_AVIATOR,
    "aix_f14_rio"                   : VEHICLE_TYPE_AVIATOR,
    "aix_f16_v2"                    : VEHICLE_TYPE_AVIATOR,
    "aix_f16lg_v2"                  : VEHICLE_TYPE_AVIATOR,
    "aix_f5tiger_v2"                : VEHICLE_TYPE_AVIATOR,
    "aix_mig23_v2"                  : VEHICLE_TYPE_AVIATOR,
    "aix_mirage_iii_v2"             : VEHICLE_TYPE_AVIATOR,
    "aix_phantom_ii_v2_un"          : VEHICLE_TYPE_AVIATOR,
    "aix_su21_v2"                   : VEHICLE_TYPE_AVIATOR,
    "aix_su47_v2"                   : VEHICLE_TYPE_AVIATOR,
    "aix_mig21_v2"                  : VEHICLE_TYPE_AVIATOR,
    "aix_f117a_v2"                  : VEHICLE_TYPE_AVIATOR,
    "aix_mig19_v2"                  : VEHICLE_TYPE_AVIATOR,
    "aix_viggen_v2"                 : VEHICLE_TYPE_AVIATOR,
    "rh202_aa"                      : VEHICLE_TYPE_AIRDEFENSE,
# AIX 2.0 TNG 2.0
    "dirtbike_un"                   : VEHICLE_TYPE_TRANSPORT,
    "unthe_uh60"                    : VEHICLE_TYPE_HELICOPTER,
    "aix_notar_lb_un"               : VEHICLE_TYPE_HELICOPTER,
    "untnk_m1a2"                    : VEHICLE_TYPE_ARMOR,
    "unjep_hmmwv"                   : VEHICLE_TYPE_TRANSPORT,
    "unapc_lav25"                   : VEHICLE_TYPE_ARMOR,
    "aav_type95_v2"                 : VEHICLE_TYPE_AIRDEFENSE,
    "unaav_m6"                      : VEHICLE_TYPE_AIRDEFENSE,
    "aix_be12_v2"                   : VEHICLE_TYPE_AVIATOR,
    "aix_ah64_v2"                   : VEHICLE_TYPE_HELICOPTER,
    "aix_ah64gunship_v2"            : VEHICLE_TYPE_HELICOPTER,
    "m1a2_v2"                       : VEHICLE_TYPE_ARMOR
}

weaponTypeMap = {
# Battlefield2
    "usrif_m16a2"                   : WEAPON_TYPE_ASSAULT,
    "rurif_ak101"                   : WEAPON_TYPE_ASSAULT,
    "rurif_ak47"                    : WEAPON_TYPE_ASSAULT,
    "usrif_sa80"                    : WEAPON_TYPE_ASSAULT,
    "usrif_g3a3"                    : WEAPON_TYPE_ASSAULT,
    "usrif_m203"                    : WEAPON_TYPE_ASSAULT,
    "rurif_gp30"                    : WEAPON_TYPE_ASSAULT,
    "rurif_gp25"                    : WEAPON_TYPE_ASSAULT,
    "usrgl_m203"                    : WEAPON_TYPE_ASSAULTGRN,
    "rurgl_gp30"                    : WEAPON_TYPE_ASSAULTGRN,
    "rurgl_gp25"                    : WEAPON_TYPE_ASSAULTGRN,
    "rurrif_ak74u"                  : WEAPON_TYPE_CARBINE,
    "usrif_m4"                      : WEAPON_TYPE_CARBINE,
    "rurif_ak74u"                   : WEAPON_TYPE_CARBINE,
    "chrif_type95"                  : WEAPON_TYPE_CARBINE,
    "usrif_g36c"                    : WEAPON_TYPE_CARBINE,
    "uslmg_m249saw"                 : WEAPON_TYPE_LMG,
    "rulmg_rpk74"                   : WEAPON_TYPE_LMG,
    "chlmg_type95"                  : WEAPON_TYPE_LMG,
    "rulmg_pkm"                     : WEAPON_TYPE_LMG,
    "usrif_m24"                     : WEAPON_TYPE_SNIPER,
    "rurif_dragunov"                : WEAPON_TYPE_SNIPER,
    "chsni_type88"                  : WEAPON_TYPE_SNIPER,
    "ussni_m82a1"                   : WEAPON_TYPE_SNIPER,
    "ussni_m95_barret"              : WEAPON_TYPE_SNIPER,
    "uspis_92fs"                    : WEAPON_TYPE_PISTOL,
    "uspis_92fs_silencer"           : WEAPON_TYPE_PISTOL,
    "rupis_baghira"                 : WEAPON_TYPE_PISTOL,
    "rupis_baghira_silencer"        : WEAPON_TYPE_PISTOL,
    "chpis_qsz92"                   : WEAPON_TYPE_PISTOL,
    "chpis_qsz92_silencer"          : WEAPON_TYPE_PISTOL,
    "usatp_predator"                : WEAPON_TYPE_ATAA,
    "chat_eryx"                     : WEAPON_TYPE_ATAA,
    "usrif_mp5_a3"                  : WEAPON_TYPE_SMG,
    "rurif_bizon"                   : WEAPON_TYPE_SMG,
    "chrif_type85"                  : WEAPON_TYPE_SMG,
    "usrif_remington11-87"          : WEAPON_TYPE_SHOTGUN,
    "rusht_saiga12"                 : WEAPON_TYPE_SHOTGUN,
    "chsht_norinco982"              : WEAPON_TYPE_SHOTGUN,
    "chsht_protecta"                : WEAPON_TYPE_SHOTGUN,
    "ussht_jackhammer"              : WEAPON_TYPE_SHOTGUN,
    "kni_knife"                     : WEAPON_TYPE_KNIFE,
    "c4_explosives"                 : WEAPON_TYPE_C4,
    "ushgr_m67"                     : WEAPON_TYPE_HANDGRENADE,
    "usmin_claymore"                : WEAPON_TYPE_CLAYMORE,
    "defibrillator"                 : WEAPON_TYPE_SHOCKPAD,
    "at_mine"                       : WEAPON_TYPE_ATMINE,
    "simrad"                        : WEAPON_TYPE_TARGETING,
# xpack1 - SpecialForces
    "nshgr_flashbang"               : WEAPON_TYPE_TACTICAL,
    "sasrif_teargas"                : WEAPON_TYPE_TACTICAL,
    "insgr_rpg"                     : WEAPON_TYPE_ATAA,
    "nsrif_crossbow"                : WEAPON_TYPE_ZIPLINE,
    "rurif_oc14"                    : WEAPON_TYPE_ASSAULT,
    "sasrif_fn2000"                 : WEAPON_TYPE_ASSAULT,
    "sasgr_fn2000"                  : WEAPON_TYPE_ASSAULTGRN,
    "sasrif_g36e"                   : WEAPON_TYPE_ASSAULT,
    "sasrif_g36k"                   : WEAPON_TYPE_ASSAULT,
    "sasrif_mg36"                   : WEAPON_TYPE_LMG,
    "sasrif_mp7"                    : WEAPON_TYPE_SMG,
    "spzrif_aps"                    : WEAPON_TYPE_ASSAULT,
    "usrif_fnscarh"                 : WEAPON_TYPE_ASSAULT,
    "usrif_fnscarl"                 : WEAPON_TYPE_CARBINE,
# booster pack 1 - Euroforces
    "eurif_fnp90"                   : WEAPON_TYPE_SMG,
    "eurif_hk53a3"                  : WEAPON_TYPE_CARBINE,
    "gbrif_benelli_m4"              : WEAPON_TYPE_SHOTGUN,
    "gbrif_l96a1"                   : WEAPON_TYPE_SNIPER,
    "eurif_famas"                   : WEAPON_TYPE_ASSAULT,
    "gbrif_sa80a2_l85"              : WEAPON_TYPE_ASSAULT,
    "gbgr_sa80a2_l85"               : WEAPON_TYPE_ASSAULTGRN,
    "eurif_hk21"                    : WEAPON_TYPE_LMG,
# POE2
    "at_mine2"                      : WEAPON_TYPE_ATMINE,
    "gergre_dm61"                   : WEAPON_TYPE_HANDGRENADE,
    "gergrl_ag36"                   : WEAPON_TYPE_ASSAULTGRN,
    "gerkni_km2000"                 : WEAPON_TYPE_KNIFE,
    "gerlmg_mg3"                    : WEAPON_TYPE_LMG,
    "gerlmg_mg36"                   : WEAPON_TYPE_LMG,
    "gerpis_p8"                     : WEAPON_TYPE_PISTOL,
    "gerrif_g36"                    : WEAPON_TYPE_ASSAULT,
    "gerrif_g36c"                   : WEAPON_TYPE_CARBINE ,
    "gerrif_g36k"                   : WEAPON_TYPE_ASSAULT,
    "gerrif_msg90"                  : WEAPON_TYPE_SNIPER,
    "gerroc_bunkerfaust"            : WEAPON_TYPE_ATAA,
    "gerroc_fliegerfaust2"          : WEAPON_TYPE_ATAA,
    "gerroc_panzerfaust3"           : WEAPON_TYPE_ATAA,
    "gerroc_panzerfaust3t"          : WEAPON_TYPE_ATAA,
    "gersni_g82"                    : WEAPON_TYPE_CARBINE,
    "gergre_smoke"                  : WEAPON_TYPE_TACTICAL,
    "gergre_smoke2"                 : WEAPON_TYPE_TACTICAL,
    "katana"                        : WEAPON_TYPE_KNIFE,
    "ruskni_expknife"               : WEAPON_TYPE_KNIFE,
    "ukrgre_rdg2"                   : WEAPON_TYPE_ASSAULTGRN,
    "ukrgre_rdg2_2"                 : WEAPON_TYPE_ASSAULTGRN,
    "ukrgre_rgd5"                   : WEAPON_TYPE_ASSAULTGRN,
    "ukrgrl_gp25"                   : WEAPON_TYPE_ASSAULTGRN,
    "ukrlmg_pkm"                    : WEAPON_TYPE_LMG,
    "ukrlmg_rpk74"                  : WEAPON_TYPE_LMG,
    "ukrpis_fort12"                 : WEAPON_TYPE_PISTOL,
    "ukrpis_pb6p9"                  : WEAPON_TYPE_PISTOL,
    "ukrrif_aks74u"                 : WEAPON_TYPE_CARBINE,
    "ukrrif_pp2000"                 : WEAPON_TYPE_SMG,
    "ukrrif_pp2000_2"               : WEAPON_TYPE_SMG,
    "ukrrif_svd"                    : WEAPON_TYPE_SNIPER,
    "ukrrif_skorpion"               : WEAPON_TYPE_SNIPER,
    "ukrrif_vepr"                   : WEAPON_TYPE_ASSAULT,
    "ukrrif_vintorez"               : WEAPON_TYPE_SNIPER,
    "ukrroc_rpgfrag"                : WEAPON_TYPE_ATAA,
    "ukrroc_rpgheat"                : WEAPON_TYPE_ATAA,
    "ukrroc_rpgtandem"              : WEAPON_TYPE_ATAA,
    "ukrroc_rpgthermo"              : WEAPON_TYPE_ATAA,
    "ukrroc_sa7"                    : WEAPON_TYPE_ATAA,
    "ukrsht_toz194"                 : WEAPON_TYPE_SHOTGUN,
    "ukrsmg_asval"                  : WEAPON_TYPE_ASSAULT,
    "ukrsni_ntw20"                  : WEAPON_TYPE_ASSAULT,
    "usasht_m1014"                  : WEAPON_TYPE_SHOTGUN,
    "usasmg_mp7"                    : WEAPON_TYPE_SMG,
    "usasmg_mp7_2"                  : WEAPON_TYPE_SMG,
    "usasmg_mp7_scoped"             : WEAPON_TYPE_SMG,
    "usasmg_mp7_silenced"           : WEAPON_TYPE_SMG,
    "usmin_claymore2"               : WEAPON_TYPE_CLAYMORE,
    "usrif_g36c"                    : WEAPON_TYPE_ASSAULT,
#AIX 1.0
    "aix_ak5_tactical"              : WEAPON_TYPE_ASSAULT,
    "aix_famas"                     : WEAPON_TYPE_ASSAULT,
    "aix_fs2000"                    : WEAPON_TYPE_ASSAULT,
    "aix_g36k_rif"                  : WEAPON_TYPE_ASSAULT,
    "aix_m41a"                      : WEAPON_TYPE_ASSAULT,
    "aix_mk14ebr"                   : WEAPON_TYPE_ASSAULT,
    "aix_scarl_rif"                 : WEAPON_TYPE_ASSAULT,
    "aix_steyr_aug"                 : WEAPON_TYPE_ASSAULT,
    "chrif_type95_b"                : WEAPON_TYPE_ASSAULT,
    "rurif_ak47_b"                  : WEAPON_TYPE_ASSAULT,
    "aix_g36k_gl"                   : WEAPON_TYPE_ASSAULTGRN,
    "aix_mgl140"                    : WEAPON_TYPE_ASSAULTGRN,
    "aix_scarl_gl"                  : WEAPON_TYPE_ASSAULTGRN,
    "aix_as50"                      : WEAPON_TYPE_SNIPER,
    "aix_barrett_m109"              : WEAPON_TYPE_SNIPER,
    "aix_dsr"                       : WEAPON_TYPE_SNIPER,
    "aix_beretta"                   : WEAPON_TYPE_PISTOL,
    "aix_beretta_silencer"          : WEAPON_TYPE_PISTOL,
    "aix_glock19"                   : WEAPON_TYPE_PISTOL,
    "aix_glock19_silencer"          : WEAPON_TYPE_PISTOL,
    "aix_gsh"                       : WEAPON_TYPE_PISTOL,
    "aix_gsh_silencer"              : WEAPON_TYPE_PISTOL,
    "aix_uspmatch"                  : WEAPON_TYPE_PISTOL,
    "aix_uspmatch_silencer"         : WEAPON_TYPE_PISTOL,
    "aix_fim92a"                    : WEAPON_TYPE_ATAA,
    "aix_rpg7"                      : WEAPON_TYPE_ATAA,
    "aix_strela2"                   : WEAPON_TYPE_ATAA,
    "chat_eryx_lt"                  : WEAPON_TYPE_ATAA,
    "rurpg_rpg7"                    : WEAPON_TYPE_ATAA,
    "mortar_deployable"             : WEAPON_TYPE_ATAA,    
    "aix_g36v"                      : WEAPON_TYPE_CARBINE,
    "aix_hk416"                     : WEAPON_TYPE_CARBINE,
    "aix_sig552"                    : WEAPON_TYPE_CARBINE,
    "aix_xm8"                       : WEAPON_TYPE_CARBINE,
    "rurif_ak47u_b"                 : WEAPON_TYPE_CARBINE,
    "tavor"                         : WEAPON_TYPE_CARBINE,
    "aix_grenade1"                  : WEAPON_TYPE_HANDGRENADE,
    "aix_m41a_shot"                 : WEAPON_TYPE_SHOTGUN,
    "aix_mac11"                     : WEAPON_TYPE_SMG,
    "rurif_ak101_b"                 : WEAPON_TYPE_SMG,
    "aix_portableminigun"           : WEAPON_TYPE_LMG,
    "aix_portableminigun_mec"       : WEAPON_TYPE_LMG,
    "aix_stg58"                     : WEAPON_TYPE_LMG,
    "aix_tpg1"                      : WEAPON_TYPE_SNIPER,
    "at4_mine"                      : WEAPON_TYPE_ATMINE,
    "binoculars_mec_ch"             : WEAPON_TYPE_TARGETING,
    "us_binocular"                  : WEAPON_TYPE_TACTICAL,
    "us_flaretrap"                  : WEAPON_TYPE_TACTICAL,
    "c4_timebomb"                   : WEAPON_TYPE_C4,
    "hgr_flashbang"                 : WEAPON_TYPE_TACTICAL,
    "ch_flaretrap"                  : WEAPON_TYPE_TACTICAL,
    "hgr_incendiary"                : WEAPON_TYPE_TACTICAL,
    "hgr_incendiary_sticky"         : WEAPON_TYPE_TACTICAL,
    "hgr_smoke_orange"              : WEAPON_TYPE_TACTICAL,
    "hgr_smoke_purple"              : WEAPON_TYPE_TACTICAL,
    "hgr_smoke_yellow"              : WEAPON_TYPE_TACTICAL,
    "hgr_teargas"                   : WEAPON_TYPE_TACTICAL,
    "mec_flaretrap"                 : WEAPON_TYPE_TACTICAL,
    "grapplinghook"                 : WEAPON_TYPE_GRAPPLINGHOOK,
    "throwknife"                    : WEAPON_TYPE_KNIFE,
#AIX 2.0
    "aix_flaretrap"                 : WEAPON_TYPE_TACTICAL,
    "aix_kimber"                    : WEAPON_TYPE_PISTOL,
    "aix_kimber_silencer"           : WEAPON_TYPE_PISTOL,
    "aix_magpul"                    : WEAPON_TYPE_ASSAULT,
    "aix_tavor"                     : WEAPON_TYPE_ASSAULT,
    "aix_sig552specops"             : WEAPON_TYPE_CARBINE,
    "aix_type97"                    : WEAPON_TYPE_CARBINE,
    "aix_type97_mg"                 : WEAPON_TYPE_LMG,
    "aix_p90"                       : WEAPON_TYPE_SMG,
    "aix_vintorez"                  : WEAPON_TYPE_SNIPER,
#Hard Justice stuff
    #"sa7"                          : WEAPON_TYPE_AIRDEFENSE,
    "m14lm"                         : WEAPON_TYPE_CLAYMORE,
    #"hgr_gas"                      : WEAPON_TYPE_POISIONGAS,
    "deserteagal"                   : WEAPON_TYPE_PISTOL,
    "sasrif_fn20001"                : WEAPON_TYPE_ASSAULT,
    "sasgr_fn20001"                 : WEAPON_TYPE_ASSAULTGRN,
    "gbrif_sa80a21_l85"             : WEAPON_TYPE_ASSAULT,
    "gbgr_sa80a21_l85"              : WEAPON_TYPE_ASSAULTGRN,
    "m95_barret"                    : WEAPON_TYPE_SNIPER,
    "designator"                    : WEAPON_TYPE_TARGETING,
    "javelin"                       : WEAPON_TYPE_ATAA,
    "javelin_direct"                : WEAPON_TYPE_ATAA,
    "hgr_smoke2"                    : WEAPON_TYPE_TACTICAL,
    "mk19"                          : WEAPON_TYPE_LMG,
    "usatp_predator2"               : WEAPON_TYPE_ATAA,
    "chhmg_type85"                  : WEAPON_TYPE_SMG,
    "usrif_mp5_a3_2"                : WEAPON_TYPE_SMG,
# AIX 2.0 TNG Maps
    "aix_portableminigun_v2"        : WEAPON_TYPE_LMG,
    "aix_portableminigun_mec_v2"    : WEAPON_TYPE_LMG,
    "aix_type97_v2"                 : WEAPON_TYPE_CARBINE,
    "aix_type97_mg_v2"              : WEAPON_TYPE_LMG,
    "aix_tavor_v2"                  : WEAPON_TYPE_CARBINE,
    "aix_g36k_rif_v2"               : WEAPON_TYPE_ASSAULT,
    "aix_fs2000_v2"                 : WEAPON_TYPE_ASSAULT,
    "aix_dsr_v2"                    : WEAPON_TYPE_SNIPER,
# AIX 2.0 TNG 2.0
    "aix_scarl_rif_v2"              : WEAPON_TYPE_ASSAULT,
    "sasrif_g36e_v2"                : WEAPON_TYPE_ASSAULT,
    "aix_ak5_tactical_v2"           : WEAPON_TYPE_ASSAULT
}


kitTypeMap = {
    "us_at"                         : KIT_TYPE_AT,
    "us_assault"                    : KIT_TYPE_ASSAULT,
    "us_engineer"                   : KIT_TYPE_ENGINEER,
    "us_medic"                      : KIT_TYPE_MEDIC,
    "us_specops"                    : KIT_TYPE_SPECOPS,
    "us_support"                    : KIT_TYPE_SUPPORT,
    "us_sniper"                     : KIT_TYPE_SNIPER,
    "mec_at"                        : KIT_TYPE_AT,
    "mec_assault"                   : KIT_TYPE_ASSAULT,
    "mec_engineer"                  : KIT_TYPE_ENGINEER,
    "mec_medic"                     : KIT_TYPE_MEDIC,
    "mec_specops"                   : KIT_TYPE_SPECOPS,
    "mec_support"                   : KIT_TYPE_SUPPORT,
    "mec_sniper"                    : KIT_TYPE_SNIPER,
    "ch_at"                         : KIT_TYPE_AT,
    "ch_assault"                    : KIT_TYPE_ASSAULT,
    "ch_engineer"                   : KIT_TYPE_ENGINEER,
    "ch_medic"                      : KIT_TYPE_MEDIC,
    "ch_specops"                    : KIT_TYPE_SPECOPS,
    "ch_support"                    : KIT_TYPE_SUPPORT,
    "ch_sniper"                     : KIT_TYPE_SNIPER,
# xpack1 - SpecialForces
    "seal_at"                       : KIT_TYPE_AT,
    "seal_assault"                  : KIT_TYPE_ASSAULT,
    "seal_engineer"                 : KIT_TYPE_ENGINEER,
    "seal_medic"                    : KIT_TYPE_MEDIC,
    "seal_specops"                  : KIT_TYPE_SPECOPS,
    "seal_support"                  : KIT_TYPE_SUPPORT,
    "seal_sniper"                   : KIT_TYPE_SNIPER,
    "sas_at"                        : KIT_TYPE_AT,
    "sas_assault"                   : KIT_TYPE_ASSAULT,
    "sas_engineer"                  : KIT_TYPE_ENGINEER,
    "sas_medic"                     : KIT_TYPE_MEDIC,
    "sas_specops"                   : KIT_TYPE_SPECOPS,
    "sas_support"                   : KIT_TYPE_SUPPORT,
    "sas_sniper"                    : KIT_TYPE_SNIPER,
    "spetsnaz_at"                   : KIT_TYPE_AT,
    "spetsnaz_assault"              : KIT_TYPE_ASSAULT,
    "spetsnaz_engineer"             : KIT_TYPE_ENGINEER,
    "spetsnaz_medic"                : KIT_TYPE_MEDIC,
    "spetsnaz_specops"              : KIT_TYPE_SPECOPS,
    "spetsnaz_support"              : KIT_TYPE_SUPPORT,
    "spetsnaz_sniper"               : KIT_TYPE_SNIPER,
    "mecsf_at"                      : KIT_TYPE_AT,
    "mecsf_assault"                 : KIT_TYPE_ASSAULT,
    "mecsf_engineer"                : KIT_TYPE_ENGINEER,
    "mecsf_medic"                   : KIT_TYPE_MEDIC,
    "mecsf_specops"                 : KIT_TYPE_SPECOPS,
    "mecsf_support"                 : KIT_TYPE_SUPPORT,
    "mecsf_sniper"                  : KIT_TYPE_SNIPER,
    "chinsurgent_at"                : KIT_TYPE_AT,
    "chinsurgent_assault"           : KIT_TYPE_ASSAULT,
    "chinsurgent_engineer"          : KIT_TYPE_ENGINEER,
    "chinsurgent_medic"             : KIT_TYPE_MEDIC,
    "chinsurgent_specops"           : KIT_TYPE_SPECOPS,
    "chinsurgent_support"           : KIT_TYPE_SUPPORT,
    "chinsurgent_sniper"            : KIT_TYPE_SNIPER,
    "meinsurgent_at"                : KIT_TYPE_AT,
    "meinsurgent_assault"           : KIT_TYPE_ASSAULT,
    "meinsurgent_engineer"          : KIT_TYPE_ENGINEER,
    "meinsurgent_medic"             : KIT_TYPE_MEDIC,
    "meinsurgent_specops"           : KIT_TYPE_SPECOPS,
    "meinsurgent_support"           : KIT_TYPE_SUPPORT,
    "meinsurgent_sniper"            : KIT_TYPE_SNIPER,
    "mecsf_at_special"              : KIT_TYPE_AT,
    "mecsf_assault_special"         : KIT_TYPE_ASSAULT,
    "mecsf_specops_special"         : KIT_TYPE_SPECOPS,
    "mecsf_sniper_special"          : KIT_TYPE_SNIPER,
    "sas_at_special"                : KIT_TYPE_AT,
    "sas_assault_special"           : KIT_TYPE_ASSAULT,
    "sas_specops_special"           : KIT_TYPE_SPECOPS,
    "sas_sniper_special"            : KIT_TYPE_SNIPER,
# booster pack 1 - Euroforces
    "eu_at"                         : KIT_TYPE_AT,
    "eu_assault"                    : KIT_TYPE_ASSAULT,
    "eu_engineer"                   : KIT_TYPE_ENGINEER,
    "eu_medic"                      : KIT_TYPE_MEDIC,
    "eu_specops"                    : KIT_TYPE_SPECOPS,
    "eu_support"                    : KIT_TYPE_SUPPORT,
    "eu_sniper"                     : KIT_TYPE_SNIPER,
# POE2
    "ger_assault"                   : KIT_TYPE_ASSAULT,
    "ger_at"                        : KIT_TYPE_AT,
    "ger_engineer"                  : KIT_TYPE_ENGINEER,
    "ger_medic"                     : KIT_TYPE_MEDIC,
    "ger_sniper"                    : KIT_TYPE_SNIPER,
    "ger_specops"                   : KIT_TYPE_SPECOPS,
    "ger_support"                   : KIT_TYPE_SUPPORT,
    "ukr_at"                        : KIT_TYPE_AT,
    "ukr_assault"                   : KIT_TYPE_ASSAULT,
    "ukr_engineer"                  : KIT_TYPE_ENGINEER,
    "ukr_medic"                     : KIT_TYPE_MEDIC,
    "ukr_specops"                   : KIT_TYPE_SPECOPS,
    "ukr_support"                   : KIT_TYPE_SUPPORT,
    "ukr_sniper"                    : KIT_TYPE_SNIPER,
# AIX 1.0
    "un_at"                         : KIT_TYPE_AT,
    "un_assault"                    : KIT_TYPE_ASSAULT,
    "un_engineer"                   : KIT_TYPE_ENGINEER,
    "un_medic"                      : KIT_TYPE_MEDIC,
    "un_sniper"                     : KIT_TYPE_SNIPER,
    "un_specops"                    : KIT_TYPE_SPECOPS,
    "un_support"                    : KIT_TYPE_SUPPORT,
    # Pickup Kits
    "assault_ak5"                   : KIT_TYPE_ASSAULT,
    "assault_ak5"                   : KIT_TYPE_ASSAULT,
    "assault_ak5"                   : KIT_TYPE_ASSAULT,
    "assault_fn_fal"                : KIT_TYPE_ASSAULT,
    "assault_g36k"                  : KIT_TYPE_ASSAULT,
    "assault_g3a3"                  : KIT_TYPE_ASSAULT,
    "assault_gp25"                  : KIT_TYPE_ASSAULT,
    "assault_gp30"                  : KIT_TYPE_ASSAULT,
    "assault_m16_m203"              : KIT_TYPE_ASSAULT,
    "assault_m41a"                  : KIT_TYPE_ASSAULT,
    "assault_sa80a2"                : KIT_TYPE_ASSAULT,
    "at_bizon"                      : KIT_TYPE_AT,
    "at_eryx_lt"                    : KIT_TYPE_AT,
    "at_mgl140"                     : KIT_TYPE_AT,
    "at_mp5"                        : KIT_TYPE_AT,
    "at_rpg7"                       : KIT_TYPE_AT,
    "at_stinger"                    : KIT_TYPE_AT,
    "at_strela2"                    : KIT_TYPE_AT,
    "engineer_benelli_m4"           : KIT_TYPE_ENGINEER,
    "engineer_famas"                : KIT_TYPE_ENGINEER,
    "engineer_hk416"                : KIT_TYPE_ENGINEER,
    "engineer_jackhammer"           : KIT_TYPE_ENGINEER,
    "engineer_mk14ebr"              : KIT_TYPE_ENGINEER,
    "engineer_norinco982"           : KIT_TYPE_ENGINEER,
    "engineer_protecta"             : KIT_TYPE_ENGINEER,
    "engineer_remington11-87"       : KIT_TYPE_ENGINEER,
    "engineer_saiga12"              : KIT_TYPE_ENGINEER,
    "engineer_tavor"                : KIT_TYPE_ENGINEER,
    "medic_ak101"                   : KIT_TYPE_MEDIC,
    "medic_ak47"                    : KIT_TYPE_MEDIC,
    "medic_fs2000"                  : KIT_TYPE_MEDIC,
    "medic_g36e"                    : KIT_TYPE_MEDIC,
    "medic_m16a2"                   : KIT_TYPE_MEDIC,
    "medic_sa80"                    : KIT_TYPE_MEDIC,
    "medic_steyr_aug"               : KIT_TYPE_MEDIC,
    "sniper_as50"                   : KIT_TYPE_SNIPER,
    "sniper_dragunov"               : KIT_TYPE_SNIPER,
    "sniper_dsr"                    : KIT_TYPE_SNIPER,
    "sniper_l96a1"                  : KIT_TYPE_SNIPER,
    "sniper_m109"                   : KIT_TYPE_SNIPER,
    "sniper_m24"                    : KIT_TYPE_SNIPER,
    "sniper_m82"                    : KIT_TYPE_SNIPER,
    "sniper_m95_barret"             : KIT_TYPE_SNIPER,
    "sniper_tpg1"                   : KIT_TYPE_SNIPER,
    "sniper_type88"                 : KIT_TYPE_SNIPER,
    "specops_aix_famas"             : KIT_TYPE_SPECOPS,
    "specops_ak74u"                 : KIT_TYPE_SPECOPS,
    "specops_fnscarl"               : KIT_TYPE_SPECOPS,
    "specops_g36c"                  : KIT_TYPE_SPECOPS,
    "specops_hk53a3"                : KIT_TYPE_SPECOPS,
    "specops_m4"                    : KIT_TYPE_SPECOPS,
    "specops_sg552"                 : KIT_TYPE_SPECOPS,
    "specops_type95"                : KIT_TYPE_SPECOPS,
    "specops_xm8"                   : KIT_TYPE_SPECOPS,
    "support_hk21"                  : KIT_TYPE_SUPPORT,
    "support_m249saw"               : KIT_TYPE_SUPPORT,
    "support_mg36"                  : KIT_TYPE_SUPPORT,
    "support_minigun"               : KIT_TYPE_SUPPORT,
    "support_minigun_mec"           : KIT_TYPE_SUPPORT,
    "support_pkm"                   : KIT_TYPE_SUPPORT,
    "support_pkm"                   : KIT_TYPE_SUPPORT,
    "support_stg58"                 : KIT_TYPE_SUPPORT,
    "support_type95"                : KIT_TYPE_SUPPORT,
# AIX 2.0
    "ch_assault-inf"                : KIT_TYPE_ASSAULT,
    "ch_at-inf"                     : KIT_TYPE_AT,
    "ch_engineer-inf"               : KIT_TYPE_ENGINEER,
    "ch_sniper-inf"                 : KIT_TYPE_SNIPER,
    "ch_specops-inf"                : KIT_TYPE_SPECOPS,
    "mec_assault-inf"               : KIT_TYPE_ASSAULT,
    "mec_at-inf"                    : KIT_TYPE_AT,
    "mec_engineer-inf"              : KIT_TYPE_ENGINEER,
    "mec_sniper-inf"                : KIT_TYPE_SNIPER,
    "mec_specops-inf"               : KIT_TYPE_SPECOPS,
    "mec_support_pkm"               : KIT_TYPE_SUPPORT,
    "un_assault-inf"                : KIT_TYPE_ASSAULT,
    "un_at-inf"                     : KIT_TYPE_AT,
    "un_engineer-inf"               : KIT_TYPE_ENGINEER,
    "un_sniper-inf"                 : KIT_TYPE_SNIPER,
    "un_specops-inf"                : KIT_TYPE_SPECOPS,
    "us_assault-inf"                : KIT_TYPE_ASSAULT,
    "us_at-inf"                     : KIT_TYPE_AT,
    "us_engineer-inf"               : KIT_TYPE_ENGINEER,
    "us_sniper-inf"                 : KIT_TYPE_SNIPER,
    "us_specops-inf"                : KIT_TYPE_SPECOPS,
    "us_support_saw"                : KIT_TYPE_SUPPORT,
    # Pickup Kits
    "engineer_tavor-inf"            : KIT_TYPE_ENGINEER,
    "sniper_m109-inf"               : KIT_TYPE_SNIPER,
    "specops_sg552-inf"             : KIT_TYPE_SPECOPS,
#Hard Justice
    "us2_at"                        : KIT_TYPE_AT,
    "us2_assault"                   : KIT_TYPE_ASSAULT,
    "us2_engineer"                  : KIT_TYPE_ENGINEER,
    "us2_medic"                     : KIT_TYPE_MEDIC,
    "us2_specops"                   : KIT_TYPE_SPECOPS,
    "us2_support"                   : KIT_TYPE_SUPPORT,
    "us2_sniper"                    : KIT_TYPE_SNIPER,
    "mec2_at"                       : KIT_TYPE_AT,
    "mec2_assault"                  : KIT_TYPE_ASSAULT,
    "mec2_engineer"                 : KIT_TYPE_ENGINEER,
    "mec2_medic"                    : KIT_TYPE_MEDIC,
    "mec2_specops"                  : KIT_TYPE_SPECOPS,
    "mec2_support"                  : KIT_TYPE_SUPPORT,
    "mec2_sniper"                   : KIT_TYPE_SNIPER,
    "ch2_at"                        : KIT_TYPE_AT,
    "ch2_assault"                   : KIT_TYPE_ASSAULT,
    "ch2_engineer"                  : KIT_TYPE_ENGINEER,
    "ch2_medic2"                    : KIT_TYPE_MEDIC,
    "ch2_specops"                   : KIT_TYPE_SPECOPS,
    "ch2_support"                   : KIT_TYPE_SUPPORT,
    "ch2_sniper"                    : KIT_TYPE_SNIPER,
    "us3_at"                        : KIT_TYPE_AT,
    "us3_assault"                   : KIT_TYPE_ASSAULT,
    "us3_engineer"                  : KIT_TYPE_ENGINEER,
    "us3_medic"                     : KIT_TYPE_MEDIC,
    "us3_specops"                   : KIT_TYPE_SPECOPS,
    "us3_support"                   : KIT_TYPE_SUPPORT,
    "us3_sniper"                    : KIT_TYPE_SNIPER,
    "mec3_at"                       : KIT_TYPE_AT,
    "mec3_assault"                  : KIT_TYPE_ASSAULT,
    "mec3_engineer"                 : KIT_TYPE_ENGINEER,
    "mec3_medic"                    : KIT_TYPE_MEDIC,
    "mec3_specops"                  : KIT_TYPE_SPECOPS,
    "mec3_support"                  : KIT_TYPE_SUPPORT,
    "mec3_sniper"                   : KIT_TYPE_SNIPER,
    "ch3_at"                        : KIT_TYPE_AT,
    "ch3_assault"                   : KIT_TYPE_ASSAULT,
    "ch3_engineer"                  : KIT_TYPE_ENGINEER,
    "ch3_medic"                     : KIT_TYPE_MEDIC,
    "ch3_specops"                   : KIT_TYPE_SPECOPS,
    "ch3_support"                   : KIT_TYPE_SUPPORT,
    "ch3_sniper"                    : KIT_TYPE_SNIPER,
    "ca_at"                         : KIT_TYPE_AT,
    "ca_assault"                    : KIT_TYPE_ASSAULT,
    "ca_engineer"                   : KIT_TYPE_ENGINEER,
    "ca_medic"                      : KIT_TYPE_MEDIC,
    "ca_specops"                    : KIT_TYPE_SPECOPS,
    "ca_support"                    : KIT_TYPE_SUPPORT,
    "ca_sniper"                     : KIT_TYPE_SNIPER,
# AIX 2.0 TNG Maps
    "ch_assault_v2"                 : KIT_TYPE_ASSAULT,
    "ch_at_v2"                      : KIT_TYPE_AT,
    "ch_engineer_v2"                : KIT_TYPE_ENGINEER,
    "ch_medic_v2"                   : KIT_TYPE_MEDIC,
    "ch_sniper_v2"                  : KIT_TYPE_SNIPER,
    "ch_specops_v2"                 : KIT_TYPE_SPECOPS,
    "ch_support_v2"                 : KIT_TYPE_SUPPORT,
    "mec_support_v2"                : KIT_TYPE_SUPPORT,
    "us_support_v2"                 : KIT_TYPE_SUPPORT,
# AIX 2.0 TNG 2.0
    "ch_assault_v2"                 : KIT_TYPE_ASSAULT,
    "ch_at_v2"                      : KIT_TYPE_AT,
    "ch_engineer_v2"                : KIT_TYPE_ENGINEER,
    "ch_medic_v2"                   : KIT_TYPE_MEDIC,
    "ch_medic_v3"                   : KIT_TYPE_MEDIC,
    "ch_sniper_v2"                  : KIT_TYPE_SNIPER,
    "ch_specops_v2"                 : KIT_TYPE_SPECOPS,
    "ch_support_v2"                 : KIT_TYPE_SUPPORT,
    "ch_support_v3"                 : KIT_TYPE_SUPPORT,
    "un_assault_v2"                 : KIT_TYPE_ASSAULT,
    "un_at_v2"                      : KIT_TYPE_AT,
    "un_engineer_v2"                : KIT_TYPE_ENGINEER,
    "un_medic_v2"                   : KIT_TYPE_MEDIC,
    "un_medic_v3"                   : KIT_TYPE_MEDIC,
    "un_specops_v2"                 : KIT_TYPE_SPECOPS,
    "un_support_v2"                 : KIT_TYPE_SUPPORT,
    "un_support_v3"                 : KIT_TYPE_SUPPORT,
    "un_sniper_v2"                  : KIT_TYPE_SNIPER,
    "mec_assault_v2"                : KIT_TYPE_ASSAULT,
    "mec_medic_v2"                  : KIT_TYPE_MEDIC,
    "mec_medic_v3"                  : KIT_TYPE_MEDIC,
    "mec_sniper_v2"                 : KIT_TYPE_SNIPER,
    "mec_specops_v2"                : KIT_TYPE_SPECOPS,
    "mec_support_v3"                : KIT_TYPE_SUPPORT,
    "us_assault_v2"                 : KIT_TYPE_ASSAULT,
    "us_at_v2"                      : KIT_TYPE_AT,
    "us_engineer_v2"                : KIT_TYPE_ENGINEER,
    "us_medic_v2"                   : KIT_TYPE_MEDIC,
    "us_sniper_v2"                  : KIT_TYPE_SNIPER,
    "us_specops_v2"                 : KIT_TYPE_SPECOPS
}

armyMap = {
# Battlefield2
    "us"             : ARMY_USA,
    "mec"            : ARMY_MEC,
    "ch"             : ARMY_CHINESE,
# xpack1 - SpecialForces
    "seal"           : ARMY_SEALS,
    "sas"            : ARMY_SAS,
    "spetz"          : ARMY_SPETZNAS,
    "mecsf"          : ARMY_MECSF,
    "chinsurgent"    : ARMY_REBELS,
    "meinsurgent"    : ARMY_INSURGENTS,
# booster pack 1 - Euroforces
    "eu"             : ARMY_EURO,
# POE2
    "ger"            : ARMY_GER,
    "ukr"            : ARMY_UKR,
# AIX
    "un"             : ARMY_UN,
# Hard Justice
    "us2"            : ARMY_USA,
    "us3"            : ARMY_USA,
    "mec2"           : ARMY_MEC,
    "mec3"           : ARMY_MEC,
    "ch2"            : ARMY_CHINESE,
    "ch3"            : ARMY_CHINESE,
    "ca"             : ARMY_CANADIAN
}


mapMap = {
# !! Doppelte Maps sind auskommentiert, bei bedarf aendern!!
# Battlefield2
    # middle eastern theater
    "kubra_dam"                     : "0",
    "mashtuur_city"                 : "1",
    "operation_clean_sweep"         : "2",
    "zatar_wetlands"                : "3",
    "strike_at_karkand"             : "4",
    "sharqi_peninsula"              : "5",
    "gulf_of_oman"                  : "6",
    "operationsmokescreen"          : "10",
    "taraba_quarry"                 : "11",
    "road_to_jalalabad"             : "12",
    # Asian Theater
    "daqing_oilfields"              : "100",
    "dalian_plant"                  : "101",
    "dragon_valley"                 : "102",
    "fushe_pass"                    : "103",
    "hingan_hills"                  : "104",
    "songhua_stalemate"             : "105",
    "greatwall"                     : "110",
    # US Theatre
    "midnight_sun"                  : "200",
    "operationroadrage"             : "201",
    "operationharvest"              : "202",
    # xpack1 - SpecialForces
    "devils_perch"                  : "300",
    "iron_gator"                    : "301",
    "night_flight"                  : "302",
    "warlord"                       : "303",
    "leviathan"                     : "304",
    "mass_destruction"              : "305",
    "surge"                         : "306",
    "ghost_town"                    : "307",
    # Special maps
    "wake_island_2007"              : "601",
    "highway_tampa"                 : "602",
# POE2 
    "battle_of_sambir"              : "1001",
    "carpathian_mountains"          : "1002",
    "dnipro_sunrise"                : "1003",
    "dnister_river_valley"          : "1004",
    "fallen"                        : "1005",
    "first_snow"                    : "1006",
    "guardian"                      : "1007",
    "highway_to_hell"               : "1008",
    "lutsk"                         : "1009",
    "orel"                          : "1010",
    "rivne"                         : "1011",
    "rolling_thunder"               : "1012",
    "zhytomyr"                      : "1013",
    "spies_like_us"                 : "1014",
# AIX 1.0
    "aix_archipelago"               : "3000",
    "aix_damocles"                  : "3001",
    "daqing_dawn"                   : "3002",
    "dragon_valley_moon"            : "3003",
    "falklands"                     : "3004",
    #"gulf_of_oman"                 : "3005", #In Battlefield 2 vorhanden
    "karkand_stormfront"            : "3006",
    "processing_plant"              : "3007",
    "aix_refinery"                  : "3008",
    "aix_runningman"                : "3009",
    #"sharqi_peninsula"             : "3010", #In Battlefield 2 vorhanden
    "the_push_day"                  : "3011",
    "urban_jungle"                  : "3012",
    "wake_twilight"                 : "3013",
    "zatar_wetlands_ii"             : "3014",
    "zzz_easter_island"             : "3015",
# AIX 1.0 ITTH MapPack 1
    "end_of_the_line"               : "3020",
    "kursk"                         : "3021",
    "marauders_at_midnight"         : "3022",
    "midway"                        : "3023",
    "snowy_park"                    : "3024",
    "snowy_park_day"                : "3025",
    "snowy_park_summer"             : "3026",
    "solomon_showdown"              : "3027",
# AIX 1.0 ITTH MapPack 2
    "battle_of_kirkuk_oilfields"    : "3030",
    "husky"                         : "3031",
    "invasion_of_the_philippines"   : "3032",
    "iron_thunder"                  : "3033",
    "operation_fox"                 : "3034",
# AIX 1.0 ITTH MapPack 3
    "eagles_nest"                   : "3040",
    "iwo_jima"                      : "3041",
    "manamoc_island"                : "3042",
    "rebellion"                     : "3043",
    "red_dawn"                      : "3044",
    "tobruk"                        : "3045",
# AIX 2.0
    #"aix_archipelago"              : "3100", #In AIX 1.0 vorhanden
    #"aix_damocles"                 : "3101", #In AIX 1.0 vorhanden
    "aix_greasy_mullet"             : "3102",
    "aix_hammer_down"               : "3103",
    "aix_operation_static"          : "3104",
    #"aix_refinery"                 : "3105", #In AIX 1.0 vorhanden
    #"aix_runningman"               : "3106", #In AIX 1.0 vorhanden
    "aix_trident"                   : "3107",
    "aix_wake_island_2007"          : "3108",
    "city_district"                 : "3109",
    #"dalian_plant"                 : "3110", #In Battlefield 2 vorhanden
    #"daqing_dawn"                  : "3111", #In AIX 1.0 vorhanden
    #"dragon_valley"                : "3112", #In Battlefield 2 vorhanden
    #"dragon_valley_moon"           : "3113", #In AIX 1.0 vorhanden
    #"falklands"                    : "3114", #In AIX 1.0 vorhanden
    #"gulf_of_oman"                 : "3115", #In AIX 1.0 vorhanden
    #"karkand_stormfront"           : "3116", #In AIX 1.0 vorhanden
    #"processing_plant"             : "3117", #In AIX 1.0 vorhanden
    #"sharqi_peninsula"             : "3118", #In AIX 1.0 vorhanden
    #"the_push_day"                 : "3119", #In AIX 1.0 vorhanden
    #"urban_jungle"                 : "3120", #In AIX 1.0 vorhanden
    #"wake_twilight"                : "3121", #In AIX 1.0 vorhanden
    #"zatar_wetlands_ii"            : "3122", #In AIX 1.0 vorhanden
    #"zzz_easter_island"            : "3123", #In AIX 1.0 vorhanden
# AIX 2.0 ITTH MapPack
    "aberdeen"                      : "3150",
    "bataan"                        : "3151",
    #"battle_of_kirkuk_oilfields"   : "3152", # In AIX 1.0 ITTH MapPack 2 vorhanden
    "battleaxe"                     : "3153",
    "bizerte"                       : "3154",
    "city_park"                     : "3155",
    "city_park_night"               : "3156",
    #"eagles_nest"                  : "3157", # In AIX 1.0 ITTH MapPack 3 vorhanden
    #"end_of_the_line"              : "3158", # In AIX 1.0 ITTH MapPack 1 vorhanden
    "guadalcanal"                   : "3159",
    #"husky"                        : "3160", # In AIX 1.0 ITTH MapPack 2 vorhanden
    "invasion_of_the_coral_sea"     : "3161",
    #"invasion_of_the_philippines"  : "3162", # In AIX 1.0 ITTH MapPack 2 vorhanden
    #"iron_thunder"                 : "3163", # In AIX 1.0 ITTH MapPack 2 vorhanden
    #"iwo_jima"                     : "3164", # In AIX 1.0 ITTH MapPack 3 vorhanden
    "kasserine_pass_2008"           : "3165",
    #"kursk"                        : "3166", # In AIX 1.0 ITTH MapPack 1 vorhanden
    #"manamoc_island"               : "3167", # In AIX 1.0 ITTH MapPack 3 vorhanden
    #"marauders_at_midnight"        : "3168", # In AIX 1.0 ITTH MapPack 1 vorhanden
    #"midway"                       : "3169", # In AIX 1.0 ITTH MapPack 1 vorhanden
    #"operation_fox"                : "3170", # In AIX 1.0 ITTH MapPack 2 vorhanden
    "raid_on_agheila"               : "3171",
    #"rebellion"                    : "3172", # In AIX 1.0 ITTH MapPack 3 vorhanden
    #"red_dawn"                     : "3173", # In AIX 1.0 ITTH MapPack 3 vorhanden
    #"snowy_park"                   : "3174", # In AIX 1.0 ITTH MapPack 1 vorhanden
    #"snowy_park_day"               : "3175", # In AIX 1.0 ITTH MapPack 1 vorhanden
    #"snowy_park_summer"            : "3176", # In AIX 1.0 ITTH MapPack 1 vorhanden
    #"solomon_showdown"             : "3177", # In AIX 1.0 ITTH MapPack 1 vorhanden
    #"tobruk"                       : "3178", # In AIX 1.0 ITTH MapPack 3 vorhanden
    "urban_decay"                   : "3179",
# AIX 2 TNG
    "tng_archipelago"               : "3300",
    "tng_clean_sweep_ii"            : "3301",
    "tng_dalian_plant"              : "3302",
    "tng_daqing_dawn"               : "3303",
    "tng_dragon_valley_moon"        : "3304",
    "tng_frostbite"                 : "3305",
    "tng_fushe_pass"                : "3306",
    "tng_gazala_v2"                 : "3307",
    "tng_archipelago_moon"          : "3308",
    "tng_gulf_of_oman"              : "3309",
    "tng_highway_tampa"             : "3310",
    "tng_iwo_jima"                  : "3311",
    "tng_kirkuk_basin"              : "3312",
    "tng_kubra_dam"                 : "3313",
    "tng_the_dam_flood"             : "3314",
    "tng_the_push_day"              : "3315",
    "tng_town_strike"               : "3316",
    "tng_trident"                   : "3317",
    "tng_trident_moon"              : "3318",
    "tng_wake_island"               : "3319",
    "tng_zatar_wetlands_ii"         : "3320",
# AIX 2 TNG 2.0
    "tng_airport"                   : "3350",
    #"tng_archipelago"              : "3351", # In AIX 2 TNG schon vorhanden
    #"tng_archipelago_moon"         : "3352", # In AIX 2 TNG schon vorhanden
    #"tng_clean_sweep_ii"           : "3353", # In AIX 2 TNG schon vorhanden
    "tng_course_of_the_river"       : "3354",
    #"tng_dalian_plant"             : "3355", # In AIX 2 TNG schon vorhanden
    #"tng_daqing_dawn"              : "3356", # In AIX 2 TNG schon vorhanden
    "tng_dragon_valley"             : "3357",
    #"tng_dragon_valley_moon"       : "3358", # In AIX 2 TNG schon vorhanden
    #"tng_frostbite"                : "3359", # In AIX 2 TNG schon vorhanden
    #"tng_fushe_pass"               : "3360", # In AIX 2 TNG schon vorhanden
    #"tng_gazala_v2"                : "3361", # In AIX 2 TNG schon vorhanden
    #"tng_gulf_of_oman"             : "3362", # In AIX 2 TNG schon vorhanden
    #"tng_highway_tampa"            : "3363", # In AIX 2 TNG schon vorhanden
    "tng_kandahar_river_valley"     : "3364",
    #"tng_kirkuk_basin"             : "3365", # In AIX 2 TNG schon vorhanden
    #"tng_kubra_dam"                : "3366", # In AIX 2 TNG schon vorhanden
    "tng_oasis_revisited"           : "3367",
    "tng_op_yellow_dragon"          : "3368",
    "tng_road_to_jalalabad"         : "3369",
    "tng_sands_of_sinai"            : "3370",
    "tng_street"                    : "3371",
    #"tng_the_dam_flood"            : "3372", # In AIX 2 TNG schon vorhanden
    #"tng_the_push_day"             : "3373", # In AIX 2 TNG schon vorhanden
    "tng_the_sniper_day"            : "3374",
    #"tng_town_strike"              : "3375", # In AIX 2 TNG schon vorhanden
    #"tng_trident"                  : "3376", # In AIX 2 TNG schon vorhanden
    #"tng_trident_moon"             : "3377", # In AIX 2 TNG schon vorhanden
    #"tng_wake_island"              : "3378", # In AIX 2 TNG schon vorhanden
    "tng_wake_twilight"             : "3379",
    #"tng_zatar_wetlands_ii"        : "3380", # In AIX 2 TNG schon vorhanden
# Hard Justice
    "basrah"                        : "4000",
    #"dalian_plant"                 : "4001", #In Battlefield 2 vorhanden
    #"daqing_oilfields"             : "4002", #In Battlefield 2 vorhanden
    "desert_shield_advanced"        : "4003",
    #"dragon_valley"                : "4004", #In Battlefield 2 vorhanden
    #"fuShe_pass"                   : "4005", #In Battlefield 2 vorhanden
    #"gulf_of_oman"                 : "4006", #In Battlefield 2 vorhanden
    #"highway_tampa"                : "4007", #In Battlefield 2 vorhanden
    "island_city"                   : "4008",
    "juno_beach"                    : "4009",
    "kandaharpatrol"                : "4010",
    #"kubra_dam"                    : "4011", #In Battlefield 2 vorhanden
    "lost_island"                   : "4012",
    #"mashtuur_city"                : "4013", #In Battlefield 2 vorhanden
    "omaha_beach_2008"              : "4014",
    #"operation_clean_sweep"        : "4015", #In Battlefield 2 vorhanden
    "remagen_bridge"                : "4016",
    #"road_to_jalalabad"            : "4017", #In Battlefield 2 vorhanden
    "road_to_karkand"               : "4018",
    #"sharqi_peninsula"             : "4019", #In Battlefield 2 vorhanden
    #"songhua_stalemate"            : "4020", #In Battlefield 2 vorhanden
    #"strike_at_karkand"            : "4021", #In Battlefield 2 vorhanden
    "the_harbor"                    : "4022",
    "the_middle_ground"             : "4023",
    #"wake_island_2007"             : "4024", #In Battlefield 2 vorhanden
    "weapon_bunker"                 : "4025",
    #"zatar_wetlands"               : "4026", #In Battlefield 2 vorhanden
# Hard Justice Mappack 1
    # "battle_of_kirkuk_oilfields"  : "4030", In # AIX 1.0 ITTH MapPack 2 vorhanden
    "bl_bridge2b"                   : "4031",
    "course_of_the_river"           : "4032",
    "cult_site"                     : "4033",
    "desert_storm"                  : "4034",
    "divided_city"                  : "4035",
    "el_alamein_day1"               : "4036",
    "gulf_of_aqaba_bfsp"            : "4037",
    "jammed"                        : "4038",
    "kyzyl_kum"                     : "4039",
    "requiem"                       : "4040",
# Hard Justice Mappack 2
    "insurgency_on_alcatraz_island" : "4050",
    "nantari_crossing"              : "4051",
    "operation_amos"                : "4052",
    "operation_compton"             : "4053",
    "operation_frog"                : "4054",
    "operation_nightshift"          : "4055",
    "operation_power_failure_bfsp"  : "4056",
    "street"                        : "4057",
    #"tobruk"                       : "4058", # In AIX 1.0 ITTH MapPack 3 vorhanden
    "vulcan_island"                 : "4059",
    "zhanjiang_security_area"       : "4060",
# Hard Justice Mappack 3
    "heli_attack"                   : "4070",
    "imprisoned"                    : "4071",
    "jibbel_city"                   : "4072",
    "last_stand"                    : "4073",
    #"red_dawn"                     : "4074", # In AIX 1.0 ITTH MapPack 3 vorhanden
    "sands_of_sinai_v1_1"           : "4075",
    "snow_soldier"                  : "4076",
    "steel_thunder"                 : "4077",
    "volgograd_2010"                : "4078",
# Custom added (add your custom maps here)
	"operation_hydra"				: "5000"
}
UNKNOWN_MAP = 99

gameModeMap = {
    "gpm_cq"    : 0,
    "gpm_sl"    : 1,
    "gpm_coop"  : 2,
}
UNKNOWN_GAMEMODE = 99



def getVehicleType(templateName):
    return vehicleTypeMap.get(templateName.lower(), VEHICLE_TYPE_UNKNOWN)


    
def getWeaponType(templateName):
    return weaponTypeMap.get(templateName.lower(), WEAPON_TYPE_UNKNOWN)
    
    
    
def getKitType(templateName):    
    return kitTypeMap.get(templateName.lower(), KIT_TYPE_UNKNOWN)
    
    
    
def getArmy(templateName):
    return armyMap.get(templateName.lower(), ARMY_UNKNOWN)



def getMapId(mapName):
    return mapMap.get(mapName.lower(), UNKNOWN_MAP)



def getGameModeId(gameMode):
    return gameModeMap.get(gameMode.lower(), UNKNOWN_GAMEMODE)



def getRootParent(obj):
    parent = obj.getParent()
    
    if parent == None:
        return obj
        
    return getRootParent(parent)



if g_debug: print "Stat constants loaded"