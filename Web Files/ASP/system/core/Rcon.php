<?php
/* 
| --------------------------------------------------------------
| BF2 Statistics Admin Util
| --------------------------------------------------------------
| Author:       Steven Wilson 
| Copyright:    Copyright (c) 2012
| License:      GNU GPL v3
| ---------------------------------------------------------------
| Class Rcon
| ---------------------------------------------------------------
| Author: jamie.rfurness@gmail.com
| Modified / Improved By: Wilson212
| Source: http://code.google.com/p/bf2php/
|
*/
class Rcon
{
    private $version;
    private $socket = false;
    public $message = '';

    public function connect($ip, $port, $password, $timeout = 2) 
    {
        // Cant connect? Return 0
        if( !$this->socket = @fsockopen($ip, $port, $errno, $errstr, $timeout) )
        {
            return 0;
        }

        // Read the bf2 rcon version info
        $this->version = $this->read(true);
        
        // If we dont have a password, dont login!
        if($password == null)
        {
            $this->close();
            return -1;
        }

        // Login... If password is incorect, return -1
        $result = $this->query('login '. md5(substr($this->read(true), 17) . $password));
        if($result != 'Authentication successful, rcon ready.')
        {
            $this->message = $result;
            $this->close();
            return -1;
        }
        
        // Success?
        return 1;
    }

    public function get_version() 
    {
        return $this->version;
    }

    protected function query($line, $bare = false)
    {
        // Make sure the socket is open!
        if(!$this->socket) throw new Exception('Not connected to an Rcon socket');
        
        // Write line
        $this->write($line, $bare);
        if(strpos($result = $this->read($bare), 'rcon: unknown command:') === 0)
        {
            $this->message = $result;
            return false;
        }

        return $result;
    }

    protected function write($line, $bare = false)
    {
        @fputs($this->socket, ($bare ? '' : "\x02").$line."\n");
    }

    protected function read($bare = false)
    {
        $delim = $bare ? "\n" : "\x04";
        $buffer = '';
        while(($char = @fgetc($this->socket)) != $delim)
        {
            if($char === false) break;
            $buffer .= $char;
        }

        return trim($buffer);
    }

    public function __destruct()
    {
        $this->close();
    }
    
    public function close()
    {
        if ($this->socket) @fclose($this->socket);
    }
    
/*
| ---------------------------------------------------------------
| Execute Commands
| ---------------------------------------------------------------
|
*/	

    public function get_record_demos_enabled() {
        return trim($this->query('exec sv.getAutoDemoHook'));
    }

    public function send_record_demos_enabled($enabled = true) {
        return trim($this->query('exec sv.setAutoDemoHook '.($enabled ? 1 : 0)));
    }

    public function get_demo_quality() {
        return trim($this->query('exec sv.demoQuality'));
    }

    public function send_demo_quality($quality) {
        return trim($this->query('exec sv.demoQuality '.$quality));
    }

    public function get_kick_voting_enabled() {
        return trim($this->query('exec sv.getVotingEnabled')) == '1';
    }

    public function send_kick_voting_enabled($enabled = true) {
        return trim($this->query('exec sv.setVotingEnabled '.($enabled ? 1 : 0)));
    }

    public function get_kick_voting_time() {
        return trim($this->query('exec sv.getVoteTime'));
    }

    public function send_kick_voting_time($time) {
        return trim($this->query('exec sv.setVoteTime '.$time));
    }

    public function get_kick_voting_minimum() {
        return trim($this->query('exec sv.getMinPlayersForVoting'));
    }

    public function send_kick_voting_minimum($min) {
        return trim($this->query('exec sv.setMinPlayersForVoting '.$min));
    }

    public function get_tk_punish_enabled() {
        return trim($this->query('exec sv.getTKPunishEnabled')) == '1';
    }

    public function send_tk_punish_enabled($enabled = true) {
        return trim($this->query('exec sv.setTKPunishEnabled '.($enabled ? 1 : 0)));
    }

    public function get_tk_punish_default() {
        return trim($this->query('exec sv.getTKPunishByDefault')) == '1';
    }

