<?php
class Mergeplayers
{
	public function Init() 
	{
		// Make sure the database if offline
		if(DB_VER == '0.0.0')
			redirect('home');
			
		// Check for post data
		if($_POST['action'] == 'merge')
		{
			$this->Process();
		}
		else
		{
			// Setup the template
			$Template = new Template();
			$Template->render('mergeplayers');
		}
	}
	
	public function Process()
	{
		// Make Sure Script doesn't timeout
		set_time_limit(0);

		// Load the database and player class
		$DB = Database::GetConnection();
		$Player = new Player();
		
		// Get PlayerID's
		$pids = array();
		$pids[0] = (isset($_POST['target_pid'])) ? intval($_POST['target_pid']) : 0; // Target PID
		$pids[1] = (isset($_POST['source_pid'])) ? intval($_POST['source_pid']) : 0; // Source PID
		
		// Make sure the PID's dont match!
		if($pids[0] == $pids[1]) 
		{
			echo json_encode( array('success' => false, 'message' => 'Target player cannot be the same player as the Source player!') );
			die();
		}
		
		// Check Players Exist
		foreach ($pids as $key => $pid) 
		{
			// Make sure PID's are valid
			if (!is_numeric($pid) || $pid == 0) 
			{
				echo json_encode( array('success' => false, 'message' => 'Invalid player ID\'s entered.') );
				die();
			}
			
			// Retrieve our player
			$result = $DB->query("SELECT `id` FROM `player` WHERE `id` = {$pid}");
			if($result instanceof PDOStatement && $result->fetchColumn())
			{
				$pids_exist = true;
			} 
			else 
			{
				echo json_encode( array('success' => false, 'message' => "PID ({$pid}) is not a valid player!") );
				die();
			}
		}
		
		// Make this alitte more simple
		$target = $pids[0]; // <-- Player getting merged INTO
		$source = $pids[1]; // <-- Player getting put into the source, and Removed
		
		// Create our log array
		$log = array();
		$log[] = "Merging Player $source into Player $target ...";
		
		// Merge Single-line data tables
		$DataTables = array('army','kits','vehicles','weapons','player');
		foreach ($DataTables as $DataTable) 
		{
			$log[] =  " -> Merging {$DataTable} table...";
			$query = "SELECT * FROM {$DataTable} WHERE `id` = {$source}";
			$result = $DB->query($query);
			if($result instanceof PDOStatement && ($row = $result->fetch()))
			{
				// Build Update Query
				$query = "UPDATE {$DataTable} SET ";
				
				foreach($row as $colname => $colvalue)
				{
					if($colname == "id")
						continue;
						
					if(is_numeric($colvalue))
					{
						if($DataTable== "player")
						{
							if($colname == 'rank')
								$query .= "`" . $colname . "` = 0,\n";
							elseif($colname == 'joined') 
								$query .= "`" . $colname . "` = " . $colvalue . ",\n";
							elseif($colname == 'lastonline') 
								$query .= "`" . $colname . "` = `" . $colname . "`,\n";
							elseif($colname == 'rndscore') 
								$query .= "`" . $colname . "` = (SELECT IF(" . $colvalue . " > `" . $colname . "`, " . $colvalue . ", `" . $colname . "`)),\n";
							else 
								$query .= "`" . $colname . "` = `" . $colname . "` + " . $colvalue . ",\n";
						}
						else
							$query .= "`" . $colname . "` = `" . $colname . "` + " . $colvalue . ",\n";
					}
				}
				$query = rtrim($query, ",\n") . "\nWHERE `id` = {$target};";
				
				// Update Data
				if($DB->exec($query) !== false) 
				{
					$log[] =  "\t\tSuccess!";
					
					// Remove Old Data
					$query = "DELETE FROM `{$DataTable}` WHERE `id` = {$source};";
					if ($DB->exec($query) !== false) 
						$log[] =  " -> Old Player Data ({$DataTable}) Removed.";
					else 
						$log[] =  "\t\tFailed to Remove Old Player Data from ({$DataTable})! ";
				} 
				else 
				{
					$error = $DB->errorInfo();
					$log[] =  "\t\t". $error[2];
					echo json_encode( array('success' => false, 'message' => "Fetal Error while merging players: <br /><br />Mysql Error: ". $error[2]) );
					die();
				}
			}
		}
		
		// Reset Unlocks
		$log[] =  " -> Reseting Unlocks for Player ({$target})...";
		$query = "UPDATE `unlocks` SET `state` = 'n' WHERE `id` = {$target}";
		if($DB->exec($query) !== false) 
		{
			$query = "UPDATE `player` SET `availunlocks` = 0, `usedunlocks` = 0 WHERE `id` = {$target}";
			if($DB->exec($query) !== false) 
			{
				$log[] =  "\t\tSuccess!";
			} 
			else 
			{
				$error = $DB->errorInfo();
				$log[] =  "\t\tReset Unlocks Failed! ". $error[2];
			}
			
			// Remove Old Unlocks Data
			$log[] =  " -> Removing Old Unlocks for Player ({$source})...";
			$query = "DELETE FROM `unlocks` WHERE `id` = {$source}";
			if($DB->exec($query) !== false) 
			{
				$log[] =  "\t\tSuccess!";
			} 
			else 
			{
				$error = $DB->errorInfo();
				$log[] =  "\t\tUnlocks Removal Failed! ". $error[2];
			}
		} 
		else 
		{
			$error = $DB->errorInfo();
			$log[] =  "\t\tFailed! ". $error[2];
			echo json_encode( array('success' => false, 'message' => "Fetal Error While Resetting Unlocks: <br /><br />Mysql Error: ". $error[2]) );
			die();
		}
		
		// Merge Awards Data
		$log[] =  " -> Merging Awards table...";
		$query = "SELECT * FROM `awards` WHERE `id` = {$source};";
		$result = $DB->query($query);
		if( $result instanceof PDOStatement )
		{
			while($rowsrc = $result->fetch())
			{
				// Check Awards exist
				if ($rowsrc['awd']) 
				{
					$query = "SELECT * FROM `awards` WHERE `id` = {$target} AND `awd` = " . $rowsrc['awd'] . "a;";
					$chkresult = $DB->query($query);
					if( $chkresult instanceof PDOStatement && ($rowdest = $chkresult->fetch())) 
					{
						// Update Award
						$query = "UPDATE `awards` SET\n";
						switch ($rowsrc['awd'])
						{
							case 2051902:	// Gold
							case 2051907:	// Silver
							case 2051919:	// Bronze
								$query .= "`level` = `level` + " . $rowsrc['level'] . ",\n";
								break;
							default:
								$query .= "`level` = " . max((int)$rowsrc['level'], (int)$rowdest['level']) . ",\n";
						}

						$query .= "`earned` = " . min((int)$rowsrc['earned'], (int)$rowdest['earned']) . ",\n";
						$query .= "`first` = " . min((int)$rowsrc['first'], (int)$rowdest['first']) . "\n";
						$query .= "WHERE `id` = {$target} AND `awd` = " . $rowsrc['awd'] . ";";
						if($DB->exec($query) !== false)
						{
							$log[] =  "\t\tAward {$rowsrc['awd']} Update Success!";
						} 
						else 
						{
							$error = $DB->errorInfo();
							$log[] =  "\t\tAward {$rowsrc['awd']} Update Failed: ". $error[2];
						}
					} 
					else 
					{
						// Insert Award
						$query  = "INSERT INTO `awards` SET\n";
						$query .= "`id` = {$target},\n";
						$query .= "`awd` = " . $rowsrc['awd'] . ",\n";
						$query .= "`level` = " . $rowsrc['level'] . ",\n";
						$query .= "`earned` = " . $rowsrc['earned'] . ",\n";
						$query .= "`first` = " . $rowsrc['first'] . ";";
						if($DB->exec($query) !== false) 
						{
							$log[] =  "\t\tAward {$rowsrc['awd']} Insert Success!";
						} 
						else 
						{
							$error = $DB->errorInfo();
							$log[] =  "\t\tAward {$rowsrc['awd']} Insert Failed: ". $error[2];
						}
					}
				}
			}
			$log[] =  "\t\tAwards Table Merged!";
			
			// Remove Old Awards Data
			$log[] =  " -> Removing Old Awards for Player ({$source})...";
			$query = "DELETE FROM `awards` WHERE `id` = {$source}";
			if ($DB->exec($query) !== false) 
			{
				$log[] =  "\t\tSuccess!";
			} 
			else 
			{
				$error = $DB->errorInfo();
				$log[] =  "\t\tFailed! ". $error[2];
			}
		}
		
		// Merge Maps Data
		$log[] =  " -> Merging Maps table...";
		$query = "SELECT * FROM `maps` WHERE `id` = {$source};";
		$result = $DB->query($query);
		if( $result instanceof PDOStatement )	
		{
			while($rowsrc = $result->fetch())
			{
				// Check Map exist
				if ($rowsrc['mapid'] >= 0) 
				{
					$query = "SELECT * FROM `maps` WHERE `id`= {$target} AND `mapid` = " . $rowsrc['mapid'] . " LIMIT 1;";
					$chkresult = $DB->query($query);
					if( $chkresult instanceof PDOStatement && ($rowdest = $chkresult->fetch()))
					{
						// Update Map Data
						$query = "UPDATE `maps` SET";
						$query .= " `time` = `time` + " . $rowsrc['time'] . ",";
						$query .= " `win` = `win` + " . $rowsrc['win'] . ",";
						$query .= " `loss` = `loss` + " . $rowsrc['loss'] . ",";
						if ($rowsrc['best'] > $rowdest['best']) 
						{
							$query .= " `best` = " . $rowsrc['best'] . ",";
						}
						if ($rowsrc['worst'] < $rowdest['worst']) 
						{
							$query .= " `worst` = `worst` + " . $rowsrc['worst'];
						}
						
						// Trim the last comma if there is one
						$query = trim($query, ',');
						$query .= " WHERE `id` = '{$target}' AND `mapid` = '" . $rowsrc['mapid'] . "';";
						if($DB->exec($query) !== false) 
						{
							$log[] =  "\t\tMap {$rowsrc['mapid']} Update Success!";
						} 
						else 
						{
							$error = $DB->errorInfo();
							$log[] =  "\t\tMap {$rowsrc['mapid']} Update Failed: ". $error[2];
						}
					}
					else 
					{
						// Insert Map Data
						$query  = "INSERT INTO `maps` SET\n";
						$query .= "`id` = {$target},\n";
						$query .= "`mapid` = " . $rowsrc['mapid'] . ",\n";
						$query .= "`time` = " . $rowsrc['time'] . ",\n";
						$query .= "`win` = " . $rowsrc['win'] . ",\n";
						$query .= "`loss` = " . $rowsrc['loss'] . ",\n";
						$query .= "`best` = " . $rowsrc['best'] . ",\n";
						$query .= "`worst` = " . $rowsrc['worst'] . ";";
						if($DB->exec($query) !== false) 
						{
							$log[] =  "\t\tMap {$rowsrc['mapid']} Insert Success!";
						} 
						else 
						{
							$error = $DB->errorInfo();
							$log[] =  "\t\tMap {$rowsrc['mapid']} Insert Failed: ". $error[2];
						}
					}
				} 
				else 
				{
					$log[] =  "\t\tMapID Invalid!";
				}
			}
			$log[] =  "\t\tDone!";
			
			// Remove Old Maps Data
			$log[] =  " -> Removing Old Maps for Player ({$source})...";
			$query = "DELETE FROM `maps` WHERE `id` = {$source}";
			if($DB->exec($query) !== false) 
			{
				$log[] =  "\t\tSuccess!";
			} 
			else 
			{
				$error = $DB->errorInfo();
				$log[] =  "\t\tFailed! : ". $error[2];
			}
		}
		
		// Update Kills Data
		$log[] =  " -> Updating Kills Data...";
		$query = "SELECT * FROM `kills` WHERE `attacker` = {$source};";
		$result = $DB->query($query);
		if( $result instanceof PDOStatement )	
		{
			while($rowsrc = $result->fetch()) 
			{
				// Check Kills exist
				if ($rowsrc['victim']) 
				{
					$query = "SELECT * FROM `kills` WHERE `attacker` = {$target} AND `victim` = " . $rowsrc['victim'] . ";";
					$chkresult = $DB->query($query);
					if( $chkresult instanceof PDOStatement && ($row = $chkresult->fetch()))
					{
						// Update Existing record
						$query = "UPDATE `kills` SET\n";
						$query .= "`count` = `count` + " . $rowsrc['count'] . "\n";
						$query .= "WHERE attacker = {$target} AND victim = " . $rowsrc['victim'] . ";";
						if ($DB->exec($query) === false)  
						{
							$error = $DB->errorInfo();
							$log[] =  "\t\tERROR: Kills data not updated: ". $error[2];
						}
					}
					else 
					{
						// Insert Kills
						$query  = "INSERT INTO `kills` SET\n";
						$query .= "`attacker` = {$target},\n";
						$query .= "`victim` = " . $rowsrc['victim'] . ",\n";
						$query .= "`count` = " . $rowsrc['count'] . ";";
						if($DB->exec($query) === false) 
						{
							$error = $DB->errorInfo();
							$log[] =  "\t\tERROR:Kills data not inserted: ". $error[2];
						}
					}
				}
			}
			$log[] =  "\t\tKills Done!";
			
			// Remove Old Kills Data
			$log[] =  " -> Removing Old Kills for Player ({$source})...";
			$query = "DELETE FROM `kills` WHERE `attacker` = {$source}";
			if ($DB->exec($query) !== false) 
			{
				$log[] =  "\t\tSuccess!";
			} 
			else 
			{
				$error = $DB->errorInfo();
				$log[] =  "\t\tFailed! : ". $error[2];
			}
		}
		
		// Update Deaths Data
		$log[] =  " -> Updating Deaths Data...";
		$query = "SELECT * FROM `kills` WHERE `victim` = {$source};";
		$result = $DB->query($query);
		if( $result instanceof PDOStatement )
		{
			while($rowsrc = $result->fetch()) 
			{
				// Check Deaths exist
				if ($rowsrc['attacker']) 
				{
					$query = "SELECT * FROM `kills` WHERE `attacker` = " . $rowsrc['attacker'] . " AND `victim` = {$target};";
					$chkresult = $DB->query($query);
					if( $chkresult instanceof PDOStatement  && ($row = $chkresult->fetch())) 
					{
						// Update Existing record
						$query = "UPDATE `kills` SET\n";
						$query .= "`count` = `count` + " . $rowsrc['count'] . "\n";
						$query .= "WHERE `attacker` = " . $rowsrc['attacker'] . " AND `victim` = {$target};";
						if ($DB->exec($query) === false) 
						{
							$error = $DB->errorInfo();
							$log[] =  "\t\tERROR: Kills data not updated: ". $error[2];
						}
					} 
					else 
					{
						// Insert Deaths
						$query  = "INSERT INTO `kills` SET\n";
						$query .= "`attacker` = " . $rowsrc['attacker'] . ",\n";
						$query .= "`victim` = {$target},\n";
						$query .= "`count` = " . $rowsrc['count'] . ";";
						if($DB->exec($query) === false) 
						{
							$error = $DB->errorInfo();
							$log[] =  "\t\tERROR: Kills data not inserted: ". $error[2];
						}
					}
				}
			}
			$log[] =  "\t\tDeaths Done!";
			
			// Remove Old Deaths Data
			$log[] =  " -> Removing Old Deaths for Player ({$source})...";
			$query = "DELETE FROM `kills` WHERE `victim` = {$source}";
			if($DB->exec($query) !== false) 
			{
				$log[] =  "\t\tSuccess!";
			} 
			else 
			{
				$error = $DB->errorInfo();
				$log[] =  "\t\tFailed! :  ". $error[2];
			}
		}

		$log[] =  "Done! :)\n";
		
		// Validate rank
		$Player->validateRank($target);
		
		// Delete the old player
		if( !$Player->deletePlayer($source) )
		{
			echo json_encode( 
				array(
					'success' => true,
					'type' => 'warning',
					'message' => "Failed to delete source Player ($source). You will need to manually delete this player from the \"Manage Players\" Menu. "
				) 
			);
		}
		else
		{
			// Success
			echo json_encode( 
				array(
					'success' => true,
					'type' => 'success',
					'message' => "Player ($source) -> Merged to -> Player ($target) Successfully!"
				) 
			);
		}
		
		// Log the messages
		$lines = "Merge Players Logging Started: ". date('Y-m-d H:i:s') . PHP_EOL;
		foreach($log as $line)
		{
			$lines .= $line . PHP_EOL;
		}
		$lines .= PHP_EOL;
		$log = SYSTEM_PATH . DS . 'logs' . DS . 'merge_players.log';
		$file = @fopen($log, 'a');
		@fwrite($file, $lines);
		@fclose($file);
	}
}
?>