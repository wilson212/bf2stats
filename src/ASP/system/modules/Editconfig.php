<?php
class Editconfig
{
	public function Init() 
	{
		// Check for post data
		if($_POST['action'] == 'save_config')
		{
			$this->ProcessSave();
		}
		else
		{
			// Setup the template
			$Template = new Template();
			$Template->set('config', Config::FetchAll());
			$Template->render('editconfig');
		}
	}
	
	public function ProcessSave()
	{
		foreach($_POST as $item => $val) 
		{
			$key = explode('__', $item);
			if($key[0] == 'cfg') 
			{
				Config::Set($key[1], $val);
			}
		}
		
		// Determine if our save is a success
		echo json_encode( array('success' => Config::Save()) );
	}
}
?>