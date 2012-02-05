<?php 
/****************************************************************************/
/*  				  < BF2 Statistics Admin Util >  						*/
/*            Original Author: The Shadow,  Copyright 2006                  */
/* 					 Unofficial v1.4.3 by Wilson212							*/
/*					  < http://bf2statistics.com >							*/
/*																			*/
/****************************************************************************/

/***************************************************************
 * Define that we are here in the BF2 Admin area, prevents direct linking of files
 ***************************************************************/
DEFINE('_BF2_ADMIN', 1);
(!isset($_POST['action'])) ? $_POST['action'] = null : '';
(!isset($_GET['action'])) ? $_GET['action'] = null : '';

/***************************************************************
 * Import Configurations and Security scripts
 ***************************************************************/
require('includes/utils.php');
require('includes/admin.security.php');
$cfg = new Config();

/***************************************************************
 * Check to see if users IP is authorized to view the ASP
 ***************************************************************/
if(!checkIpAuth($cfg->get('admin_hosts'))) 
{
	die("<font color='red'>ERROR:</font> You are NOT Authorised to access this Page!");
}

/***************************************************************
 * Import database class
 ***************************************************************/
include('includes/db/class.database.php');
$DB = new Database(
	$cfg->get('db_host'), 
	$cfg->get('db_port'), 
	$cfg->get('db_user'),
	$cfg->get('db_pass'),
	$cfg->get('db_name')
);


/***************************************************************
 * Check for login / logout requests
 ***************************************************************/
if($_POST['action'] == 'login') 
{
	processLogin();
}
elseif(($_POST['action'] == 'logout') || ($_GET['action'] == 'logout')) 
{
	processLogout();
}

/***************************************************************
 * Check database version
 ***************************************************************/
$dbver = getDbVer();
if($cfg->get('db_expected_ver') == $dbver) 
{
	$dbver_txt = $dbver;
}
else
{
	$dbver_txt = "<font color=\"red\"><b>".$dbver."</b></font>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	<title>BF2 - Private Stats Admin</title>
	<link rel="stylesheet" href="images/bf2admin.css" type="text/css"/>
	<script type='text/javascript' src='includes/admin.script.js'></script>
	<!--[if lt IE 7]>
	<script defer type="text/javascript" src="includes/pngfix.js"></script>
	<![endif]-->
</head>

<body>
<table border="0" width="100%" height="100%" id="maintable">
	<tr>
		<td width="130" height="80" style="border-bottom: 1px dotted #666666;"><a href="index.php?task=home"><img border="0" src="images/bf2logo.png"></a></td>
		<td align="center" valign="top" style="border-bottom: 1px dotted #666666;">
			<div class="title">Private Stats Admin</div>
			<div class="dbver">Code Version: <?php echo $cfg->get('db_expected_ver'); ?> || Database Version: <?php echo $dbver_txt; ?> </div>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="border-right: 1px dotted #666666;">
			<?php 
				require("includes/admin.menu.php"); 
			?>
		</td>
		<td align="left" valign="top">
			<?php
				if(isset($_POST['action']) && $_POST['action'] == 'process') 
				{
					require("includes/admin.process.php");
				}
				else
				{
					require("includes/admin.content.php");
				}
			?>
		</td>
	</tr>
</table>
</body>
</html>
<?php
// Close the DB connection
$DB->__destruct();
?>