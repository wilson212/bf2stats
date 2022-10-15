<?php
class Testconfig
{
    public function Init() 
    {
		// Make sure the database if offline
		if(DB_VER == '0.0.0')
			redirect('home');
			
        // Check for post data
        if($_POST['action'] == 'runtests')
        {
            $this->ProcessTest();
        }
        else
        {
            // Setup the template
            $Template = new Template();
            $Template->render('testconfig');
        }
    }
    
    public function ProcessTest()
    {
        // Remove our time limit!
        ini_set('max_execution_time', 0);

        // Load player class
        $Player = new Player();
        
        // Define our pass/fail messages for less typing
        define('__PASS','<b><font color="green">Pass</font></b><br />');
        define('__WARN','<b><font color="orange">Warn</font></b><br />');
        define('__FAIL','<b><font color="red">Fail</font></b><br />');
        
        // Define Test Snapshot String (PID: 111)
        $tst_prefix = 'TST'.uniqid(rand());
        $tst_snapshot = $tst_prefix.'\test_server\queryport\29900\mapname\test_map\mapid\999\mapstart\1157264950.7\mapend\1157266995.57\win\1\gm\0'.
            '\v\bf2\pc\1\rwa\2\ra1\0\rs1\25\ra2\2\rs2\0\pID_0\999\name_0\Test Player\t_0\2\a_0\0\ctime_0\1559\c_0\1\ip_0\0\ai_0\0\rs_0\24\cs_0\0\ss_0\18'.
            '\ts_0\6\kills_0\9\deaths_0\17\cpc_0\0\cpn_0\1\cpa_0\0\cpna_0\0\cpd_0\0\ka_0\0\he_0\0\rev_0\0\rsp_0\0\rep_0\0\tre_0\0\drs_0\0\dra_0\4'.
            '\pa_0\0\tmkl_0\0\tmdg_0\0\tmvd_0\0\su_0\0\ks_0\6\ds_0\6\rank_0\3\ban_0\0\kck_0\0\tco_0\0\tsl_0\1559\tsm_0\0\tlw_0\0\ta0_0\1559\ta1_0\0'.
            '\ta2_0\0\ta3_0\0\ta4_0\0\ta5_0\0\ta6_0\0\ta7_0\0\ta8_0\0\ta9_0\0\ta10_0\0\ta11_0\0\ta12_0\0\ta13_0\0\mvns_0\29000037\mvks_0\1\mvns_0\29000113\mvks_0\1\mvns_0\29000069\mvks_0'.
            '\1\mvns_0\29000081\mvks_0\2\mvns_0\29000108\mvks_0\1\mvns_0\29000080\mvks_0\1\mvns_0\29000089\mvks_0\1\mvns_0\29000041\mvks_0\1\tv0_0\278'.
            '\tv1_0\0\tv2_0\0\tv3_0\532\tv4_0\227\tv5_0\0\tv6_0\0\tvp_0\17\kv0_0\5\kv1_0\0\kv2_0\0\kv3_0\0\kv4_0\0\kv5_0\0\kv6_0\0\bv0_0\3\bv1_0\0'.
            '\bv2_0\0\bv3_0\0\bv4_0\0\bv5_0\0\bv6_0\0\kvr0_0\1\kvr1_0\0\kvr2_0\0\kvr3_0\0\kvr4_0\0\kvr5_0\0\kvr6_0\0\tk0_0\736\tk1_0\20\tk2_0\311\tk3_0'.
            '\0\tk4_0\320\tk5_0\84\tk6_0\29\kk0_0\8\kk1_0\0\kk2_0\0\kk3_0\0\kk4_0\1\kk5_0\0\kk6_0\0\dk0_0\10\dk1_0\1\dk2_0\2\dk3_0\0\dk4_0\2\dk5_0\1\dk6_0\1'.
            '\tw0_0\11\tw1_0\0\tw2_0\49\tw3_0\28\tw4_0\10\tw5_0\5\tw6_0\54\tw7_0\382\tw8_0\47\te0_0\2\te1_0\0\te3_0\48\te2_0\0\te4_0\0\te5_0\0\te6_0\0\te7_0'.
            '\0\te8_0\0\kw0_0\0\kw1_0\0\kw2_0\0\kw3_0\0\kw4_0\0\kw5_0\1\kw6_0\0\kw7_0\1\kw8_0\0\ke0_0\1\ke1_0\0\ke3_0\0\ke2_0\0\ke4_0\0\ke5_0\0\bw0_0\1\bw1_0'.
            '\0\bw2_0\2\bw3_0\1\bw4_0\0\bw5_0\0\bw6_0\3\bw7_0\5\bw8_0\1\be0_0\0\be1_0\0\be3_0\1\be2_0\0\be4_0\0\be5_0\0\be8_0\0\be9_0\0\de6_0\0\de7_0\0\de8_0'.
            '\0\sw0_0\0\sw1_0\0\sw2_0\26\sw3_0\0\sw4_0\0\sw5_0\15\sw6_0\4\sw7_0\53\sw8_0\0\se0_0\2\se1_0\0\se2_0\0\se3_0\6\se4_0\0\se5_0\0\hw0_0\0\hw1_0\0'.
            '\hw2_0\3\hw3_0\0\hw4_0\0\hw5_0\5\hw6_0\1\hw7_0\8\hw8_0\0\he0_0\1\he1_0\0\he2_0\0\he3_0\3\he4_0\0\he5_0\0\EOF\1';
        $tst_pid = 999;
        $tst_mapid = 999;
        $errors = false;
        $warns = false;
        $out = '<p>';
        
        // Check Config File Write Access
        $out .= " > Checking Config File...<br />";
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Config File Writable (system/config/config.php): ";
        if (!FileSystem::IsWritable( SYSTEM_PATH . DS .'config'. DS .'config.php' )) 
        {
            $out .= __FAIL;
            $errors = true;
        } 
        else 
        {
            $out .= __PASS;
        }
        
        // Check Database Access
        $out .= " > Checking Database Config...<br />";
        $DB = Database::GetConnection();
        if( $DB instanceof PDO )
        {
            $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Database Connection (".Config::Get('db_host')."): ".__PASS;
            
            // Check Database Version
            if (DB_VER != Config::Get('db_expected_ver')) 
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Database version (".Config::Get('db_expected_ver')."): ".__FAIL;
            else 
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Database version (".Config::Get('db_expected_ver')."): ".__PASS;
        }
        else
        {
            $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Database Connection (".Config::Get('db_host')."): ".__FAIL;
            $errors = true;
        }
        
        // Check Log File Write Access
        $out .= " > Checking Log Files...<br />";
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Stats Debug Log File Writable (system/logs/stats_debug.log): ";
        $log = SYSTEM_PATH . DS . 'logs' . DS . 'stats_debug.log';
        if (!FileSystem::IsWritable( $log ))
        {
            $out .= __WARN;
            $warns = true;
        } 
        else 
        {
            $out .= __PASS;
        }
        
        // Check Admin Log
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Admin Log File Writable (system/logs/admin_event.log): ";
        $log = SYSTEM_PATH . DS .'logs'. DS .'admin_event.log';
        if (!FileSystem::IsWritable( $log ))
        {
            $out .= __WARN;
            $warns = true;
        } 
        else 
        {
            $out .= __PASS;
        }
        
        // Check merge players log
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Merge Players Log File Writable (system/logs/merge_players.log): ";
        $log = SYSTEM_PATH . DS .'logs'. DS .'merge_players.log';
        if (!FileSystem::IsWritable( $log ))
        {
            $out .= __WARN;
            $warns = true;
        } 
        else 
        {
            $out .= __PASS;
        }
        
        // Check validate awards log
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Validate Awards Log File Writable (system/logs/validate_awards.log): ";
        $log = SYSTEM_PATH . DS .'logs'. DS .'validate_awards.log';
        if (!FileSystem::IsWritable( $log ))
        {
            $out .= __WARN;
            $warns = true;
        } 
        else 
        {
            $out .= __PASS;
        }
        
        // Check validate ranks log
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Validate Ranks Log File Writable (system/logs/validate_ranks.log): ";
        $log = SYSTEM_PATH . DS .'logs'. DS .'validate_ranks.log';
        if (!FileSystem::IsWritable( $log ))
        {
            $out .= __WARN;
            $warns = true;
        } 
        else 
        {
            $out .= __PASS;
        }
        
        
        // SNAPSHOTS
        $out .= " > Checking SNAPSHOT Storage Path...<br />";
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- SNAPSHOT Temporary Path Writable (system/snapshots/temp): ";
        $path = SYSTEM_PATH . DS .'snapshots'. DS .'temp'. DS;
        if (!FileSystem::IsWritable( $path ))
        {
            $out .= __FAIL;
            $errors = true;
        } 
        else 
        {
            $out .= __PASS;
        }
        
        // Snapshot Archive Path
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- SNAPSHOT Processed Path Writable (system/snapshots/processed): ";
        $path = SYSTEM_PATH . DS .'snapshots'. DS .'processed'. DS;
        if (!FileSystem::IsWritable( $path ))
        {
            $out .= __FAIL;
            $errors = true;
        } 
        else 
        {
            $out .= __PASS;
        }

        // Check Admin Backup Write Access
        $out .= " > Checking Database Backup Storage Path...<br />";
        $path = str_replace(array('/', '\\'), DS, trim(Config::Get('admin_backup_path')));
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Backup Path Writable ({$path}): ";
        if (!FileSystem::IsWritable( $path ))
        {
            $out .= __FAIL;
            $errors = true;
        } 
        else 
        {
            $out .= __PASS;
        }
        
        // Check For Required Functions
        $out .= " > Checking Remote URL Functions...<br />";
        if( function_exists('file') && function_exists('fopen') && ini_get('allow_url_fopen') ) 
        {
            $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Remote URL Function Exist ('FOPEN'): ".__PASS;
            $doURLChecks = true;
        } 
        elseif( (function_exists('curl_exec')) ) 
        {
            $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Remote URL Function Exist ('CURL'): ".__PASS;
            $doURLChecks = true;
        } 
        else 
        {
            $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Remote URL Function Exist: ".__WARN;
            $warns = true;
            $doURLChecks = false;
        }
        
        // ASPX File Checks
        if ($doURLChecks) 
        {
            // Check bf2statistics.php Processing
            $out .= " > Checking BF2Statistics Processing...<br />";

            // Post the headers and snapshot data
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://{$_SERVER['HTTP_HOST']}/ASP/bf2statistics.php");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $tst_snapshot);
            curl_setopt($ch, CURLOPT_USERAGENT, "GameSpyHTTP/1.0");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $result = curl_exec($ch);
            $curlInfo = curl_getinfo($ch);
            curl_close($ch);

            if ($result && $curlInfo['http_code'] == 200) 
            {
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- BF2Statistics Processing Check: ".__PASS;
            } 
            else 
            {
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- BF2Statistics Processing Check: ".__FAIL;
                $errors = true;
            }
            
            // Check .aspx Page Responses
            $out .= " > Checking Gamespy (.aspx) File Basic Response...<br />";
            $url = "http://".$_SERVER['HTTP_HOST']."/ASP/getbackendinfo.aspx";
            $response = getPageContents($url);
            if ($response === false || trim($response[0]) != 'O') 
            {
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Gamespy (.aspx) Basic Response: ".__FAIL;
                $errors = true;
            } 
            else 
            {
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Gamespy (.aspx) Basic Response: ".__PASS;
            }
            
            // Advanced request (1)
            $out .= " > Checking Gamespy (.aspx) File Advanced Responses...<br />";
            $url = "http://".$_SERVER['HTTP_HOST']."/ASP/getawardsinfo.aspx?pid=". $tst_pid;
            $response = getPageContents($url);
            if ($response === false || trim($response[0]) != 'O') 
            {
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Gamespy (.aspx) Advanced (1) Response: ".__FAIL;
                $errors = true;
            } 
            else 
            {
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Gamespy (.aspx) Advanced (1) Response: ".__PASS;
            }
            
            // Advanced Request (2)
            $url = "http://".$_SERVER['HTTP_HOST']."/ASP/getrankinfo.aspx?pid=". $tst_pid;
            $response = getPageContents($url);
            if ($response === false || trim($response[0]) != 'O') 
            {
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Gamespy (.aspx) Advanced (2) Response: ".__FAIL;
                $errors = true;
            } 
            else 
            {
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Gamespy (.aspx) Advanced (2) Response: ".__PASS;
            }
            
            // Advanced Request (3)
            $url = "http://".$_SERVER['HTTP_HOST']."/ASP/getunlocksinfo.aspx?pid=". $tst_pid;
            $response = getPageContents($url);
            if ($response === false || trim($response[0]) != 'O') 
            {
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Gamespy (.aspx) Advanced (3) Response: ".__FAIL;
                $errors = true;
            } 
            else 
            {
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Gamespy (.aspx) Advanced (3) Response: ".__PASS;
            }
        }
        
        // Remove Test Player
        if( !$Player->deletePlayer($tst_pid) )
        {
            $out .= " -> Remove Test Player Data: ". __WARN;
            $warns = true;
        }
        else
        {
            $out .= " -> Remove Test Player Data: ". __PASS;
        }
        
        // Remove Test Server Data
        $result = $DB->exec("DELETE FROM `servers` WHERE `prefix` = '{$tst_prefix}';");
        if($result === false)
        {
            $out .= " -> Server Info ({$tst_prefix}) removed from Table (servers): ". __WARN;
            $warn = true;
        }
        else
        {
            $out .= " -> Server Info ({$tst_prefix}) removed from Table (servers): ". __PASS;
        }
        
        // Remove Test Map Data
        $result = $DB->exec("DELETE FROM `mapinfo` WHERE `id` = {$tst_mapid};");
        if($result === false)
        {
            $out .= " -> Map Info ({$tst_mapid}) removed from Table (mapinfo): ". __WARN;
            $warns = true;
        }
        else
        {
            $out .= " -> Map Info ({$tst_mapid}) removed from Table (mapinfo): ". __PASS;
        }
        
		// Remove round history
        $result = $DB->exec("DELETE FROM `round_history` WHERE `mapid` = {$tst_mapid};");
        if($result === false)
        {
            $out .= " -> Map Info ({$tst_mapid}) removed from Table (round_history): ". __WARN;
            $warns = true;
        }
        else
        {
            $out .= " -> Map Info ({$tst_mapid}) removed from Table (round_history): ". __PASS;
        }
        
        // Finish :)
        $out .= '</p>';
        
        // Determine if our save is a success
        echo json_encode( 
            array(
                'success' => ($errors == false),
                'warnings' => $warns,
                'html' => $out
            )
        );
    }
}
?>
