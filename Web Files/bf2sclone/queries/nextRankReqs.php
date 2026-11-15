<?php
/****************************************************************************/
/*  				< BF2s Clone 1SG / SGM rank checker >  					*/
/*              		   Written by:   <Wilson212>                    	*/
/****************************************************************************/


// *******************************************************************
// Checks to see if the player has the required rewards to make 1SG or SGM
function checkReqs($rank, $PID): bool
{
	global $link;
	// Lets make sure we are dealing with the correct ranks!
	// Rank 7 is MSG, Rank 9 is MGYSG
	if($rank == 7 || $rank == 9)
	{
		switch($rank)
		{
			// Rank is MSG
			case 7:
				$award_list = [
					'1031105' => 1, // Engineer Combat Badge
					'1031109' => 1, // Sniper Combat Badge
					'1031113' => 1, // Medic Combat Badge
					'1031115' => 1, // Spec Ops Combat Badge
					'1031119' => 1, // Assault Combat Badge
					'1031120' => 1, // Anti-tank Combat Badge
					'1031121' => 1, // Support Combat Badge
					'1031406' => 1, // Knife Combat Badge
					'1031619' => 1 // Pistol Combat Badge
					//'1032415' => 1, // Explosives Ordinance Badge
					//'1190507' => 1, // Engineer Badge
					//'1190601' => 1, // First Aid Badge
					//'1191819' => 1  // Resupply Badge
				];
			break;
			
			// Rank is MGYSG
			case 9:
				$award_list = [
					'1031923' => 1, // Ground Defense
					'1220104' => 1, // Air Defense
					'1220118' => 1, // Armor Badge
					'1220122' => 1, // Aviator Badge
					'1220803' => 1, // Helicopter Badge
					'1222016' => 1  // Transport Badge
				];
			break;
		}
		
		// Initiate an array of players earned awards
		$player_awards = [];
		
		// Start a query to get users awards
		$query = "SELECT * FROM awards where id = $PID";
		$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
		
		// Build the players earned awards to an array
		while($row = mysqli_fetch_assoc($result)) 
		{
			$player_awards[$row['awd']] = $row['level'];
		}

		mysqli_free_result($result);
		
		// Start a loop. For each required award, check to see 2 things:
		// 1) The player has the award
		// 2) The level of the award is equal to or greater then required
		foreach($award_list as $award => $level)
		{
			// Check if the award is in the list of players earned awards
			if(array_key_exists($award, $player_awards))
			{
				$lvl = $player_awards[$award];
				
				// Check to see if the level of the earned award is geater or equal
				// value of the required award
				if($lvl >= $level)
				{
					// Dont return false :p
					// The award is good, move to the next award in the loop
					continue;
				}
				else
				{
					// return FALSE because the level is too low
					return FALSE;
				}
			}
			else
			{
				// Return FALSE because the user doesnt have the award
				return FALSE;
			}
		}
		
		// If the loop finished, then the player had all awards so return TRUE!
		return TRUE;
	}
	else
	{
		// What an error this is lol
		return FALSE;
	}
}

// *******************************************************************
// Removes rank 11 (SMOC) from the posted rank, converts to rank 12 (2LT)
function removeSMOC($r1, $r2, $r3): array
{
	if($r1 == 11) { $r1++; $r2++; $r3++; }
	if($r2 == 11) { $r2++; $r3++; }
	if ($r3 == 11) {
        $r3++;
    }
	return [$r1, $r2, $r3];
}

// *******************************************************************
// Main function... gets the next %s ranks based on requirements
/**
 * @return mixed[]
 */
function getNextRanks($PID, $rank, $count = 3): array
{
	// Get the next $count of ranks for the player
	if ($count == 0) {
        $count = 21;
    }
	$return = [];
	$i = 0;
	
	// loop
	while($rank < 21 && $i < $count)
	{
		$data = getNext3($PID, $rank);
		$i += count($data);
		$return = array_merge($return, $data);
		$rank = end($data);
	}
	
	// remove extra's
	$num = count($return);
	if($num > $count)
	{
		$dif = $num - $count;
		while($dif > 0)
		{
			array_pop($return);
			--$dif;
		}
	}
	return $return;
}

// *******************************************************************
// gets the next 3 ranks based on requirements
function getNext3($PID, $rank)
{
	// Build next 3 ranks
	$first = $rank + 1;
	$second = $rank + 2;
	$third = $rank + 3;
	
	// First we need to make sure that none of next 3 ranks are 1SG or SGM
	if($first == 8 || $first == 10)
	{
		$first++; // Increment
		$second++; // Increment
		$third++; // Increment
	}
	if($second == 8 || $second == 10)
	{
		$second++; // Increment
		$third++; // Increment
	}
	if ($third == 8 || $third == 10) {
        $third++;
    } // Increment
	
	// if -> NEXT <- rank is MSG, or MGYSGT
	if($first == 7 || $first == 9)
	{
		// Check to see if the player gets 1SG or SGM
		$award_check = checkReqs($first, $PID);
		
		// If a true return return, add 1 to the cuurent rank to make it 1SG or SGM
		if($award_check == TRUE)
		{
			switch($first)
			{
				case 7:
					$first = 8;
					$second = 9; 
					$third = 12;
				break;
				
				case 9:
					$first = 10;
					$second = 12;
					$third = 13;
				break;
			}
		}
		
		// 1SG or SGM is a no go
		else
		{	
			switch($first)
			{
				case 7:
					$second = 9; 
					$third = 12;
				break;
				
				case 9:
					$second = 12;
					$third = 13;
				break;
			}
		}
	}
	
	// Check the -> NEXT NeXT <-rank for MSG or MGYSGT
	if($second == 7 || $second == 9)
	{
		// Check to see if the player gets 1SG or SGM
		$award_check = checkReqs($second, $PID);
		
		// If a true return return, add 1 to the cuurent rank to make it 1SG or SGM
		if($award_check == TRUE)
		{
			$second++; // 8 or 10
			
			switch($second)
			{
				case 8:
					$third = 9;
				break;
		
				case 10:
					$third = 12; // 2nd Lieutenant
				break;
			}
		}
	}
	
	// Check the -> next next NEXT <- rank for MSG or MGYSGT
	if($third == 7 || $third == 9)
	{
		// Check to see if the player gets 1SG or SGM
		$award_check = checkReqs($third, $PID);
		
		// If a true return return, add 1 to the cuurent rank to make it 1SG or SGM
		if($award_check == TRUE)
		{
			$third++; // Increment
		}
	}
	
	// Just incase we got a rank 11 (SMOC), Remove itsince its not a promotable rank
	$return = removeSMOC($first, $second, $third);
	
	// Remove additional ranks
	foreach($return as $k => $v)
	{
		if ($v > 21) {
            unset($return[$k]);
        }
	}
	
	// return the next 3 ranks
	return $return;
}
?>