    public function send_tk_punish_default($default = true) {
        return trim($this->query('exec sv.setTKPunishByDefault '.($default ? 1 : 0)));
    }

    public function get_tk_before_kick() {
        return trim($this->query('exec sv.getTKNumPunishToKick'));
    }

    public function send_tk_before_kick($amount) {
        return trim($this->query('exec sv.setTKNumPunishToKick '.$amount));
    }

    public function get_friendly_soldier_fire_damage() {
        return trim($this->query('exec sv.getSoldierFriendlyFire'));
    }

    public function send_friendly_soldier_fire_damage($damage) {
        return trim($this->query('exec sv.setSoldierFriendlyFire '.$damage));
    }

    public function get_friendly_vehicle_fire_damage() {
        return trim($this->query('exec sv.getVehicleFriendlyFire'));
    }

    public function send_friendly_vehicle_fire_damage($damage) {
        return trim($this->query('exec sv.setVehicleFriendlyFire '.$damage));
    }

    public function get_friendly_soldier_splash_damage() {
        return trim($this->query('exec sv.getSoldierSplashFriendlyFire'));
    }

    public function send_friendly_soldier_splash_damage($damage) {
        return trim($this->query('exec sv.setSoldierSplashFriendlyFire '.$damage));
    }

    public function get_friendly_vehicle_splash_damage() {
        return trim($this->query('exec sv.getVehicleSplashFriendlyFire'));
    }

    public function send_friendly_vehicle_splash_damage($damage) {
        return trim($this->query('exec sv.setVehicleSplashFriendlyFire '.$damage));
    }

    public function get_max_players() {
        return trim($this->query('exec sv.maxPlayers'));
    }

    public function get_demo_index() {
        return trim($this->query('exec sv.getDemoIndexURL'));
    }

    public function send_demo_index($url) {
        return trim($this->query('exec sv.setDemoIndexURL "'.$url.'"'));
    }

    public function get_demo_download() {
        return trim($this->query('exec sv.getDemoDownloadURL'));
    }

    public function send_demo_download($url) {
        return trim($this->query('exec sv.setDemoDownloadURL "'.$url.'"'));
    }

    public function get_sponsor_text() {
        return trim($this->query('exec sv.getSponsorText'));
    }

    public function send_sponsor_text($text) {
        return trim($this->query('exec sv.setSponsorText "'.$text.'"'));
    }

    public function get_sponsor_logo() {
        return trim($this->query('exec sv.getSponsorLogoURL'));
    }

    public function send_sponsor_logo($url) {
        return trim($this->query('exec sv.setSponsorLogoURL "'.$url.'"'));
    }

    public function get_community_logo() {
        return trim($this->query('exec sv.CommunityLogoURL'));
    }

    public function send_community_logo($url) {
        return trim($this->query('exec sv.CommunityLogoURL "'.$url.'"'));
    }

    public function get_start_delay() {
        return trim($this->query('exec sv.getStartDelay'));
    }

    public function send_start_delay($delay) {
        return trim($this->query('exec sv.setStartDelay '.$delay));
    }

    public function get_end_delay() {
        return trim($this->query('exec sv.getEndDelay'));
    }

    public function send_end_delay($delay) {
        return trim($this->query('exec sv.setEndDelay '.$delay));
    }

    public function get_restart_delay() {
        return trim($this->query('exec sv.getTimeBeforeRestartMap'));
    }

    public function send_restart_delay($delay) {
        return trim($this->query('exec sv.setTimeBeforeRestartMap '.$delay));
    }

    public function get_server_name() {
        return trim($this->query('exec sv.getServerName'));
    }

    public function send_server_name($name) {
        return trim($this->query('exec sv.setServerName "'.$name.'"'));
    }

    public function get_welcome_message() {
        return str_replace('|', "\n", trim($this->query('exec sv.getWelcomeMessage')));
    }

    public function send_welcome_message($message) {
        return trim($this->query('exec sv.setWelcomeMessage "'.str_replace("\n", '|', $message).'"'));
    }

