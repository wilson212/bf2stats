<?php
// Prevent Direct Access
defined('BF2_ADMIN') or die('No Direct Access!');

// Navigation menu builder
function build_navigation()
{
    // Section links
    $task = $_GET['task'];
    $system = array('editconfig','testconfig','installdb','upgradedb','cleardb','backupdb','restoredb');
    $players = array('manageplayers','mergeplayers','importplayer');
    $server = array('serverinfo','mapinfo','validateranks','checkawards','importlogs');
    
    // Prepare for open/closed sections
    $Sys = (in_array($task, $system)) ? true : false;
    $Plyrs = (in_array($task, $players)) ? true : false;
    $Svr = (in_array($task, $server)) ? true : false;
    if(!$Sys && !$Plyrs && !$Svr) $task = 'home';
    
    $html = '
                <li'; if($task == 'home') $html .= ' class="active"'; $html .= '>
                    <a href="?task=home" class="mws-i-24 i-home">Dashboard</a>
                </li>
                <li'; if($Sys == true) $html .= ' class="active"'; $html .= '>
                <a href="#" class="mws-i-24 i-tools">System</a>'; 
                if(DB_VER == '0.0.0') 
                { 
                    $html .= '<ul>
                        <li><a href="?task=editconfig">Edit Configuration</a></li>
                        <li><a href="?task=installdb">Install Database</a></li>
                    </ul>';
                }
                elseif(DB_VER !== CODE_VER)
                {
                    $html .= '<ul>
                        <li><a href="?task=editconfig">Edit Configuration</a></li>
                        <li><a href="?task=installdb">Install Database</a></li>
                        <li><a href="?task=upgradedb">Upgrade Database</a></li>
                        <li><a href="?task=backupdb">Backup Database</a></li>
                    </ul>';
                }
                else
                {
                    $html .= '
                        <ul'; if($Sys == false) $html .= ' class="closed"'; $html .= '>
                            <li><a href="?task=editconfig">Edit Configuration</a></li>
                            <li><a href="?task=testconfig">Test System</a></li>
                            <li><a href="?task=installdb">Install Database</a></li>
                            <li><a href="?task=upgradedb">Upgrade Database</a></li>
                            <li><a href="?task=cleardb">Clear Database</a></li>
                            <li><a href="?task=backupdb">Backup Database</a></li>
                            <li><a href="?task=restoredb">Restore Database</a></li>
                        </ul>
                    </li>
                    <li'; if($Plyrs == true) $html .= ' class="active"'; $html .= '>
                        <a href="#" class="mws-i-24 i-users">Manage Players</a>
                        <ul'; if($Plyrs == false) $html .= ' class="closed"'; $html .= '>
                            <li><a href="?task=manageplayers">Manage Players</a></li>
                            <li><a href="?task=mergeplayers">Merge Players</a></li>
                            <li><a href="?task=importplayer">Import Player From EA</a></li>
                        </ul>
                    </li>
                    <li'; if($Svr == true) $html .= ' class="active"'; $html .= '>
                        <a href="#" class="mws-i-24 i-graph">Server Admin</a>
                        <ul'; if($Svr == false) $html .= ' class="closed"'; $html .= '>
                            <li><a href="?task=serverinfo">Server Info</a></li>
                            <li><a href="?task=mapinfo">Map Info</a></li>
                            <li><a href="?task=validateranks">Validate Ranks</a></li>
                            <li><a href="?task=checkawards">Check Awards</a></li>
                            <li><a href="?task=importlogs">Import Logs</a></li>
                        </ul>
                    </li>';
                }
    $html .= '
                    <li><a href="index.php?action=logout" class="mws-i-24 i-off">Logout</a></li>'. PHP_EOL;
    echo $html;
}