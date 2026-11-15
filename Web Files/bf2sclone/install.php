<?php
// Define a smaller Directory seperater and ROOT path
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__);
define('CACHE_PATH', ROOT . DS . 'cache' . DS);
define('TEMPLATE_PATH', ROOT . DS . 'template' . DS);

// Check for post data
$DBIP = $_POST["dbip"] ?? 0;
$DBNAME = $_POST["dbname"] ?? 0;
$DBLOGIN = $_POST["dblogin"] ?? 0;
$DBPASSWORD = $_POST["dbpassword"] ?? 0;

$TITLE = $_POST["title"] ?? 0;

// If no post data, then load the instaler	
if ($DBIP === 0)	
{
	echo '<style type="text/css">
	<!--
	.style1 {
		color: #FFFFFF;
		font-weight: bold;
	}
	.style4 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: small; }
	.style6 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small; }
.style7 {font-size: small}
.style8 {font-family: Verdana, Arial, Helvetica, sans-serif}
	-->
	</style>
	<form id="form1" name="form1" method="post" action="install.php">
	<table width="480" border="1" align="center" bordercolor="#000000" cellspacing="0">
	  <tr>
		<td bgcolor="#000000"><div align="center" class="style4"><span class="style1">B.F.2.S. Clone Install Guide </span></div></td>
	  </tr>
	  <tr>
		<td><span class="style7"><span class="style8">B.F.2.S. Clone - Web Interface Configuration</span><br />
		</span>
		  <table width="480" border="0" align="center" bordercolor="#000000">
			<tr>
              <td><span class="style6">Enter your Leaderboard Name <br />
                <input name="title" type="text" value="BF2S Clone" maxlength="255" width="480" />
              </span></td>
            </tr>
            <tr>
              <td nowrap="nowrap">&nbsp;</td>
            </tr>
          </table>
	    <span class="style7">          </span></td>
	  </tr>
	  <tr>
		<td nowrap="nowrap"><span class="style8">B.F.2.S. Clone - Database Configuration 
		  </span>
		  <table width="480" border="0" align="center" bordercolor="#000000">
            <tr>
              <td class="style6">Enter the IP [and port if its not default] (format.: &quot;IP:PORT&quot; or &quot;IP&quot; or localhost) <br />
                <input name="dbip" type="text" value="localhost" maxlength="255" width="480" />              </td>
            </tr>
            <tr>
              <td nowrap="nowrap" class="style6">Enter database name (eg.: bf2stats) <br />
                <input name="dbname" type="text" value="bf2stats" maxlength="255" width="480" />
                <br />
              Enter login name (eg.: bf2) <br />
              <input name="dblogin" type="text" value="bf2" maxlength="255" width="480" />
              <br />
              Enter password (eg.: bf2stats) <br />
              <input name="dbpassword" type="password" value="mybf2" maxlength="255" width="480" /></td>
            </tr>
		  </table>
		  </td>
	  </tr>
	  <tr>
		<td nowrap="nowrap"><div align="center">
		  <input type="submit" name="Submit" value="install" />
		</div></td>
	  </tr>
	</table>
	<p>&nbsp;</p>
	</form>';
}
else
{
	// Include our functions file
	include_once(__DIR__ . '/functions.inc.php');
	
	// delete cache files, otherwise links wont work
	deleteCompleteCache();
	
	// patch config.inc.php
	$config = file_get_contents( TEMPLATE_PATH . 'config.inc.php.template' );
	
	// set database settings...
	$config = str_replace('{:DBIP:}', $DBIP, $config);
	$config = str_replace('{:DBNAME:}', $DBNAME, $config);
	$config = str_replace('{:DBLOGIN:}', $DBLOGIN, $config);
	$config = str_replace('{:DBPASSWORD:}', $DBPASSWORD, $config);
	
	// Set title
	$config = str_replace('{:TITLE:}', $TITLE, $config);

	// Place the contents in the config file
	file_put_contents( ROOT . DS .'config.inc.php', $config);
	echo 'Thanks for using this installer<br /><br />By the way... its done ;) ... You can edit some config options be editing the the config.inc.php file';
}
?>