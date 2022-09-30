<?php
class Mapinfo
{
	public function Init() 
	{
		// Make sure the database if offline
		if(DB_VER == '0.0.0')
			redirect('home');
			
		// Get array
        $maps = array();
		$DB = Database::GetConnection();
		$result = $DB->query("SELECT * FROM `mapinfo` ORDER BY `id` ASC;");
		if($result instanceof PDOStatement) 
        {
            // Set proper time format
            $i = 0;
            while($row = $result->fetch())
            {
                // add map
                $maps[$i] = $row;
                
                // Set formated time
                $maps[$i]['time'] = formatTime($row['time']);
                
                // Format custom map text
                $maps[$i]['custom'] = ($row['custom'] == true) ? "<font color='red'>YES</font>" : "<font color='green'>NO</font>";
                
                // Increment
                ++$i;
            }
        }
	
		$Template = new Template();
		$Template->set('maps', $maps);
		$Template->render('mapinfo');
	}
}
?>