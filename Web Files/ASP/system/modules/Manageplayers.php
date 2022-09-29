<?php
class Manageplayers
{
	public function Init() 
	{
		// Make sure the database if offline
		if(DB_VER == '0.0.0')
			redirect('home');
			
		// Check for post data
		if(isset($_GET['ajax']))
		{
			switch($_GET['ajax'])
			{
				case "list":
					$this->displayPlayerList();
					break;
					
				case "player":
					$this->processPlayer($_POST['id']);
					break;
					
				case "action":
					$this->processAction($_POST['action'], $_POST['id']);
					break;
			}
		}
		else
		{
			// Setup the template
			$Template = new Template();
			$Template->render('manageplayers');
		}
	}
	
	public function displayPlayerList()
	{
		/* Array of database columns which should be read and sent back to DataTables. Use a space where
		 * you want to insert a non-database field (for example a counter or static image)
		 */
		$aColumns = array( 'id', 'name', 'clantag', 'rank', 'score', 'country', 'permban' );
		
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "id";
		
		/* DB table to use */
		$sTable = "player";
		
		// Get a column count
        $aColumnCount = count($aColumns);
		
		// Get database connections
		$DB = Database::GetConnection();
        
        /* Paging */
        $sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
            $sLimit = "LIMIT ". addslashes( $_GET['iDisplayStart'] ) .", ". addslashes( $_GET['iDisplayLength'] );
        
        /*  Ordering */
        $sOrder = "";
        if( isset( $_GET['iSortCol_0'] ) )
        {
            $sOrder = "ORDER BY  ";
            for($i = 0; $i < intval($_GET['iSortingCols']); $i++)
            {
                if( $_GET[ 'bSortable_'. intval($_GET['iSortCol_'.$i]) ] == "true" )
                {
                    $sOrder .= "`". $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."`". addslashes( $_GET['sSortDir_'.$i] ) .", ";
                }
            }
            
            $sOrder = substr_replace( $sOrder, "", -2 );
            if( $sOrder == "ORDER BY" ) $sOrder = "";
        }
        
        /* 
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        if( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
        {
            $sWhere = "WHERE (";
            for ($i = 0; $i < count($aColumns); $i++)
            {
                $sWhere .= "`". $aColumns[$i]."` LIKE '%". addslashes( $_GET['sSearch'] ) ."%' OR ";
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }
		
		/* AI Filtering */
		if(Config::Get('admin_ignore_ai'))
			$sWhere = (( empty($sWhere) ) ? "WHERE " : " AND ") ." `isbot` = 0 ";
        
        /* Individual column filtering */
        for($i = 0; $i < count($aColumns); $i++)
        {
            if( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
            {
                $sWhere .= ($sWhere == "") ? "WHERE " : " AND ";
                $sWhere .= "`".$aColumns[$i]."` LIKE '%". addslashes($_GET['sSearch_'.$i]) ."%' ";
            }
        }
        
        /* SQL queries, Get data to display */
        $columns = "`". str_replace(",``", " ", implode("`, `", $aColumns)) ."`";
        $sQuery = "SELECT SQL_CALC_FOUND_ROWS {$columns} FROM {$sTable} {$sWhere} {$sOrder} {$sLimit}";
        $rResult = $DB->query( $sQuery )->fetchAll();
        
        /* Data set length after filtering */
        $iFilteredTotal = $DB->query( "SELECT FOUND_ROWS()" )->fetchColumn();
        
        /* Total data set length */
        $iTotal = $DB->query( "SELECT COUNT(`".$sIndexColumn."`) FROM   $sTable" )->fetchColumn();

        /* Output */
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => intval($iTotal),
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        
        // Now add each row to the aaData
        foreach( $rResult as $aRow )
        {
            $row = array();
            for($i = 0; $i < $aColumnCount; $i++)
            {
                if( $aColumns[$i] == "version" )
                {
                    /* Special output formatting for 'version' column */
                    $row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
                }
                elseif( $aColumns[$i] != ' ' )
                {
                    /* General output */
                    $row[] = $aRow[ $aColumns[$i] ];
                }
            }
            
			// Fix name and clantag for special characters
			$row[1] = htmlspecialchars($row[1]);
			$row[2] = htmlspecialchars($row[2]);
			
			// Add manage button and country flag... also fancy little permban yes/no
			$C = ($row[5] == '') ? 'US' : strtoupper($row[5]);
			$flag = $C .'.png';
			$row[5] = '<img src="frontend/images/flags/'. $flag .'" height="16" width="24" alt="'. $C .'">';
			$row[6] = ($row[6] == 1) ? '<font color="red">Yes</font>' : '<font color="green">No</font>';
			$row[] = "<a id='edit' name='". $row[0] ."|". $row[1]."' href='#'>Manage</a>";
			$output['aaData'][] = $row;
        }
		
		echo json_encode( $output );
	}
	
	public function processPlayer($pid)
	{
		// Load the database
		$DB = Database::GetConnection();
		
		// Process action
		switch($_POST['action'])
		{
			case "fetch":
				// Get the player
				$query = "SELECT `name`, `rank`, `permban`, `clantag` FROM `player` WHERE `id` = ". intval($pid);
				$result = $DB->query( $query );
				if(!($result instanceof PDOStatement) || !is_array(($row = $result->fetch())))
				{
					echo json_encode( array('success' => false, 'message' => "Player ID ($pid) Does Not Exist!") );
					die();
				}
				
				echo json_encode( array('success' => true) + $row );
				break;
				
			case "update":
				// Get unlocks
				$query = "SELECT `availunlocks`, `usedunlocks` FROM `player` WHERE `id` = ". intval($pid);
				$unlocks = $DB->query( $query );
				if(!($unlocks instanceof PDOStatement) || !is_array(($unlocks = $unlocks->fetch())))
				{
					echo json_encode( array('success' => false, 'message' => "Player ID ($pid) Does Not Exist!") );
					die();
				}

				// Reset Unlocks
				if(isset($_POST['reset']) && $_POST['reset'] == 'on')
				{
					$query = "UPDATE `unlocks` SET `state` = 'n' WHERE id = ". intval($pid);
					$DB->exec( $query );
					$unlocks['availunlocks'] = $unlocks['availunlocks'] + $unlocks['usedunlocks'];
					$unlocks['usedunlocks'] = 0;
				}

				// Save the player
				$query = "UPDATE `player` SET 
					`rank` = ". intval($_POST['rank']) .",
					`availunlocks` = {$unlocks['availunlocks']}, 
					`usedunlocks` = {$unlocks['usedunlocks']},				
					`permban` = ". intval($_POST['permban']) .", 
					`clantag` = ". $DB->quote($_POST['clantag']) ."
					WHERE id = ". intval($pid);
				if($DB->exec($query) === false)
				{
					echo json_encode( array('success' => false, 'message' => "Failed to update player ID ($pid)") );
					die();
				}
				
				echo json_encode( array('success' => true, 'message' => 'Player Updated Successfully!') );
				break;
		}
	}
	
	public function processAction($action, $pid)
	{
		// Load our player class
		$Player = new Player();

		// Switch to our actions
		switch($action)
		{
				
			case "delete":
				echo json_encode( array('success' => $Player->deletePlayer($pid)) );
				break;
		}
	}
}
?>