<?php

namespace vale\hcf\sage\provider;

use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\Config;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class DataProvider
{
	public static  $starterkit;
	public static $masterkit;
	public static $diamondkit;
	public static $minerkit;
	public static $bardkit;
	public static $betakit;
	public static $builderkit;
	public static $ranks;
	public static $rankprovider;
	public static $rankperms;
	/** @var  $worlds */
	public  $worlds;
	/** @var  $messages */
	public  $messages;
	public static $permissions;
	public static $cratedata;
	public $directorys = ["kitcooldowns"];
	public static $fund;
	public static $deathbanned;
	public static $potkit;
	public static $lastinv;

	public function initProvider()
	{
		if(!is_dir(Sage::getInstance()->getDataFolder()."players")){
			@mkdir(Sage::getInstance()->getDataFolder()."players");
		}
		if(!is_dir(Sage::getInstance()->getDataFolder()."fund")){
			@mkdir(Sage::getInstance()->getDataFolder()."fund");
		}
		@mkdir(Sage::getInstance()->getDataFolder());
		foreach ($this->directorys as $directory) {
			@mkdir(Sage::getInstance()->getDataFolder() . $directory);
		}
		Sage::getInstance()->saveResource("fund.yml");
		Sage::getInstance()->saveResource("Permissions.yml");
		Sage::$instance->getLogger()->info("Enabling DataProvider");
		self::$lastinv = new Config(Sage::getInstance()->getDataFolder() . "lastinv.json", Config::JSON, []);
		self::$fund = new Config(Sage::getInstance()->getDataFolder().  "fund.yml", Config::YAML);
		self::$cratedata = new Config(Sage::getInstance()->getDataFolder() . "CrateData.yml", Config::YAML);
		self::$masterkit = new Config(Sage::getInstance()->getDataFolder() . "MasterKit.yml", Config::YAML);
		self::$minerkit = new Config(Sage::getInstance()->getDataFolder() . "MinerKit.yml", Config::YAML);
		self::$diamondkit = new Config(Sage::getInstance()->getDataFolder() . "DiamondKit.yml", Config::YAML);
		self::$bardkit = new Config(Sage::getInstance()->getDataFolder() . "BardKit.yml", Config::YAML);
		self::$starterkit = new Config(Sage::getInstance()->getDataFolder() . "StarterKit.yml", Config::YAML);
		self::$builderkit = new Config(Sage::getInstance()->getDataFolder() . "BuilderKit.yml", Config::YAML);
		self::$potkit = new Config(Sage::getInstance()->getDataFolder() . "PotKit.yml", Config::YAML);

		self::$betakit = new Config(Sage::getInstance()->getDataFolder() . "BetaKit.yml", Config::YAML);
		self::$ranks = new Config(Sage::getInstance()->getDataFolder() . "RanksList.yml", Config::YAML);
		self::$rankprovider = new Config(Sage::getInstance()->getDataFolder() . "RankedPlayers.yml", Config::YAML);
		self::$deathbanned = new Config(Sage::getInstance()->getDataFolder() . "DeathBannedPlayers.yml", Config::YAML);

		self::$permissions = new Config(Sage::getInstance()->getDataFolder(). "Permissions.yml", Config::YAML, ["player" => ["core.test"],
			"booster" => ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "hcf.kits.core.master"],
			"cupid" => ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim","hcf.kits.core.master"],
			"aegis" => ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim","hcf.kits.core.master"],
			"raven" => ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "hcf.kits.core.master"],
			"sage" => ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "hcf.kits.core.master"],
			"media" => ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "hcf.kits.core.master"],
			"partner" => ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "hcf.kits.core.master"],
			"famous" => ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "hcf.kits.core.master"],
			"trial" => ["sage.staff.cmd","hcf.core.seevanished","staff.blacklist","hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "hcf.kits.core.master"],
			"mod" => ["sage.staff.cmd","hcf.core.seevanished","staff.blacklist","hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "hcf.kits.core.master"],
			"admin" => ["core.testpermission","hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "hcf.kits.core.master"]]);
		self::$permissions->save();
	}


	public static function getFundData(){
		return self::$fund;
	}

	public function getMasterKitData(){
		return self::$masterkit;
	}


	public static function revive(SagePlayer $p): bool
	{
		if (self::$lastinv->exists($p->getName())) {
			$dat = self::$lastinv->get($p->getName());
			$inv = $p->getArmorInventory();
			foreach ($dat["armor"] as $slot => $serializedArmor) {
				$inv->setItem($slot, Item::jsonDeserialize($serializedArmor));
			}
			$inv->sendContents($inv->getViewers());

			$inv = $p->getInventory();
			foreach ($dat["items"] as $slot => $serializedItem) {
				$inv->setItem($slot, Item::jsonDeserialize($serializedItem));
			}
			$inv->sendContents($inv->getViewers());
			$p->sendMessage("§l§c[!] §r§cAttention “{$p->getName()}”,\n§r§7It appears that we were at fault for a Error. In §r§6§lCompensation §r§7we restored your §r§6§lItems.");
			return true;
		}
		return false;
	}


	public static function getAllMoney(){
		return glob(Sage::getInstance()->getDataFolder(). "players");
	}




	/**
	 * @param Player $player
	 * @return void
	 */
	public static function createConfig(Player $player) : void {
		new Config(Sage::getInstance()->getDataFolder()."players".DIRECTORY_SEPARATOR."{$player->getName()}.yml", Config::YAML, [
			"address" => $player->getAddress(),
			"name" => $player->getName(),
			"cid" => $player->getClientId(),
			"reclaim" => "false",
			"pvptimer" => 30,
			"votecount" => 0,
			"tag" => "#",
			"lives" => 0,
			"partnerkeys" => 1
		]);
	}

	/**
	 * @param String $playerName
	 * @param String $data
	 * @param String $type
	 */
	public static function setData(String $playerName, $data, $type) : void {
		$config = new Config(Sage::getInstance()->getDataFolder()."players".DIRECTORY_SEPARATOR."{$playerName}.yml", Config::YAML);
		$config->set($data, $type);
		$config->save();
	}

	/**
	 * @param String $playerName
	 * @return String|Config
	 */
	public static function getData(String $playerName){
		return new Config(Sage::getInstance()->getDataFolder()."players".DIRECTORY_SEPARATOR."{$playerName}.yml", Config::YAML);
	}

	/**
	 * @param String $playerName
	 * @param String $configType
	 * @param String|Int $config
	 * @return void
	 */
	public static function reset(String $playerName, String $configType, $configSelect) : void {
		self::setData($playerName, $configType, $configSelect);
	}


	public static function getPartnerKeys(string $playerName){
		return self::getData($playerName)->get("partnerkeys");
	}


	public static function getTag(string $playerName){
		return self::getData($playerName)->get("tag");
	}

	/**
	 * @param String $playerName
	 * @return Int|null
	 */
	public static function getVoteCount(String $playerName) : ?Int {
		return self::getData($playerName)->get("votecount");
	}


	/**
	 * @param String $playerName
	 * @return Int|null
	 */
	public static function getLives(String $playerName) : ?Int {
		return self::getData($playerName)->get("lives");
	}



	/**
	 * @param String $playerName
	 * @return Int|null
	 */
	public static function getPvpTimer(String $playerName) : ?Int {
		return self::getData($playerName)->get("pvptimer");
	}



	/**
	 * @param String $playerName
	 * @param Int $votecount
	 * @return void
	 */
	public static function reduceVoteCount(String $playerName, Int $votecount) : void {
		self::setData($playerName, "votecount", self::getVoteCount($playerName) - $votecount);
	}

	/**
	 * @param String $playerName
	 * @param Int $lives
	 * @return void
	 */
	public static function reduceLives(String $playerName, Int $lives) : void {
		self::setData($playerName, "lives", self::getLives($playerName) - $lives);
	}


	/**
	 * @param String $playerName
	 * @param Int $keys
	 * @return void
	 */
	public static function reducePartnerKeys(String $playerName, Int $keys) : void
	{
		self::setData($playerName, "partnerkeys", self::getPartnerKeys($playerName) - $keys);

	}



	public static function reducePvPTimer(String $playerName, Int $time) : void {
		self::setData($playerName, "pvptimer", self::getPvpTimer($playerName) - $time);
	}

		/**
	 * @param String $playerName
	 * @param Int $money
	 * @return void
	 */
	public static function addVotes(String $playerName, Int $votecount) : void {
		self::setData($playerName, "votecount", self::getVoteCount($playerName) + $votecount);
	}


	/**
	 * @param String $playerName
	 * @param Int $lives
	 * @return void
	 */
	public static function addLives(String $playerName, Int $lives) : void {
		self::setData($playerName, "lives", self::getLives($playerName) + $lives);
	}


	public static function addPartnerKeys(String $playerName, Int $keys) : void
	{
		self::setData($playerName, "partnerkeys", self::getPartnerKeys($playerName) + $keys);
	}


	/**
	 * @param String $playerName
	 * @param Int $time
	 */
	public static function addPvpTime(String $playerName, Int $time) : void {
		self::setData($playerName, "pvptimer", self::getPvpTimer($playerName) + $time);
	}


	/**
	 * @param String $playerName
	 * @return bool|mixed
	 */
	public static function getReclaim(String $playerName) {
		return self::getData($playerName)->get("reclaim");
	}

	/**
	 * @param String $playerName
	 * @param string $value
	 */
	public static function setReclaim(String $playerName, string $value) : void {
		self::setData($playerName, "reclaim", $value);
	}


	/**
	 * @param String $playerName
	 * @param Int $lives
	 * @return void
	 */
	public static function setLives(String $playerName, Int $lives) : void {
		self::setData($playerName, "lives", $lives);
	}


	/**
	 * @param String $playerName
	 * @param Int $keys
	 * @return void
	 */
	public static function setPartnerKeys(String $playerName, Int $keys) : void {
		self::setData($playerName, "partnerkeys", $keys);
	}




	public static function setPvpTimer(String $playerName, Int $time) : void {
		self::setData($playerName, "pvptimer", $time);
	}
}

