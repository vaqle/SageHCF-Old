<?php

namespace vale\hcf\sage\provider;

use SQLite3;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class MuteProvider{

	public SQLite3 $db;

	public $plugin;

	public function __construct(Sage $plugin){
		$this->plugin = $plugin;
		$this->db = new SQLite3($this->plugin->getDataFolder(). "mutedplayers.db");
		$this->db->exec("CREATE TABLE IF NOT EXISTS mutedplayers(name TEXT primary key, mutetime int)");
	}

	public function addMute(string $name, int $time){
          $this->getDatabase()->exec("INSERT OR REPLACE INTO mutedplayers(name, mutetime) VALUES ('$name', '$time');");
	}

	public function isMuted(string $name): bool
	{
		$time = $this->getMuteTimeLeft($name);
		if ($time >= 0) {
			return true;
		} else {
			return false;
		}
	}

	public function getMuteTimeLeft(string $name): int
	{
		$time = $this->getMuteTime($name) - time();
		return $time;
	}

	public function getMuteTime(string $name): int
	{
		$result = $this->db->query("SELECT * FROM mutedplayers WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return intval($array["mutetime"] ?? 0);
	}


	public function getDatabase(): SQLite3{
		return $this->db;
	}

}