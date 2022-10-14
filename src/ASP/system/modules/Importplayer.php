<?php
class Importplayer
{
	public function Init()
	{
		// Make sure the database if offline
		if(DB_VER == '0.0.0')
			redirect('home');
			
		// Check for post data
		if($_POST['action'] == 'import')
		{
			$this->Import( intval(trim($_POST['pid'])) );
		}
		else
		{
			// Setup the template
			$Template = new Template();
			$Template->render('importplayer');
		}
	}
	
	public function Import($pid)
	{
		// Load Stats Parser and Database class
		$DB = Database::GetConnection();
		$Parser = new Statsparser();
		
		// BF2Web URL
		$gsURL = "http://bf2web.gamespy.com";

		// Get Player Info: Base (getplayerinfo.aspx query)
		$playerinfoURL1 = $gsURL."/ASP/getplayerinfo.aspx?pid={$pid}&info=per*,cmb*,twsc,cpcp,cacp,dfcp,kila,heal,rviv,rsup,rpar,tgte,dkas,dsab,cdsc,rank,cmsc,kick,kill,deth,suic,ospm,klpm,klpr,dtpr,bksk,wdsk,bbrs,tcdr,ban,dtpm,lbtl,osaa,vrk,tsql,tsqm,tlwf,mvks,vmks,mvn*,vmr*,fkit,fmap,fveh,fwea,wtm-,wkl-,wdt-,wac-,wkd-,vtm-,vkl-,vdt-,vkd-,vkr-,atm-,awn-,alo-,abr-,ktm-,kkl-,kdt-,kkd-";
		$playerinfoBase = getPageContents($playerinfoURL1);
		if(!($playerinfoBase[0] == "O" && $playerinfoBase[1] == "H".chr(9)."asof")) 
		{
			echo json_encode( array('success' => false, 'message' => "Player with PID {$pid} doesn't exist on the EA stat server !") );
			die();
		}
		$playerinfoData1 = $Parser->parsePlayerInfo($playerinfoBase);
		
		// Get Player Info: Extra (getplayerinfo.aspx query)
		$playerinfoURL2 = $gsURL."/ASP/getplayerinfo.aspx?pid={$pid}&info=mtm-,mwn-,mls-"; 
		$playerinfoExtra = getPageContents($playerinfoURL2);
		$playerinfoData2 = $Parser->parsePlayerInfo($playerinfoExtra);
		
		// Get Player Info: Unlocks (getunlocksinfo.aspx query)
		// $unlocksinfoURL = $gsURL."/ASP/getunlocksinfo.aspx?pid={$pid}"; 
		// $playerinfoUnlocks = getPageContents($unlocksinfoURL);
		// $playerinfoData3 = $Parser->parseUnlocks($playerinfoUnlocks);
		
		// Get Player Info: Awards (getawardsinfo.aspx query)
		$awardsinfoURL = $gsURL."/ASP/getawardsinfo.aspx?pid={$pid}"; 
		$playerinfoAwards = getPageContents($awardsinfoURL);
		$playerinfoData4 = $Parser->parseAwards($playerinfoAwards);
		
		// Check if Player Already Exists	
		$result = $DB->query("SELECT * FROM player WHERE id = {$pid}");	
		if($result instanceof PDOStatement && $result->rowCount() > 0) 
		{
			echo json_encode( array('success' => false, 'message' => "Player with PID {$pid} already exist on the stat server !") );
			die();
		}
		else
		{            	                      
			// Insert information. Add a space infront the name to signify an online account!
			$query = "INSERT INTO player SET
				id = {$pid},
				name = ' " . trim($playerinfoData1["nick"]) . "',
				country = 'xx',
				time = " . $playerinfoData1["time"] . ",
				rounds = " . $playerinfoData1["mode0"] . ",
				ip = '0.0.0.0',
				score = " . $playerinfoData1["scor"] . ",
				cmdscore = " . $playerinfoData1["cdsc"] . ",
				skillscore = " . $playerinfoData1["cmsc"] . ",
				teamscore = " . $playerinfoData1["twsc"] . ",
				kills = " . $playerinfoData1["kill"] . ",
				deaths = " . $playerinfoData1["deth"] . ",
				captures = " . $playerinfoData1["cpcp"] . ",
				captureassists = " . $playerinfoData1["cacp"] . ",
				defends = " . $playerinfoData1["dfcp"] . ",
				damageassists = " . $playerinfoData1["kila"] . ",
				heals = " . $playerinfoData1["heal"] . ",
				revives = " . $playerinfoData1["rviv"] . ",
				ammos = " . $playerinfoData1["rsup"] . ",
				repairs = " . $playerinfoData1["rpar"] . ",
				targetassists = 0,
				driverspecials = " . $playerinfoData1["dsab"] . ",
				teamkills = 0,
				teamdamage = 0,
				teamvehicledamage = 0,
				suicides = " . $playerinfoData1["suic"] . ",
				killstreak = " . $playerinfoData1["bksk"] . ",
				deathstreak = " . $playerinfoData1["wdsk"] . ",
				rank = " . $playerinfoData1["rank"] . ",
				banned = " . $playerinfoData1["ban"] . ", 
				kicked = " . $playerinfoData1["kick"] . ",
				cmdtime = " . $playerinfoData1["tcdr"] . ",
				sqltime = " . $playerinfoData1["tsql"] . ",
				sqmtime = " . $playerinfoData1["tsqm"] . ",
				lwtime = " . $playerinfoData1["tlwf"] . ",
				wins = " . $playerinfoData1["wins"] . ",
				losses = " . $playerinfoData1["loss"] . ",
				joined = " . $playerinfoData1["jond"] . ",
				rndscore = " . $playerinfoData1["bbrs"] . ",
				lastonline = " . $playerinfoData1["lbtl"] . ",
				mode0 = " . $playerinfoData1["mode0"] . ",
				mode1 = " . $playerinfoData1["mode1"] . ",
				mode2 = " . $playerinfoData1["mode2"] . ",
				clantag = ''
			";
			$result = $DB->exec($query);
			if(!$result) 
			{
				echo json_encode( array('success' => false, 'message' => "Failed to install player data into the 'players' table!") );
				die();
			}
		}
		
		// Army			
		$result = $DB->query("SELECT * FROM army WHERE id = {$pid}");
		if($result instanceof PDOStatement && !$result->rowCount())
		{
		
			// Insert information
			$query = "INSERT INTO army SET
				id = {$pid},
				time0 = " . $playerinfoData1["atm-0"] . ",
				win0 = " . $playerinfoData1["awn-0"] . ",
				loss0 = " . $playerinfoData1["alo-0"] . ", 
				best0 = " . $playerinfoData1["abr-0"] . ",
				time1 = " . $playerinfoData1["atm-1"] . ", 
				win1 = " . $playerinfoData1["awn-1"] . ",
				loss1 = " . $playerinfoData1["alo-1"] . ",
				best1 = " . $playerinfoData1["abr-1"] . ",
				time2 = " . $playerinfoData1["atm-2"] . ",
				win2 = " . $playerinfoData1["awn-2"] . ",
				loss2 = " . $playerinfoData1["alo-2"] . ", 
				best2 = " . $playerinfoData1["abr-2"] . ",
				time3 = " . $playerinfoData1["atm-3"] . ",
				win3 = " . $playerinfoData1["awn-3"] . ",
				loss3 = " . $playerinfoData1["alo-3"] . ", 
				best3 = " . $playerinfoData1["abr-3"] . ",
				time4 = " . $playerinfoData1["atm-4"] . ",
				win4 = " . $playerinfoData1["awn-4"] . ",
				loss4 = " . $playerinfoData1["alo-4"] . ", 
				best4 = " . $playerinfoData1["abr-4"] . ",
				time5 = " . $playerinfoData1["atm-5"] . ",
				win5 = " . $playerinfoData1["awn-5"] . ",
				loss5 = " . $playerinfoData1["alo-5"] . ", 
				best5 = " . $playerinfoData1["abr-5"] . ",
				time6 = " . $playerinfoData1["atm-6"] . ",
				win6 = " . $playerinfoData1["awn-6"] . ",
				loss6 = " . $playerinfoData1["alo-6"] . ", 
				best6 = " . $playerinfoData1["abr-6"] . ",
				time7 = " . $playerinfoData1["atm-7"] . ",
				win7 = " . $playerinfoData1["awn-7"] . ",
				loss7 = " . $playerinfoData1["alo-7"] . ", 
				best7 = " . $playerinfoData1["abr-7"] . ",
				time8 = " . $playerinfoData1["atm-8"] . ",
				win8 = " . $playerinfoData1["awn-8"] . ",
				loss8 = " . $playerinfoData1["alo-8"] . ", 
				best8 = " . $playerinfoData1["abr-8"] . ",
				time9 = " . $playerinfoData1["atm-9"] . ",
				win9 = " . $playerinfoData1["awn-9"] . ",
				loss9 = " . $playerinfoData1["alo-9"] . ", 
				best9 = " . $playerinfoData1["abr-9"] . "                   				
			";
			$result = $DB->exec($query);
		}
		
		// Kits
		$result = $DB->query("SELECT * FROM kits WHERE id = {$pid}");
		if($result instanceof PDOStatement && !$result->rowCount())
		{
			// Insert information
			$query = "INSERT INTO kits SET
				id = {$pid},
				time0 = " . $playerinfoData1["ktm-0"] . ",
				kills0 = " . $playerinfoData1["kkl-0"] . ",
				deaths0 = " . $playerinfoData1["kdt-0"] . ",		
				time1 = " . $playerinfoData1["ktm-1"] . ",
				kills1 = " . $playerinfoData1["kkl-1"] . ",
				deaths1 = " . $playerinfoData1["kdt-1"] . ",			
				time2 = " . $playerinfoData1["ktm-2"] . ",
				kills2 = " . $playerinfoData1["kkl-2"] . ",
				deaths2 = " . $playerinfoData1["kdt-2"] . ",	
				time3 = " . $playerinfoData1["ktm-3"] . ",
				kills3 = " . $playerinfoData1["kkl-3"] . ",
				deaths3 = " . $playerinfoData1["kdt-3"] . ",
				time4 = " . $playerinfoData1["ktm-4"] . ",
				kills4 = " . $playerinfoData1["kkl-4"] . ",
				deaths4 = " . $playerinfoData1["kdt-4"] . ",
				time5 = " . $playerinfoData1["ktm-5"] . ",
				kills5 = " . $playerinfoData1["kkl-5"] . ",
				deaths5 = " . $playerinfoData1["kdt-5"] . ",
				time6 = " . $playerinfoData1["ktm-6"] . ",
				kills6 = " . $playerinfoData1["kkl-6"] . ",
				deaths6 = " . $playerinfoData1["kdt-6"] . "
			";
			$result = $DB->exec($query);
		}
		
		// Vehicles					
		$result = $DB->query("SELECT * FROM vehicles WHERE id = {$pid}");
		if($result instanceof PDOStatement && !$result->rowCount())
		{ 
			// Insert information
			$query = "INSERT INTO vehicles SET
				id = {$pid},		
				time0 = " . $playerinfoData1["vtm-0"] . ",
				time1 = " . $playerinfoData1["vtm-1"] . ",
				time2 = " . $playerinfoData1["vtm-2"] . ",
				time3 = " . $playerinfoData1["vtm-3"] . ",
				time4 = " . $playerinfoData1["vtm-4"] . ",
				time5 = " . $playerinfoData1["vtm-5"] . ",
				time6 = " . $playerinfoData1["vtm-6"] . ",
				timepara = 0,
				kills0 = " . $playerinfoData1["vkl-0"] . ",
				kills1 = " . $playerinfoData1["vkl-1"] . ",
				kills2 = " . $playerinfoData1["vkl-2"] . ",
				kills3 = " . $playerinfoData1["vkl-3"] . ",
				kills4 = " . $playerinfoData1["vkl-4"] . ",
				kills5 = " . $playerinfoData1["vkl-5"] . ",
				kills6 = " . $playerinfoData1["vkl-6"] . ",
				deaths0 = " . $playerinfoData1["vdt-0"] . ",
				deaths1 = " . $playerinfoData1["vdt-1"] . ",
				deaths2 = " . $playerinfoData1["vdt-2"] . ",
				deaths3 = " . $playerinfoData1["vdt-3"] . ",
				deaths4 = " . $playerinfoData1["vdt-4"] . ",
				deaths5 = " . $playerinfoData1["vdt-5"] . ",
				deaths6 = " . $playerinfoData1["vdt-6"] . ",
				rk0 = " . $playerinfoData1["vkr-0"] . ",
				rk1 = " . $playerinfoData1["vkr-1"] . ",
				rk2 = " . $playerinfoData1["vkr-2"] . ",
				rk3 = " . $playerinfoData1["vkr-3"] . ",
				rk4 = " . $playerinfoData1["vkr-4"] . ",
				rk5 = " . $playerinfoData1["vkr-5"] . ",
				rk6 = " . $playerinfoData1["vkr-6"] . "
			";
			$result = $DB->exec($query);		
		}
		
		// Weapons
		$result = $DB->query("SELECT * FROM weapons WHERE id = {$pid}");
		if($result instanceof PDOStatement && !$result->rowCount())
        {
			// Insert information
			$query = "INSERT INTO weapons SET
				id = {$pid},
				time0 = " . $playerinfoData1["wtm-0"] . ",
				time1 = " . $playerinfoData1["wtm-1"] . ",
				time2 = " . $playerinfoData1["wtm-2"] . ",
				time3 = " . $playerinfoData1["wtm-3"] . ",
				time4 = " . $playerinfoData1["wtm-4"] . ",
				time5 = " . $playerinfoData1["wtm-5"] . ",
				time6 = " . $playerinfoData1["wtm-6"] . ",
				time7 = " . $playerinfoData1["wtm-7"] . ",
				time8 = " . $playerinfoData1["wtm-8"] . ",
				knifetime = " . $playerinfoData1["wtm-9"] . ",
				c4time = 0,
				handgrenadetime = " . $playerinfoData1["wtm-12"] . ",
				claymoretime = " . $playerinfoData1["wtm-11"] . ",
				shockpadtime = " . $playerinfoData1["wtm-10"] . ",
				atminetime = 0,
				tacticaltime = 0,
				grapplinghooktime = 0,
				ziplinetime = 0,
				kills0 = " . $playerinfoData1["wkl-0"] . ",
				kills1 = " . $playerinfoData1["wkl-1"] . ",
				kills2 = " . $playerinfoData1["wkl-2"] . ",
				kills3 = " . $playerinfoData1["wkl-3"] . ",
				kills4 = " . $playerinfoData1["wkl-4"] . ",
				kills5 = " . $playerinfoData1["wkl-5"] . ",
				kills6 = " . $playerinfoData1["wkl-6"] . ",
				kills7 = " . $playerinfoData1["wkl-7"] . ",
				kills8 = " . $playerinfoData1["wkl-8"] . ",
				knifekills = " . $playerinfoData1["wkl-9"] . ",
				c4kills = 0,
				handgrenadekills = " . $playerinfoData1["wkl-12"] . ",
				claymorekills = " . $playerinfoData1["wkl-11"] . ",
				shockpadkills = " . $playerinfoData1["wkl-10"] . ",
				atminekills = 0,
				deaths0 = " . $playerinfoData1["wdt-0"] . ",
				deaths1 = " . $playerinfoData1["wdt-1"] . ",
				deaths2 = " . $playerinfoData1["wdt-2"] . ",
				deaths3 = " . $playerinfoData1["wdt-3"] . ",
				deaths4 = " . $playerinfoData1["wdt-4"] . ",
				deaths5 = " . $playerinfoData1["wdt-5"] . ",
				deaths6 = " . $playerinfoData1["wdt-6"] . ",
				deaths7 = " . $playerinfoData1["wdt-7"] . ",
				deaths8 = " . $playerinfoData1["wdt-8"] . ",
				knifedeaths = " . $playerinfoData1["wdt-9"] . ",
				c4deaths = 0,
				handgrenadedeaths = " . $playerinfoData1["wdt-12"] . ",
				claymoredeaths = " . $playerinfoData1["wdt-11"] . ",
				shockpaddeaths = " . $playerinfoData1["wdt-10"] . ",
				atminedeaths = 0,
				ziplinedeaths = 0,
				grapplinghookdeaths = 0,
				tacticaldeployed = " . $playerinfoData1["de-6"] . ",
				grapplinghookdeployed = " . $playerinfoData1["de-7"] . ",
				ziplinedeployed = " . $playerinfoData1["de-8"] . ",
				fired0 = 0,
				fired1 = 0,
				fired2 = 0,
				fired3 = 0,
				fired4 = 0,
				fired5 = 0,
				fired6 = 0,
				fired7 = 0,
				fired8 = 0,
				knifefired = 0,
				c4fired = 0,
				claymorefired = 0,
				handgrenadefired = 0,
				shockpadfired = 0,
				atminefired = 0,
				hit0 = 0,
				hit1 = 0,
				hit2 = 0,
				hit3 = 0,
				hit4 = 0,
				hit5 = 0,
				hit6 = 0,
				hit7 = 0,
				hit8 = 0,
				knifehit = 0,
				c4hit = 0,
				claymorehit = 0,
				handgrenadehit = 0,
				shockpadhit = 0,
				atminehit = 0
			";
			$result = $DB->exec($query);		
		}

		// Maps 
		$result = $DB->query("SELECT * FROM maps WHERE id = {$pid}");
		if($result instanceof PDOStatement && !$result->rowCount())
		{
			$MAPS = array(0,1,2,3,4,5,6,100,101,102,103,105,601,300,301,302,303,304,305,306,307,10,11,110,200,201,202,12);
			
			foreach($MAPS as $key => $x)
			{
				// Insert information	
				$query = "INSERT INTO maps SET
					id = {$pid},
					mapid = {$x},
					time = " . $playerinfoData2["mtm-". $x] . ",
					win = " . $playerinfoData2["mwn-". $x] . ",
					loss = " . $playerinfoData2["mls-". $x] . ",
					best = 0,
					worst = 0
				";
				$result = $DB->exec($query);
			}
        }

        // Insert all unlocks as NO... Unlocks are processed in the getunlocksinfo.aspx
        // When the player first visits the bfhq
        for ($i = 11; $i < 100; $i += 11)
        {
            $query = "INSERT INTO unlocks SET
                id = {$pid},
                kit = {$i},
                state = 'n'
            ";
            $result = $DB->exec($query);

        }
        for ($i = 111; $i < 556; $i += 111)
        {
            $query = "INSERT INTO unlocks SET
                id = {$pid},
                kit = {$i},
                state = 'n'
            ";
            $result = $DB->exec($query);
        }
        
        // Awards
        $result = $DB->query("SELECT * FROM awards WHERE id = {$pid}");
		if($result instanceof PDOStatement && !$result->rowCount())
        {
            foreach($playerinfoData4 as $key => $value) 
            {
               // Insert information
                $query = "INSERT INTO awards (`id`, `awd`, `level`, `earned`, `first`) 
                    VALUES ('".$pid."', '".$value['id']."', '".$value['level']."', '".$value['when']."', '".$value['first']."')"; 
                $DB->exec($query);
            } 
        }
		
		// Success
		echo json_encode( array('success' => true, 'message' => "Player {$playerinfoData1["nick"]} (PID: {$pid}) has been successfully imported !") );
	}
}
