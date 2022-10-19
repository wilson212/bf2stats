<?php
/* 
| --------------------------------------------------------------
| BF2 Statistics Admin Util
| --------------------------------------------------------------
| Author:       Steven Wilson 
| Copyright:    Copyright (c) 2012
| License:      GNU GPL v3
| ---------------------------------------------------------------
| Class: Player()
| ---------------------------------------------------------------
|
*/
class Player
{
	/* Class Variables */
	protected $DB;
	protected $messages = array();
	protected $rankdata = false;
	protected $awardsdata = false;
	
/*
| ---------------------------------------------------------------
| Constructer
| ---------------------------------------------------------------
*/ 
	public function __construct()
	{
		// Init DB connection
		$this->DB = Database::GetConnection();
		
		// Load Rank data
		if(!$this->rankdata)
		{
			require( SYSTEM_PATH . DS . 'data'. DS . 'ranks.php' );
			$this->rankdata = $ranks;
		}
		
		// Import Backend Awards Data
		if(!$this->awardsdata)
		{
			require_once( SYSTEM_PATH . DS . 'data' . DS . 'awards.php' );
			$this->awardsdata = buildBackendAwardsData('xpack');
		}
	}
	
/*
| ---------------------------------------------------------------
| Method: log()
| ---------------------------------------------------------------
|
| This method logs messages from the methods in this class
|
*/ 
	protected function log($message)
	{
		$this->messages[] = $message;
	}
	
/*
| ---------------------------------------------------------------
| Method: messages()
| ---------------------------------------------------------------
|
| This method returns all the logged messages
|
*/ 
	public function messages()
	{
		return $this->messages;
	}
  
/*
| ---------------------------------------------------------------
| Method: deletePlayer()
| ---------------------------------------------------------------
|
| This method is used to delete all player data from all bf2 tables
|
*/   
	public function deletePlayer($pid)
	{
		// Build Data Table Array
		$return = true;
		$DataTables = getDataTables();
		foreach ($DataTables as $DataTable) 
		{
			// Check Table Exists
			$result = $this->DB->query("SHOW TABLES LIKE '{$DataTable}'");
			if($result instanceof PDOStatement && $result->fetchColumn()) 
			{
				// Table Exists, lets clear it
				$query = "DELETE FROM `" . $DataTable . "` ";
				$query .= ($DataTable == 'kills') ? "WHERE `attacker` = {$pid} OR `victim` = {$pid};" : "WHERE `id` = {$pid};";
				$result = $this->DB->exec($query);
				if($result !== false) 
				{
					$this->log("Player removed from Table (" . $DataTable . ").");
				} 
				else 
				{
					$return = false;
					$this->log("Player *NOT* removed from Table (" . $DataTable . ")!");
				}
			}
		}
		
		return $return;
	}

/*
| ---------------------------------------------------------------
| Method: validateRank()
| ---------------------------------------------------------------
|
| This method will validate and correct the given players rank
| based on the values stored in the "system/data/ranks.php"
|
*/    
	public function validateRank($pid)
	{
		// Make sure the player exists
		$query = "SELECT `id`, `score`, `rank` FROM `player` WHERE `id`=". intval($pid);
		$result = $this->DB->query($query);
		if(!($result instanceof PDOStatement) || !($row = $result->fetch()))
			return false;

		// Setup our player variables
		$pid   = (int)$row['id'];
		$score = (int)$row['score'];
		$rank  = (int)$row['rank'];
		
		// Our set rank and expected ranks
		$setRank = 0;
		$expRank = 0;
		
		// Figure out which rank we are suppossed to be by points
		foreach($this->rankdata as $key => $value)
		{
			// Keep going till we are no longer in the correct point range
			if($value['points'] != -1 && ($value['points'] < $score))
			{
				$expRank = $key;
			}
		}
		
		// SetRank if we are good!
		if($rank == $expRank)
		{
			$setRank = $rank;
		}

		// If the rank isnt as expected, and we are not a 4 start gen... then we need to process ranks
		elseif($rank != $expRank && $rank != 21)
		{
			// Get player awards
			$query = "SELECT * FROM `awards` WHERE `id` = ". intval($pid);
			$awards = $this->DB->query($query);
			if(!($awards instanceof PDOStatement)) 
				$awards = array();
			else
				$awards = $awards->fetchAll();
			
			// Build our player awards list
			$player_awards = array();
			foreach($awards as $value)
				$player_awards[$value['awd']] = $value['level'];
			
			// Prevent rank skipping unless the player meets ALL prior rank requirements
			for($i = 1; $i <= $expRank; $i++)
			{
				/// Process Has Rank ///

				// First, we must check to see if the set rank is IN the net rank Reqs.
				$reqRank = $this->rankdata[$i]['has_rank'];
				if(is_array($reqRank))
				{
					if(!in_array($setRank, $reqRank))
					{
						// Check to see if the current ranks points match
						if($this->rankdata[$i]['points'] != $this->rankdata[$setRank]['points'])
							continue;
					}
				}
				elseif( $setRank != $reqRank)
				{
					// Check to see if the current ranks points match, if the do
					// We can continue, otherwise we cant go any higher
					if($this->rankdata[$i]['points'] != $this->rankdata[$setRank]['points'])
						continue;
				}   
				
				/// Process Has Awards ///
				
				// If rank requires medals, then we have to check if the player has them
				if(!empty($this->rankdata[$i]['has_awards']))
				{
					// Good marker
					$good = true;

					// Make sure the player has each reward required
					foreach($this->rankdata[$i]['has_awards'] as $award => $level)
					{
						// Check if the award is in the list of players earned awards
						if(array_key_exists($award, $player_awards))
						{
							// Check to see if the level of the earned award is geater or equalvalue of the required award
							if($player_awards[$award] >= $level)
								continue; // The award is good, move to the next award in the loop
							else
								$good = false; // Award level is too low
						}
						else
						{
							// The user doesnt have the award
							$good = false;
						}
					}
					
					// If we have the req. medals for this rank
					if($good == true)
						$setRank = $i;
				}
				else
					$setRank = $i;
			}
			
			// Done :)
		}
		
		// Donot unpromote smoc
		if($rank == 11 && $setRank < 11)
			return true;
		
		// Update Database if we arent a 4 star gen, or smoc with a higher rank award
		if(($rank != 21 && $rank != $setRank))
		{
			// Log
			$this->log(
				" -> Rank Correction (".$row['id']."):". PHP_EOL
				."\tPlayer Score: ". $score . PHP_EOL
				."\tExpected Rank: ". $expRank . PHP_EOL
				."\tFound Rank: ". $rank . PHP_EOL
				."\tNew Rank: ". $setRank
			);
			
			// Query the update
			$query = "UPDATE `player` SET `rank` = ". $setRank ." WHERE `id` = ". intval($pid);
			return $this->DB->exec($query);
		}
		
		// Return Success
		return TRUE;
	}

/*
| ---------------------------------------------------------------
| Method: checkBackendAwards()
| ---------------------------------------------------------------
|
| This method will validate and correct the given players 'army'
| awards based on the values stored in the "system/data/awards.php"
|
*/  
	public function checkBackendAwards($pid)
	{
		// Where clause Substitution String
		$awards_substr = "###";
		$awards = $this->awardsdata;
		$pid = intval($pid);
		
		// Make sure player exists
		if($this->DB->query("SELECT COUNT(`id`) FROM `player` WHERE `id` = ". $pid)->fetchColumn() == 0)
			return false;
		
		// Check against each reward
		foreach ($awards as $award) 
		{
			// Check if Player already has Award
			$query = "SELECT COUNT(`awd`) FROM `awards` WHERE `id` = {$pid} AND `awd` = {$award[0]}";
			$awardrows = $this->DB->query($query)->fetchColumn();
			
			// Fetch the 1st award
			if($awardrows)
			{
				$query = "SELECT `awd`, `level` FROM `awards` WHERE `id` = {$pid} AND `awd` = {$award[0]} LIMIT 1";
				$rowawd = $this->DB->query( $query )->fetch();
			}

			// Loop through each award, and check the criteria
			$chkcriteria = false;
			foreach ($award[3] as $criteria) 
			{
				// If award is medal, We Can receive multiple times
				if ($award[2] == 2) 
				{
					// Can receive multiple times
					$where = str_replace($awards_substr, (($awardrows > 0) ? $rowawd['level'] + 1 : 1), $criteria[3]);
				} 
				else 
				{
					$where = $criteria[3];
				}
				
				// Check to see if the player meets the requirments for the award
				$query = "SELECT {$criteria[1]} AS `checkval` FROM {$criteria[0]} WHERE `id` = {$pid} AND {$where};";
				$checkval = $this->DB->query( $query )->fetchColumn();
				if($checkval >= $criteria[2]) 
				{
					$chkcriteria = true;
				} 
				else 
				{
					$chkcriteria = false;
					break;
				}
			}
			
			// If player is meets the requirements, but hasnt been awarded the reward...
			if ($chkcriteria && $awardrows == 0) 
			{
				// Insert information
				$this->log(" -> Award Missing ({$award[0]}) for Player ({$pid}). Adding award to Players Awards...");
				$query = "INSERT INTO awards SET
					id = " . $pid . ",
					awd = {$award[0]},
					level = 1,
					earned = " . time() . ",
					first = 0;";
				$this->DB->exec( $query ); 
			}
			
			// Else, if Player has award but doesnt meet requirements
			elseif (!$chkcriteria && $awardrows > 0) 
			{
				// Delete information
				$this->log(" -> Player ({$pid}) Has Award ({$award[0]}), but does not meet requirements! Removing award...");
				$query = "DELETE FROM awards WHERE (id = " . $pid . " AND awd = {$award[0]});";
				$this->DB->exec( $query );
			}
			
			// Maybe additional award for medal?
			elseif($award[2] == 2 &&  $Medal_Next == true && $chkcriteria == true)
			{
				// Update Award
				$this->log(" -> Award Missing ({$award[0]}) for Player ({$pid}). Adding award to Players Awards...");
				$query = "UPDATE awards SET level = ". ($rowawd['level'] + 1) .", earned = " . time() . " WHERE id = " . $pid . " AND awd = {$award[0]}";
				$this->DB->exec( $query ); 
			}
		}

		return TRUE;
	}
}