    public function get_rounds_per_map() {
        return trim($this->query('exec sv.getRoundsPerMap'));
    }

    public function send_rounds_per_map($rounds) {
        return trim($this->query('exec sv.setRoundsPerMap '.$rounds));
    }

    public function send_map_list_reload() {
        return trim($this->query('exec mapList.load'));
    }

    public function send_map_list_remove($id) {
        return trim($this->query('exec mapList.remove '.$id));
    }

    public function send_map_list_add($name, $mode, $size) {
        return trim($this->query('exec mapList.append "'.$name.'" '.$mode.' '.$size));
    }

    public function send_ban_ip_add($ip, $timeout = 'perm') {
        return trim($this->query('exec admin.addAddressToBanList '.$ip.' '.$timeout));
    }

    public function send_ban_key_add($key, $timeout = 'perm') {
        return trim($this->query('exec admin.addKeyToBanList '.$key.' '.$timeout));
    }

    public function send_ban_ip_remove($ip) {
        return trim($this->query('exec admin.removeAddressFromBanList '.$ip));
    }

    public function send_ban_key_remove($key) {
        return trim($this->query('exec admin.removeKeyFromBanList '.$key));
    }

    public function send_kick_player($id) {
        return trim($this->query('exec admin.kickPlayer '.$id));
    }

    public function send_reserved_list_add($name) {
        return trim($this->query('exec reservedSlots.addNick '.$name));
    }

    public function send_reserved_list_remove($name) {
        return trim($this->query('exec reservedSlots.removeNick '.$name));
    }

    public function get_reserved_list() 
    {
        $result = $this->query('exec reservedSlots.list');
        if(!$result) return array();

        return explode("\n", $result);
    }

    public function get_user_list() 
    {
        $result = $this->query('users');
        if(!$result) return array();

        $result = explode("\n", $result);
        array_shift($result);

        $users = array();
        foreach($result as $user) 
        {
            if($start = strpos($user, ': ')) 
            {
                $start += 2;
                $ip = substr($user, $start, strpos($user, ':', $start) - $start);
            }
            else 
            {
                $start = strpos($user, ' ', 2) + 1;
                $ip = substr($user, $start, strpos($user, ' ', $start) - $start);
            }
            $users[] = $ip;
        }

        return $users;
    }

    public function get_map_list() 
    {
        $result = $this->query('exec mapList.list');
        if(!$result) return array();

        $result = explode("\n", $result);

        $maps = array();
        foreach($result as $map) 
        {
            list($id, $name, $mode, $size) = explode(' ', $map);
            $maps[substr($id, 0, -1)] = array(
                'name'		=>	substr($name, 1, -1),
                'mode'		=>	$mode,
                'size'		=>	$size,
            );
        }

        return $maps;
    }

    public function get_current_map() {
        return $this->query('exec admin.currentLevel');
    }

    public function get_next_map() {
        return $this->query('exec admin.nextLevel');
    }

    public function get_banned_address_list() 
    {
        $result = $this->query('exec admin.listBannedAddresses');
        if(!$result) return array();

        $result = explode("\n", $result);

        $bans = array();
        foreach($result as $ban) 
        {
            strtok($ban, ' ');
            $ip = strtok(' ');
            $expires = strtok("\n");

            if(strpos($expires, 'Time Left: ') === 0) $expires = substr($expires, 11);
            $bans[] = array(
                'ip'		=>	$ip,
                'expires'	=>	$expires,
            );
        }

        return $bans;
    }

    public function get_banned_key_list() 
    {
        $result = $this->query('exec admin.listBannedKeys');
        if(!$result) return array();

        $result = explode("\n", $result);

        $bans = array();
        foreach($result as $ban) 
        {
            strtok($ban, ' ');
            $key = strtok(' ');
            $expires = strtok("\n");

            if(strpos($expires, 'Time Left: ') === 0) $expires = substr($expires, 11);
            $bans[] = array(
                'key'		=>	$key,
                'expires'	=>	$expires,
            );
        }

        return $bans;
    }
}