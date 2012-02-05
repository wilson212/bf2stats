<?php
/***************************************
*  Battlefield 2 Private Stats Config  *
****************************************
* All comments have been removed from  *
* this file. Please use the Web Admin  *
* to change values.                    *
***************************************/
$db_expected_ver = '1.4.5';
$db_host = 'localhost';
$db_port = 3306;
$db_name = 'bf2stats';
$db_user = 'bf2statslogger';
$db_pass = 'bf2';
$admin_user = 'Admin';
$admin_pass = 'Password';
$admin_hosts = array('127.0.0.1','192.168.2.0/24','localhost','192.168.1.102');
$admin_log = 'logs/_admin_event.log';
$admin_backup_path = 'c:/xampp/htdocs/ASP/_backups/';
$admin_backup_ext = '.bak';
$admin_page_size = 75;
$admin_ignore_ai = 0;
$stats_ext = '.txt';
$stats_logs = 'logs/';
$stats_logs_store = 'logs/_processed/';
$stats_move_logs = 1;
$stats_min_game_time = 0;
$stats_min_player_game_time = 0;
$stats_players_min = 1;
$stats_players_max = 256;
$stats_rank_check = 0;
$stats_rank_tenure = 7;
$stats_awds_complete = 0;
$stats_lan_override = '255.255.255.255';
$stats_local_pids = array('LocalPlayer01','210.84.29.151','LocalPlayer02','210.84.29.151');
$debug_lvl = 1;
$debug_log = 'logs/_stats_errors.log';
$game_hosts = array('127.0.0.1','192.168.2.0/24','192.168.1.102','localhost','::1');
$game_custom_mapid = 700;
$game_unlocks = 0;
$game_unlocks_bonus = 2;
$game_unlocks_bonus_min = 1;
$game_awds_ignore_time = 0;
$game_default_pid = 29000000;
?>