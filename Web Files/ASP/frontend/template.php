<?php
use System\Database;

// Prevent Direct Access
defined('BF2_ADMIN') || die('No Direct Access!');

use System\IO\Directory;
use System\IO\Path;
use System\Navigation;
use System\NavigationItem;

if (!function_exists('build_navigation'))
{
    // Navigation menu builder
    function build_navigation(): void
    {
        // Get  un-authorized snapshots count
        try
        {
            $ss = count(Directory::GetFiles(Path::Combine(SYSTEM_PATH, "snapshots", "unauthorized"), '.*\.json'));
        }
        catch (Exception)
        {
            // Ignore
            $ss = 0;
        }

        // Define Section controllers for the opening of drop down menus
        $task = $GLOBALS['controller'];
        $system = ['config', 'install', 'database'];
        $players = ['players'];
        $server = ['providers', 'servers', 'snapshots', 'roundinfo'];
        $service = ['service'];
        $statistics = ['mapinfo', 'stats'];
        $game = ['gamedata'];

        // Create navigation class
        $navigation = new Navigation();

        // Add Dashboard link
        $group = new NavigationItem("Dashboard", "/ASP/", "icon-home", $task == 'home');
        $navigation->append($group);

        // Add System Links
        $group = new NavigationItem("System", "#", "icon-cogs", in_array($task, $system));
        $group->append('/ASP/config', 'Edit Configuration');
        $group->append('/ASP/install', 'System Installation');

        // Adjust navigation items based on a few variables
        if (DB_VERSION == '0.0.0') {
            // No database connection? Fine then... no navigation for you!
            $navigation->append($group);
        } elseif (DB_VERSION != DB_EXPECTED_VERSION) {
            // If mis-matched database version, allow these 2 actions
            $group->append('/ASP/database/update', 'Upgrade Database Schema');
            $group->append('/ASP/database/backup', 'Backup Stats Database');
            $navigation->append($group);
        } else
        {
            // Grab database connection
            $pdo = Database::GetConnection('stats');

            // Append the rest of system links
            $group->append('/ASP/config/test', 'System Tests');
            $group->append('/ASP/database', 'Database Table Status');
            $group->append('/ASP/database/update', 'Update Database Schema');
            $group->append('/ASP/database/clear', 'Clear Stats Database');
            $group->append('/ASP/database/backup', 'Backup Stats Database');
            $group->append('/ASP/database/restore', 'Restore Database');
            $navigation->append($group);

            // Add Statistics Links
            $group = new NavigationItem("Maintenance", "#", "icon-tools", in_array($task, $service));
            $group->append('/ASP/service/risingstar', 'Rising Star Leaderboard');
            $group->append('/ASP/service/smoc', 'Sergeant Major of the Corp');
            $group->append('/ASP/service/general', '4-Star General Selection');
            $navigation->append($group);

            // Add Player Links
            $group = new NavigationItem("Player Manager", "/ASP/players", "icon-users", in_array($task, $players));
            $navigation->append($group);

            // Add Server Admin Links
            $snapshots = ($ss > 0) ? '<span class="mws-nav-tooltip" title="Unauthorized Snapshots">' . $ss . '</span>' : '';
            $group = new NavigationItem("Stats Admin" . $snapshots, "#", "icon-business-card", in_array($task, $server));
            $group->append('/ASP/providers', 'Manage Ranked Providers');
            $group->append('/ASP/servers', 'View Registered Servers');
            $group->append('/ASP/snapshots', 'Authorize Snapshots');
            $group->append('/ASP/snapshots/failed', 'Failed Snapshots');
            $group->append('/ASP/roundinfo', 'Round History');
            $navigation->append($group);

            // BattleSpy
            $bsr = (int)$pdo->query("SELECT COUNT(`id`) FROM `battlespy_report`")->fetchColumn(0);
            $title = 'BattleSpy <span class="mws-nav-tooltip" title="Reports">' . $bsr . '</span>';
            $group = new NavigationItem($title, "#", "icon-eye-open", $task == "battlespy");
            $group->append('/ASP/battlespy', 'View Reports');
            $group->append('/ASP/battlespy/config', 'Edit Configuration');
            $navigation->append($group);

            // Add Statistics Links
            $group = new NavigationItem("Global Statistics", "#", "icon-stats", in_array($task, $statistics));
            $group->append('/ASP/stats/armies', 'Army Statistics');
            $group->append('/ASP/stats/kits', 'Kit Statistics');
            $group->append('/ASP/mapinfo', 'Map Statistics');
            $group->append('/ASP/stats/weapons', 'Weapon Statistics');
            $group->append('/ASP/stats/vehicles', 'Vehicle Statistics');
            $navigation->append($group);

            // Add Game Data Links
            $group = new NavigationItem("Game Data", "#", "icon-link", in_array($task, $game));
            $group->append('/ASP/gamedata', 'Manage Stat Keys');
            $group->append('/ASP/gamedata/awards', 'Manage Awards');
            $group->append('/ASP/gamedata/unlocks', 'Manage unlocks');
            $group->append('/ASP/gamedata/mods', 'Manage Game Mods');
            $navigation->append($group);
        }

        // Logout
        $group = new NavigationItem("Logout", "/ASP/index.php?action=logout", "icon-off", false);
        $navigation->append($group);

        echo $navigation->toHtml();
    }
}
