<?php

namespace vale\hcf\sage\provider;

use SQLite3;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class SQLProvider{

	public SQLite3 $db;

	public Sage $plugin;

	public function __construct(Sage $plugin){
		$this->plugin = $plugin;
		$this->db = new SQLite3($this->plugin->getDataFolder(). "playerdata.db");
		$this->db->exec("CREATE TABLE IF NOT EXISTS playerbalances(name TEXT primary key, money int)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS playerkills(name TEXT primary key, kills int)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS playerdeaths(name TEXT primary key, deaths int)");
	}

	public function addMoney(string $name, int $amount)
	{
		$this->setBalance($name, $this->getBalance($name) + $amount);
	}

	public function setBalance(string $name, int $amount)
	{
		$this->getDatabase()->exec("INSERT OR REPLACE INTO playerbalances(name, money) VALUES ('$name', " . $amount . ");");
	}


	public function reduceBalance(string $name,int $money){
		$this->setBalance($name, $this->getBalance($name) - $money);
	}

	/**
	 * @param string $name
	 *
	 * @return int
	 */
	public function getBalance(string $name): int
	{
		$result = $this->getDatabase()->query("SELECT * FROM playerbalances WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return intval($array["money"] ?? 0);
	}


	public function addKills(string $name, int $amount)
	{
		$this->setKills($name, $this->getKills($name) + $amount);
	}

	public function setKills(string $name, int $amount)
	{
		$this->getDatabase()->exec("INSERT OR REPLACE INTO playerkills(name, kills) VALUES ('$name', " . $amount . ");");
	}


	public function reduceKills(string $name,int $kills){
		$this->setKills($name, $this->getKills($name) - $kills);
	}

	/**
	 * @param string $name
	 *
	 * @return int
	 */
	public function getKills(string $name): int
	{
		$result = $this->getDatabase()->query("SELECT * FROM playerkills WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return intval($array["kills"] ?? 0);
	}

	public function addDeaths(string $name, int $deaths){
		$this->getDatabase()->exec("INSERT OR REPLACE INTO playerdeaths(name, deaths) VALUES ('$name', '$deaths');");
	}

	public function setDeaths(string $name, int $deaths){
		$stmt = $this->db->prepare("INSERT OR REPLACE INTO playerdeaths(name, deaths) VALUES (:name, :deaths);");
		$stmt->bindValue(":name", $name);
		$stmt->bindValue(":deaths", $deaths);
		$stmt->execute();
	}

	public function reduceDeaths(string $name,int $deaths){
		$this->setDeaths($name, $this->getDeaths($name) - $deaths);
	}

	public function getDeaths(string $name): int
	{
		$result = $this->db->query("SELECT * FROM playerdeaths WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return (int)($array["deaths"]) ?? 0;
	}



	public function getTopKills($s) {
		$tf = "";
		$result = $this->getDatabase()->query("SELECT name FROM playerkills ORDER BY kills DESC LIMIT 10;");
		$row = array();
		$i = 0;
		$s->sendMessage("§r§6§l~ Top Players With Most Kills ~\n  \n §r§7((§r§7Listed below are the §6§lthe §r§7Players with the most kills on the Server)) \n");
		while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
			$name = $resultArr['name'];
			$kills = $this->getKills($name);
			$i++;
			$s->sendMessage("§r§6" . $i . ". §r§f" . $name . " §r§o§7" . $kills . " §r§7Kills". "\n");
		}
	}


	public function getTopPersonKills(): string{
		$tf = "";
		$result = $this->getDatabase()->query("SELECT name FROM playerkills ORDER BY kills DESC LIMIT 1;");
		$row = array();
		$i = 0;
		#$s->sendMessage("§r§6§l~ Top Players With Most Kills ~\n  \n §r§7((§r§7Listed below are the §6§lthe §r§7Players with the most kills on the Server)) \n");
		while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
			$name = $resultArr['name'];
			$kills = $this->getKills($name);
			$i++;
			return"§r§6" . $i . ". §r§f" . $name . " §r§o§7" . $kills . " §r§7Kills". "\n";
		}
		return  "None";
	}




	public function sendTopMoney($s) {
		$tf = "";
		$result = $this->getDatabase()->query("SELECT name FROM playerbalances ORDER BY money DESC LIMIT 10;");
		$row = array();
		$i = 0;
		$s->sendMessage("§r§6§l~ Top Richest Individuals ~\n  \n §r§7((§r§7Listed below are the §6§lRichest §r§7Players on the Server)) \n");
		while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
			$name = $resultArr['name'];
			$money = $this->getBalance($name);
			$i++;
			$s->sendMessage("§r§6" . $i . ". §r§f" . $name . " §r§o§7$" . number_format($money,2). "\n");
		}
	}


	public function getDatabase(): SQLite3{
		return $this->db;
	}

}