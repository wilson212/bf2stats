<?php
class Login 
{
	public function Init() 
	{
		$Template = new Template();
		$Template->render('login', false);
	}
}
?>