<?php

namespace vale\hcf\sage\factions;


use kim\present\chunkloader\ChunkLoader;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\level\Level;
use pocketmine\Server;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;
use pocketmine\utils\BinaryStream;
use vale\hcf\sage\factions\tasks\FactionTask;
use pocketmine\block\BlockIds;
use pocketmine\scheduler\Task;
use pocketmine\block\utils\ColorBlockMetaHelper;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use SQLite3;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;


class FactionsManager
{

	const CLAIM = 0;
	const SPAWN = 1;
	const DEATHBAN = 1;
	const PROTECTED = 2;
	const SOUTH_ROAD = 2;
	const EAST_ROAD = 2;
	const NORTH_ROAD = 2;
	const WEST_ROAD = 2;
	const CYBERATTACK = 2;
	const MEMBER = 0;
	const OFFICER = 1;
	const LEADER = 2;
	const CAN_OPEN = 0;

	private $plugin;

	private $db;

	private $frozens = [];


	public function __construct(Sage $plugin)
	{


		$this->setPlugin($plugin);


		$db = new SQLite3($this->getPlugin()->getDataFolder() . "Data.db");


		$this->setDb($db);


		$this->getDb()->exec("CREATE TABLE IF NOT EXISTS claims(faction TEXT PRIMARY KEY, type INT, x1 INT, z1 INT, x2 INT, z2 INT);");
		$this->getDb()->exec("CREATE TABLE IF NOT EXISTS players(name TEXT PRIMARY KEY, rank INT, faction TEXT);");
		$this->getDb()->exec("CREATE TABLE IF NOT EXISTS homes(name TEXT PRIMARY KEY, x INT, y INT, z INT);");
		$this->getDb()->exec("CREATE TABLE IF NOT EXISTS dtrs(name TEXT PRIMARY KEY, dtr FLOAT);");
		$this->getDb()->exec("CREATE TABLE IF NOT EXISTS factioncrystals(name TEXT PRIMARY KEY, crystals INT);");
		$this->getDb()->exec("CREATE TABLE IF NOT EXISTS kothwins(name TEXT PRIMARY KEY, koths INT);");
		$this->getDb()->exec("CREATE TABLE IF NOT EXISTS factionpowers(name TEXT PRIMARY KEY, power INT);");
		$this->getDb()->exec("CREATE TABLE IF NOT EXISTS freeze(name TEXT PRIMARY KEY, dtrfreeze BIGINT);");
		$this->getDb()->exec("CREATE TABLE IF NOT EXISTS balances(name TEXT PRIMARY KEY, balance INT);");


	}

	public function getPlugin(): Sage
	{
		return $this->plugin;
	}

	public function setPlugin($plugin)
	{
		$this->plugin = $plugin;
	}

	public function getDb(): SQLite3
	{

		return $this->db;


	}

	public function setDb(SQLite3 $db)
	{
		$this->db = $db;

	}

	public function createFaction(string $name, SagePlayer $creator)
	{
		if ($this->isFaction($name)) {
			$creator->sendMessage("§r§cThe team {$name} already exists within the Database.");
		} else {
			$creator->addToFaction($name, self::LEADER);
			$this->setCrystals($name, 1);
			$this->setKothWins($name, 0);
			$this->setBalance($name, 0);
			$this->setDTR($name, 1.5);
			$this->setPower($name, 0);
			$this->getPlugin()->getServer()->broadcastMessage("§r§6§l*§e* §r§6{$name} §r§7has been created by §r§6{$creator->getName()}");
		}
	}

	public function isFaction(string $name): bool
	{
		$result = $this->getDb()->query("SELECT * FROM players WHERE faction = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return !empty($array);
	}

	public function setMember(string $name, string $player)
	{
		$this->getDb()->exec("INSERT OR REPLACE INTO players(name, rank, faction) VALUES ('$player', " . self::MEMBER . ", '$name');");
	}


	public function setKothWins(string $name, int $amount)
	{
		$this->getDb()->exec("INSERT OR REPLACE INTO kothwins(name, koths) VALUES ('$name', " . $amount . ");");
	}

	public function setCrystals(string $name, int $amount)
	{
		$this->getDb()->exec("INSERT OR REPLACE INTO factioncrystals(name, crystals) VALUES ('$name', " . $amount . ");");
	}

	public function setOfficer(string $name, string $player)
	{
		Sage::getFactionsManager()->getDb()->exec("INSERT OR REPLACE INTO players(name, rank, faction) VALUES ('$player', " . self::OFFICER . ", '$name');");
	}

	public function isOfficer(string $name, string $player): bool
	{
		return in_array($player, $this->getOfficers($name));
	}

	public function getOfficers(string $name): array
	{
		$members = [];
		$result = Sage::getFactionsManager()->getDb()->query("SELECT * FROM players WHERE faction = '$name' AND rank = 1;");
		while ($array = $result->fetchArray(SQLITE3_ASSOC)) {
			$members[] = $array["name"];
		}
		return $members ?? "None";
	}

	public function setPower(string $name, int $amount)
	{

		$this->getDb()->exec("INSERT OR REPLACE INTO factionpowers(name, power) VALUES ('$name', " . $amount . ");");


	}

	public function setBalance(string $name, int $amount)
	{


		$this->getDb()->exec("INSERT OR REPLACE INTO balances(name, balance) VALUES ('$name', " . $amount . ");");


	}

	public function showMap(SagePlayer $player)
	{
		$x = $player->getFloorX();
		$y = $player->getFloorY();
		$z = $player->getFloorZ();
		$result = $this->getDb()->query("SELECT * FROM claims;");
		$data = 0;
		while ($one = $result->fetchArray(SQLITE3_ASSOC)) {
			$pos1 = new Vector3($one["x1"], $y, $one["z1"]);
			$pos2 = new Vector3($one["x2"], $y, $one["z2"]);
			$pos3 = new Vector3($one["x2"], $y, $one["z1"]);
			$pos4 = new Vector3($one["x1"], $y, $one["z2"]);
			$player->sendMessage("if check statement");
			$name = ColorBlockMetaHelper::getColorFromMeta($data);
			#$player->sendMessage(TextFormat::GRAY . $name . " for " . $one["faction"]);
			for ($i = $player->getLevel()->getHighestBlockAt($pos1->getX(), $pos1->getZ()) + 1; $i < 126; $i++) {
				$b = BlockFactory::get(Block::STAINED_GLASS, 14);
				$pk = new UpdateBlockPacket();
				$pk->blockRuntimeId = $b->getRuntimeId();
				$pk->dataLayerId = UpdateBlockPacket::DATA_LAYER_NORMAL;
				$pk->flags = UpdateBlockPacket::FLAG_ALL;
				$pk->x = (int)$pos2->getX();
				$pk->y = (int)$i;
				$pk->z = (int)$pos2->getZ();
				$player->sendDataPacket($pk);

				$pa = new UpdateBlockPacket();
				$pa->blockRuntimeId = $b->getRuntimeId();
				$pa->dataLayerId = UpdateBlockPacket::DATA_LAYER_NORMAL;
				$pa->flags = UpdateBlockPacket::FLAG_ALL;
				$pa->x = (int)$pos1->getX();
				$pa->y = (int)$i;
				$pa->z = (int)$pos1->getZ();
				$player->sendDataPacket($pa);
				$pf = new UpdateBlockPacket();
				$pf->blockRuntimeId = $b->getRuntimeId();
				$pf->dataLayerId = UpdateBlockPacket::DATA_LAYER_NORMAL;
				$pf->flags = UpdateBlockPacket::FLAG_ALL;
				$pf->x = (int)$pos3->getX();
				$pf->y = (int)$i;
				$pf->z = (int)$pos3->getZ();
				$player->sendDataPacket($pf);
				$p4 = new UpdateBlockPacket();
				$p4->blockRuntimeId = $b->getRuntimeId();
				$p4->dataLayerId = UpdateBlockPacket::DATA_LAYER_NORMAL;
				$p4->flags = UpdateBlockPacket::FLAG_ALL;
				$p4->x = (int)$pos4->getX();
				$p4->y = (int)$i;
				$p4->z = (int)$pos4->getZ();
				$player->sendDataPacket($p4);
			}
		}
	}





	public function isInFaction(string $name): bool {
		$result = Sage::getFactionsManager()->getDb()->query("SELECT * FROM players WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}

	public function getFaction(string $name): string {
		$result = Sage::getFactionsManager()->getDb()->query("SELECT * FROM players WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return $array["faction"] ?? "None";
	}





	/**
	 * @param string $name
	 * @param int $amount
	 */

	/**
	 * @param string $name
	 * @param float $amount
	 */
	public function setDTR(string $name, float $amount)
	{
		$this->getDb()->exec("INSERT OR REPLACE INTO dtrs(name, dtr) VALUES ('$name', " . $amount . ");");
	}

	/**
	 * @param string $name
	 * @param string $player
	 *
	 * @return bool
	 */


	public function isMember(string $name, string $player): bool
	{


		return in_array($player, $this->getMembers($name));


	}


	/**
	 * @param string $name
	 *
	 * @return array
	 */


	public function getMembers(string $name): array
	{


		$members = [];


		$result = $this->getDb()->query("SELECT * FROM players WHERE faction = '$name';");


		while ($array = $result->fetchArray(SQLITE3_ASSOC)) {
			$members[] = $array["name"];
		}
		return $members;
	}


	public function kick(string $name)
	{
		Sage::getFactionsManager()->getDb()->exec("DELETE FROM players WHERE name = '$name';");


	}

	public function reduceDTR(string $name)
	{
		$dtr = $this->getDTR($name);
		if ($dtr <= 1) {
			if ($this->isHome($name)) {
				$home = $this->getFactionHomeLocation($name);
				ChunkLoader::getInstance()->registerChunk($home->getX(), $home->getZ(), "hcfmap");
				Sage::getFactionsManager()->createTileChest($home);
				$this->setDTR($name, $dtr - 1);
				$this->setFrozenTime($name, time() + (60 * 30));
			}
		}
		if ($dtr <= 1) {
			$this->setDTR($name, $dtr - 1);
			$this->setFrozenTime($name, time() + (60 * 30));
		} else {
			$this->setDTR($name, $dtr - 1);
			$this->setFrozenTime($name, time() + (60 * 30));
		}
	}



	public function addDTR(string $name)
	{
		$dtr = $this->getDTR($name);
		if (!$this->isFrozen($name)) {
			if ($dtr < $this->getMaxDTR($name)) {
				if ($this->isHome($name)) {
					$home = $this->getFactionHomeLocation($name);
					$level = Server::getInstance()->getLevelByName("hcfmap");
					ChunkLoader::getInstance()->registerChunk($home->getX(), $home->getZ(), "hcfmap");
					$block = Sage::getInstance()->getServer()->getLevelByName("hcfmap")->getBlock(new Vector3($home->getX(), $home->getY(), $home->getZ()));
					if ($block->getId() === BlockIds::BEACON) {
						$this->setDTR($name, $dtr + 0.5);
						$level->setBlock(new Vector3($home->getX(), $home->getY(), $home->getZ()), Block::get(Block::AIR));

					}
				}
				if (!$this->isFrozen($name)) {
					if ($dtr < $this->getMaxDTR($name)) {
						$this->setDTR($name, $dtr + 0.5);
						foreach ($this->getOnlineMembers($name) as $player) {
							if ($player instanceof SagePlayer) {
								$player->sendMessage("§r§a+0.5 DTR");
							}
						}
					}
				}
			}
		}
	}
	public function getTotalMembers(string $name): array
	{
		$members = [];
		$result = $this->getDb()->query("SELECT * FROM players WHERE faction = '$name';");
		while ($array = $result->fetchArray(SQLITE3_ASSOC)) {
			$members[] = $array["name"];
		}
		return $members;
	}

	/**
	 * @param string $name
	 * @return float
	 */
	public function getMaxDTR(string $name): float
	{
		$total = count($this->getTotalMembers($name));
		if ($total == 4) {
			$max = 4.5;
		} else {
			$max = $total + 1.5;
		}
		return $max;
	}


	/**
	 * @param string $name
	 *
	 * @return int
	 */


	public function getDTR(string $name)
	{
		$result = $this->getDb()->query("SELECT * FROM dtrs WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return $array["dtr"] ?? 1;
	}


	/**
	 * @param string $faction
	 * @param int $time
	 */

	public function setFrozenTime(string $faction, int $time)
	{
		$this->getDb()->exec("INSERT OR REPLACE INTO freeze(name, dtrfreeze) VALUES ('$faction', " . $time . ");");
	}


	public function getOnlineMembers(string $name): array
	{
		$onlines = [];
		$result = $this->getDb()->query("SELECT * FROM players WHERE faction = '$name';");
		while ($array = $result->fetchArray(SQLITE3_ASSOC)) {
			if ($player = $this->getPlugin()->getServer()->getPlayer($array["name"])) {
				$onlines[] = $player;
			}
		}
		return $onlines;
	}

	public function getLeader(string $name): string
	{
		$result = $this->getDb()->query("SELECT * FROM players WHERE faction = '$name' AND rank = 2;");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return strval($array["name"] ?? "None");
	}


	public function setLeader(string $name, string $leader)
	{
		$this->getDb()->exec("INSERT OR REPLACE INTO players(name, rank, faction) VALUES ('$leader', " . self::LEADER . ", '$name');");
	}

	public function addPower(string $name, int $amount)
	{
		$this->setPower($name, $this->getPower($name) + $amount);
	}

	public function addKothWins(string $name, int $amount)
	{
		$this->setKothWins($name, $this->getKothWins($name) + $amount);
	}


	public function addCrystals(string $name, int $amount)
	{
		$this->setCrystals($name, $this->getCrystals($name) + $amount);
	}

	/**
	 * @param string $name
	 * @param int $amount
	 */
	public function addBalance(string $name, int $amount)
	{
		$this->setBalance($name, $this->getBalance($name) + $amount);
	}

	/**
	 * @param string $name
	 *
	 * @return int
	 */
	public function getCrystals(string $name): int
	{
		$result = $this->getDb()->query("SELECT * FROM factioncrystals WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return intval($array["crystals"] ?? 0);
	}

	public function getKothWins(string $name): int
	{
		$result = $this->getDb()->query("SELECT * FROM kothwins WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return intval($array["koths"] ?? 0);
	}

	public function getPower(string $name): int
	{
		$result = $this->getDb()->query("SELECT * FROM factionpowers WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return intval($array["power"]);
	}

	public function getBalance(string $name): int
	{
		$result = $this->getDb()->query("SELECT * FROM balances WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return intval($array["balance"]);
	}

	/**
	 * @param string $name
	 * @param int $amount
	 */
	public function reducePower(string $name, int $amount)
	{
		$this->setPower($name, $this->getPower($name) - $amount);
	}

	public function reduceCrystals(string $name, int $amount)
	{
		$this->setCrystals($name, $this->getCrystals($name) - $amount);
	}

	public function reduceBalance(string $name, int $amount)
	{
		$this->setBalance($name, $this->getBalance($name) - $amount);
	}

	/**
	 * @param string $name
	 */

	public function disbandFaction(string $name)
	{
		$this->getDb()->exec("DELETE FROM players WHERE faction = '$name';");
		$this->getDb()->exec("DELETE FROM homes WHERE name = '$name';");
		$this->getDb()->exec("DELETE FROM balances WHERE name = '$name';");
		$this->getDb()->exec("DELETE FROM factioncrystals WHERE name = '$name';");
		$this->getDb()->exec("DELETE FROM kothwins WHERE name = '$name';");
		$this->getDb()->exec("DELETE FROM factionpowers WHERE name = '$name';");
		$this->getDb()->exec("DELETE FROM dtrs WHERE name = '$name';");
		$this->getDb()->exec("DELETE FROM claims WHERE faction = '$name';");
		$this->getPlugin()->getServer()->broadcastMessage("§r§6§l*§e* §r§6{$name} §r§7has been disbanded.");
		foreach ($this->getOnlineMembers($name) as $member) {
			$member = $this->getPlugin()->getServer()->getPlayer($member);
			$member->removeFromFaction();
		}
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isHome(string $name): bool
	{
		$result = $this->getDb()->query("SELECT * FROM homes WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return !empty($array);
	}




	/**
	 * @param String $factionName
	 * @return Position|null
	 */
	# NOTE: Here we use Position and not Vector3 because Position has the function of level and Vector3 does not
	public  function getFactionHomeLocation(String $factionName) : ?Position {
		$data = $this->getDb()->query("SELECT * FROM homes WHERE name = '$factionName';");
		$result = $data->fetchArray(SQLITE3_ASSOC);
		if(empty($result)){
			return null;
		}
		$level = Sage::getInstance()->getServer()->getLevelByName("hcfmap");
		$level->loadChunk($result["x"], $result["z"]);
		ChunkLoader::getInstance()->registerChunk($result["x"], $result["z"], "hcfmap");
		return new Position($result["x"], $result["y"], $result["z"], $level);
		
	}


	public function getHome(string $name): Vector3
	{
		$result = $this->getDb()->query("SELECT * FROM homes WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		ChunkLoader::getInstance()->registerChunk($array["x"], $array["z"], "hcfmap");
		$level = Server::getInstance()->getLevelByName("hcfmap");
		$level->loadChunk($array["x"], $array["z"]);
		return new Vector3($array["x"], $array["y"], $array["z"]);
	}

	/**
	 * @param string $name
	 * @param Position $pos
	 */


	public function setHome(string $name, Position $pos)
	{
		$this->getDb()->exec("INSERT OR REPLACE INTO homes(name, x, y, z) VALUES ('$name', " . $pos->getFloorX() . ", " . $pos->getFloorY() . ", " . $pos->getFloorZ() . ");");
		ChunkLoader::getInstance()->registerChunk($pos->x, $pos->z, "hcfmap");

	}


	/**
	 * @return array
	 */


	public function getAllFactions(): array
	{
		$result = $this->getDb()->query("SELECT * FROM players;");
		$all = [];
		while ($fac = $result->fetchArray(SQLITE3_ASSOC)) {
			$all[] = $fac["faction"];
		}

		return $all;
	}

	/**
	 * @param string $name
	 * @param Vector3 $pos1
	 * @param Vector3 $pos2
	 * @param int $type
	 */

	public function claim(string $name, Vector3 $pos1, Vector3 $pos2, int $type = self::CLAIM) {
		$x1 = max($pos1->getX(), $pos2->getX());
		$z1 = max($pos1->getZ(), $pos2->getZ());
		$x2 = min($pos1->getX(), $pos2->getX());
		$z2 = min($pos1->getZ(), $pos2->getZ());
		$db = $this->getDb()->prepare("INSERT OR REPLACE INTO claims (faction, type, x1, z1, x2, z2) VALUES (:faction, :type, :x1, :z1, :x2, :z2);");
		$db->bindValue(":faction", $name);
		$db->bindValue(":type", $type);
		$db->bindValue(":x1", $x1);
		$db->bindValue(":z1", $z1);
		$db->bindValue(":x2", $x2);
		$db->bindValue(":z2", $z2);
		$result = $db->execute();
	}

	/**
	 * @param Vector3 $pos
	 *
	 * @return bool
	 */
	public function isClaim(Vector3 $pos) : bool {
		$x = $pos->getX();
		$z = $pos->getZ();
		$result = $this->getDb()->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2;");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}
	/**
	 * @param Vector3 $pos
	 *
	 * @return bool
	 */
	public function isSpawnClaim(Vector3 $pos) : bool {
		$x = $pos->getX();
		$z = $pos->getZ();
		$result = $this->getDb()->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2 AND type = 1;");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}


	/**
	 * @param Vector3 $pos
	 *
	 * @return bool
	 */
	public function isDeathBanClaim(Vector3 $pos) : bool {
		$x = $pos->getX();
		$z = $pos->getZ();
		$result = $this->getDb()->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2 AND type = 2;");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}

	/**
	 * @param Vector3 $pos
	 *
	 * @return bool
	 */
	public function isFactionClaim(Vector3 $pos) : bool {
		$x = $pos->getX();
		$z = $pos->getZ();
		$result = $this->getDb()->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2 AND type = 0;");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		if(empty($array) == false) {
			if($this->getDTR($array["faction"]) <= 0) {
				return false;
			}else {
				return true;
			}
		}else {
			return false;
		}
	}
	/**
	 * @param int $x
	 * @param int $z
	 *
	 * @return string
	 */
	public function getClaimer(int $x, int $z) : ?string {
		$result = $this->getDb()->query("SELECT * FROM claims WHERE {$x} <= x1 AND {$x} >= x2 AND {$z} <= z1 AND {$z} >= z2;");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return $array !== false ? $array["faction"] : null;
	}
	public function isRoad($x, $z) : bool {
		if($x <= 10 and $x >= -10) {
			return true;
		}
		if($z <= 10 and $z >= -10) {
			return true;

		}
		return false;
	}

	/**
	 * @return array|null
	 */
	public function getAllClaims() : ?array {
		$result = $this->getDb()->query("SELECT * FROM claims;");
		$array = [];
		while($one = $result->fetchArray(SQLITE3_ASSOC)) {
			$array[] = $one;
		}
		return $array;
	}

	public function getClaimType(int $x, int $z) : int {
		$result = $this->getDb()->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2;");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return $array["type"];
	}

	public function isFrozen(string $fac): bool
	{
		$time = $this->getFrozenTimeLeft($fac);
		if ($time >= 0) {
			return true;
		} else {
			return false;
		}
	}

	public function getFrozenTimeLeft(string $faction): int
	{
		$time = $this->getFrozenTime($faction) - time();
		return $time;
	}

	public function getFrozenTime(string $faction): int
	{
		$result = $this->getDb()->query("SELECT * FROM freeze WHERE name = '$faction';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return intval($array["dtrfreeze"] ?? 0);
	}

	public function getAllCrystals()
	{
		$data = $this->getAllFactions();
		foreach ($this->getAllFactions() as $faction) {
			$crystals = $this->getCrystals($faction);
			return $crystals;
		}
	}


	public function getTopCrystals():string
	{
		$point_top = $this->getAllCrystals();
		$message = "";
		$toppoint = "§6§lTop Factions With The Most Crystals\n  \n §r§7((Factions WITH THE MOST CRYSTALS)) \n";
		if ($point_top > 0) {
			$i = 0;
			foreach ($this->getAllFactions() as $faction) {
				$crystals = $this->getCrystals($faction);
				if ($i < 10 && $crystals) {
					$message .= "§r§6" . ($i + 1) . ". §r§f" . $faction . " §r§o§7" . $crystals. " §r§6Crystals" . "\n";
					if ($i >= 3) {
						break;
					}
					++$i;
				}
			}

		}
		$return = (string)$toppoint . $message;
		return $return;
	}

	public function sendList()
	{
		$TITLE = "§6§lTop Factions With The Most Crystals\n  \n §r§7((Factions WITH THE MOST CRYSTALS)) \n";
		$message = "";
		$factions = $this->getAllFactions();
		foreach ($factions as $faction){
			$online = $this->getOnlineMembers($faction);
			if (count($online) > 0) {
				$dtr = $this->getDTR($faction);
				$online = count($this->getOnlineMembers($faction));
				$message .= "§r§e§l* §r§f$faction §6§l" . "§r§8(§r§7" . round($dtr). " DTR§r§8)" . " §6§lOnline: §r§f{$online} \n" ;
			}
		}
		return (string)$TITLE . $message;
	}

	public function getFactionTopCrystals(): string
	{
		$tf = "§r§7((TOP FACTIONS)) \n §r§7Below listed are the §6§lTop Factions";
		$result = $this->getDb()->query("SELECT name FROM factioncrystals ORDER BY crystals DESC LIMIT 5;");
		$row = array();
		$i = 0;
		while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
			$faction = $resultArr['name'];
			$crystals = $this->getCrystals($faction);
			$i++;
			return "§r§6" . $i . ". §r§f" . $faction . " §r§o§7" . $crystals . " §r§6Crystals" . "\n";
		}
		return "None";
	}


	public function getFactionTop1(): string
	{
		$tf = "";
		$result = $this->getDb()->query("SELECT name FROM factioncrystals ORDER BY crystals DESC LIMIT 1;");
		$row = array();
		$i = 0;
		while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
			$faction = $resultArr['name'];
			$crystals = $this->getCrystals($faction);
			$i++;
			return "§r§6" . $i . ". §r§f" . $faction . " §r§o§7" . $crystals . " §r§6Crystals" . "\n";
		}
		return "None";
	}

	public function sendListOfTop10FactionsTo($s) {
		$tf = "";
		$result = $this->getDb()->query("SELECT name FROM factioncrystals ORDER BY crystals DESC LIMIT 10;");
		$row = array();
		$i = 0;
		$s->sendMessage("§6§lTop Factions With The Most Crystals\n  \n §r§7((Factions WITH THE MOST CRYSTALS)) \n");
		while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
			$faction = $resultArr['name'];
			$crystals = $this->getCrystals($faction);
			$i++;
			$s->sendMessage("§r§6" . $i . ". §r§f" . $faction . " §r§o§7" . $crystals. " §r§6Crystals" . "\n");
		}
	}



	public function topFactions(SagePlayer $player)
	{
		$factions = $this->getAllFactions();
		$i = 0;
		foreach ($factions as $faction) {
			$crystals = $this->getCrystals($faction);
			if ($i < 5 && $crystals) {
				$i++;
				switch ($i) {
					case 1:
						$player->sendMessage("§6§lTOP Factions");
						$player->sendMessage("§r§7((Factions WITH THE MOST CRYSTALS))");
						$player->sendMessage("§r§7To filter the ftop leaderboards do §6§l/leaderboards");
						$player->sendMessage("§r§7The Filter Types are Listed below:");
						$player->sendMessage("§l§6* §r§b§lCrystals ➰");
						$player->sendMessage("§l§6* §r§c§lKoth Captures ➰");
						$player->sendMessage("§l§6* §r§e§lPoints ➰");
						$player->sendMessage("\n");
						$player->sendMessage("\n");
						$player->sendMessage("§l§e1. §r§7" . $faction . ": §r§6" . $crystals . " Crystals");
						break;
					case 2:
						$player->sendMessage("§l§e2. §r§7" . $faction . ": §6" . $crystals . " Crystals");
						break;
					case 3:
						$player->sendMessage("§l§e3. §r§7" . $faction . ": §6" . $crystals . " Crystals");
						break;
					case 4:
						$player->sendMessage("§l§e4. §r§7" . $faction . ": §6" . $crystals . " Crystals");
						break;
					case 5:
						$player->sendMessage("§l§e5. §r§7" . $faction . ": §6" . $crystals . " Crystals");
						break;

				}
			}
		}
	}

	public function clearPillar(SagePlayer $player, Vector3 $pos): void
	{
		$y = ($lvl = $player->getLevel())->getHighestBlockAt($pos->x, $pos->z);
		if (empty($lvl->getBlockAt($pos->x, $y, $pos->z)->getCollisionBoxes())) {
			$y--;
		}
		$v3s = [];
		for (; $y <= Level::Y_MAX; $y++) {
			$v3s[] = new Vector3($pos->x, $y, $pos->z);
		}
		$lvl->sendBlocks([$player], $v3s);
	}

	public function createTileChest(Vector3 $position)
	{
		/** @var Tile Chest */
		$chest = Tile::createTile("Chest", Sage::getInstance()->getServer()->getLevelByName("hcfmap"), Chest::createNBT($position));
		$level = Sage::getInstance()->getServer()->getLevelByName("hcfmap");
		$level->loadChunk($chest->getX(), $chest->getZ());
		/** @var Vector3 :x|:y|:z */
		$level = Sage::getInstance()->getServer()->getLevelByName("hcfmap");
		$level->loadChunk($chest->getX(), $chest->getY(), $chest->getZ());
		Sage::getInstance()->getServer()->getLevelByName("hcfmap")->setBlock(new Vector3($chest->getX(), $chest->getY(), $chest->getZ()), Block::get(Block::BEACON));
		ChunkLoader::getInstance()->registerChunk($chest->getX(), $chest->getZ(), "hcfmap");
		return $chest;
	}

	public function createBeacon(Vector3 $position)
	{
		/** @var Tile Chest */
		$chest = Tile::createTile("Chest", Sage::getInstance()->getServer()->getLevelByName("hcfmap"), Chest::createNBT($position));
		/** @var Vector3 :x|:y|:z */
		ChunkLoader::getInstance()->registerChunk($position->getX(), $position->getZ(), "hcfmap");
		Sage::getInstance()->getServer()->getLevelByName("hcfmap")->setBlock(new Vector3($chest->getX(), $chest->getY(), $chest->getZ()), Block::get(Block::MELON_BLOCK));
		ChunkLoader::getInstance()->registerChunk($chest->getX(), $chest->getZ(), "hcfmap");
		return $chest;
	}

}