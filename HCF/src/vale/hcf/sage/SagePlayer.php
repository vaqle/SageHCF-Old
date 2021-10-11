<?php


namespace vale\hcf\sage;

use pocketmine\block\{
	Block, BlockIds};
use pocketmine\entity\{
	Effect, EffectInstance, Entity
};
use pocketmine\item\Item;
use pocketmine\level\particle\DustParticle;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use vale\hcf\sage\tasks\sql\{
	SaveItTask, LoadItTask
};
use pocketmine\network\mcpe\protocol\{
	AddEntityPacket, UpdateBlockPacket
};
use pocketmine\Player;
use pocketmine\utils\{
	Config, TextFormat
};
use vale\hcf\sage\factions\FactionsManager;
use vale\hcf\sage\factions\tasks\CheckClaimTask;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\libaries\FloatingTextParticle;
use vale\hcf\sage\waypoints\{WaypointManager, Waypoint};
class SagePlayer extends Player
{
	const N = "N";
	const NE = "NE";
	const SE = "SE";
	const S = "S";
	const E = "E";
	const W = "W";
	const NW = "NW";
	const SW = "SW";
	/** @var string $class */
	public $class = "";
	const PUBLIC = 0;
	const FACTION = 1;
	const STAFF = 2;
	public $bardenergy = 0;
	public $invited = false;
	private $lastinvite;
	private $barddelay = 0;
	private $teleporttype;
	private $isteleporting = false;
	private $archertagged = false;
	private $archertagtime = 0;
	private $focusmode = "";
	private $teleporttask;
	private $claiming = false;
	private $pos1;
	private $pos2;
	/** @var bool */
	private $showWayPoint = true;
	/** @var FloatingTextParticle[] */
	private $floatingTexts = [];
	private $task;
	private $step = self::FIRST;
	private $last;
	private $claim = [
		"cost" => 0,
		"claim" => false
	];
	private $spawntag = false;
	/** @var Int */
	protected $combatTagTime = 0;
	public $isVaished = false;

	private $disablemovement;
	private $region = "";
	private $wayPoints = [];
	private $focusing = "";
	const HOME = 1;
	const STUCK = 2;
	const FIRST = 0;
	/** @var Int */
	protected $enderPearlTime = 0;

	const SECOND = 1;
	const CONFIRM = 3;
	/** @var string $oldClass */
	public $oldclass = "";
	private $chat = self::PUBLIC;
	/** @var int */
	public $hits = 0;
	public $time;

	public $isFrozen = false;

	public $hasStaff = false;
	public $oldinv = [];
	public $oldArmor = [];

	/** @var bool */
	private $voteChecking = false;

	/** @var bool */
	private $voted = false;

	public $playerTag = "";

	protected $combatTag = false;

	public $lasthit = "";

	/** @var bool */
	protected $enderPearl = false;

	public int $cps = 0;

	public $lastPostion;



	public function getLastPosition(){
		return $this->lastPostion;
	}


	public function setLastPostion(string $x, string $y, string $z, string $level){
		$this->lastPostion = new Vector3($x,$y,$z);
	}


	/**
	 * @return bool
	 */
	public function isEnderPearl(): bool
	{
		return $this->enderPearl;
	}

	public function addPerm(string $permission): void{
		if(!$this->hasPermission($permission)){
			$this->addAttachment(Sage::getInstance(), $permission, true);
		}
	}


	public function hasRank(string $rank): bool{
		if(DataProvider::$rankprovider->get($this->getName()) == $rank){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * @param bool $enderPearl
	 */
	public function setEnderPearl(bool $enderPearl)
	{
		$this->enderPearl = $enderPearl;
	}

	/**
	 * @param Int $enderPearlTime
	 */
	public function setEnderPearlTime(int $enderPearlTime)
	{
		$this->enderPearlTime = $enderPearlTime;
	}

	/**
	 * @return Int
	 */
	public function getEnderPearlTime(): int
	{
		return $this->enderPearlTime;
	}

	/**
	 * @return string
	 */
	public function getLastHit(): string
	{
		return $this->lasthit;
	}

	/**
	 * @param string $name
	 */
	public function setLastHit(string $name)
	{
		$this->lasthit = $name;
	}

	public function isFrozen(): bool
	{
		return $this->isFrozen;
	}

	public function setFrozen(bool $yorn)
	{
		$this->isFrozen = $yorn;
	}

	public function isVanished(): bool
	{
		return $this->isVaished;
	}

	public function setHasVanished(bool $yorn)
	{
		$this->isVaished = $yorn;
	}

	public function isStaffMode(): bool
	{
		return $this->hasStaff;
	}

	public function setHasStaffMode(bool $yorn)
	{
		$this->hasStaff = $yorn;
	}

	/**
	 * @return bool
	 */
	public function isCombatTagged(): bool
	{
		return $this->combatTag;
	}

	/**
	 * @param bool $combatTag
	 */
	public function setCombatTagged(bool $combatTag)
	{
		$this->combatTag = $combatTag;
	}

	/**
	 * @param Int $combatTagTime
	 */
	public function setCombatTagTime(int $combatTagTime)
	{
		$this->combatTagTime = $combatTagTime;
	}

	/**
	 * @return Int
	 */
	public function getCombatTagTime(): int
	{
		return $this->combatTagTime;
	}

	public function getKills()
	{
		return Sage::getSQLProvider()->getKills($this->getName());
	}

	public function getDeaths()
	{
		return Sage::getSQLProvider()->getDeaths($this->getName());
	}

	public function setPlayerTag($playerTag)
	{
		$this->playerTag = $playerTag;
	}


	public function getPlayerTag(): string
	{
		return $this->playerTag ?? "**";
	}

	public function hasTag()
	{
		return $this->playerTag != null;
	}

	/**
	 * @param bool $value
	 */
	public function setCheckingForVote(bool $value = true): void
	{
		$this->voteChecking = $value;
	}

	/**
	 * @return bool
	 */
	public function isCheckingForVote(): bool
	{
		return $this->voteChecking;
	}

	/**
	 * @return bool
	 */
	public function hasVoted(): bool
	{
		return $this->voted;
	}

	/**
	 * @param bool $value
	 */
	public function setVoted(bool $value = true): void
	{
		$this->voted = $value;
	}

	public function getFacingDirection()
	{
		$yaw = $this->getYaw();
		$direction = ($yaw - 180) % 360;
		if ($direction < 0) $direction += 360;
		if (0 <= $direction && $direction < 22.5) return self::N;
		elseif (22.5 <= $direction && $direction < 67.5) return self::NE;
		elseif (67.5 <= $direction && $direction < 112.5) return self::E;
		elseif (112.5 <= $direction && $direction < 157.5) return self::SE;
		elseif (157.5 <= $direction && $direction < 202.5) return self::S;
		elseif (202.5 <= $direction && $direction < 247.5) return self::SW;
		elseif (247.5 <= $direction && $direction < 292.5) return self::W;
		elseif (292.5 <= $direction && $direction < 337.5) return self::NW;
		elseif (337.5 <= $direction && $direction < 360.0) return self::N;
		else return null;
	}


	/**
	 * @return FloatingTextParticle[]
	 */
	public function getFloatingTexts(): array
	{
		return $this->floatingTexts;
	}

	/**
	 * @param string $identifier
	 *
	 * @return FloatingTextParticle|null
	 */
	public function getFloatingText(string $identifier): ?FloatingTextParticle
	{
		return $this->floatingTexts[$identifier] ?? null;
	}

	/**
	 * @param Position $position
	 * @param string $identifier
	 * @param string $message
	 */
	public function addFloatingText(Position $position, string $identifier, string $message): void
	{
		$floatingText = new FloatingTextParticle($this, $position, $identifier, $message);
		$this->floatingTexts[$identifier] = $floatingText;
		$floatingText->sendChangesTo($this);
	}

	public function removeFloatingText(string $identifier): void
	{
		$floatingText = $this->getFloatingText($identifier);
		if ($floatingText === null) {
			Sage::getInstance()->getLogger()->info("Failed to despawn floating text: $identifier");
		}
		$floatingText->despawn($this);
		unset($this->floatingTexts[$identifier]);
	}





	public function getWayPoint(string $name): ?Waypoint
	{
		return $this->wayPoints[$name] ?? null;
	}

	/**
	 * @param WayPoint $wayPoint
	 */
	public function addWayPoint(Waypoint $wayPoint): void
	{
		$name = $wayPoint->getName();
		$this->wayPoints[$name] = $wayPoint;
		$username = $this->getName();
		$uuid = $this->getRawUniqueId();
		$x = $this->getFloorX();
		$y = $this->getFloorY();
		$z = $this->getFloorZ();
		$level = $this->getLevel()->getName();
		$stmt = Sage::getInstance()->getPlayerDatabase();
		$data = $stmt->prepare("INSERT INTO wayPoints(uuid, username, name, x, y, z, level) VALUES(?, ?, ?, ?, ?, ?, ?)");
		$data->bind_param("sssiiis", $uuid, $username, $name, $x, $y, $z, $level);
		$data->execute();
		$stmt->close();
	}

	/**
	 * @param string $name
	 */
	public function removeWayPoint(string $name): void
	{
		unset($this->wayPoints[$name]);
		$uuid = $this->getRawUniqueId();
		$stmt = Sage::getInstance()->getPlayerDatabase();
		$data = $stmt->prepare("DELETE FROM wayPoints WHERE uuid = ? AND name = ?");
		$data->bind_param("ss", $uuid, $name);
		$data->execute();
		$data->close();
	}

	/**
	 * @return WayPoint[]
	 */
	public function getWayPoints(): array
	{
		return $this->wayPoints;
	}

	/**
	 * @return bool
	 */
	public function isShowingWayPoint(): bool
	{
		return $this->showWayPoint;
	}

	/**
	 * @param bool $value
	 */
	public function setShowWayPoint(bool $value = true): void
	{
		$this->showWayPoint = $value;
	}


	public function disableMovement(int $time)
	{
		$this->disablemovement = $time;
	}

	/**
	 * @return bool
	 */
	public function isMovementDisabled(): bool
	{
		$time = time();

		return ($time - $this->disablemovement) < 0;
	}


	/**
	 * @param string $permission
	 */
	public function addPermission($permission)
	{
		if ($this->hasPermission($permission)) {
			return;
		} else {
			$this->addAttachment(Sage::getInstance(), $permission, true);
			$this->data["permissions"][] = $permission;
		}
	}

	/**
	 * @param string $permission
	 */
	public function removePermission($permission)
	{
		if ($this->hasPermission($permission)) {
			$this->addAttachment(Sage::getInstance(), $permission, false);
			unset($this->data["permissions"][$permission]);
		} else {
			return;
		}
	}

	/**
	 * /**
	 * @param int $hits
	 */
	public function addHits(int $hits)
	{
		$this->setHits($this->getHits() + 1);
	}

	/**
	 * @param int $hits
	 */
	public function setHits(int $hits)
	{
		$this->hits = $hits;
	}

	/**
	 * @return int
	 */
	public function getHits(): int
	{
		return $this->hits;
	}


	/**
	 * @return Int
	 */
	public function getBalance(): int
	{
		return Sage::getSQLProvider()->getBalance($this->getName());
	}


	public function getFocusedFaction(): string
	{
		return $this->focusing;
	}

	public function getFocusedPlayer(): string{
		return $this->focusmode;
	}

	public function setFocusedPlayer(string $focusedPlayer){
		$this->focusmode = $focusedPlayer;
	}


	public function getPartnerKeys(): string {
		return DataProvider::getPartnerKeys($this->getName());
	}

	public function getLives(): string{
		return DataProvider::getLives($this->getName());
	}


	public function getClass(): string
	{
		return $this->class;
	}

	/**
	 * @param string $class
	 */
	public function setClass(string $class)
	{
		$this->setOldClass($this->getClass());
		$this->class = $class;

	}


	public function setFocusing(string $focusing)
	{
		$this->focusing = $focusing;
	}

	/**
	 * @return string
	 */
	public function getOldClass(): string
	{
		return $this->oldclass;
	}

	/**
	 * @param string $old
	 */
	public function setOldClass(string $old)
	{
		$this->oldclass = $old;
	}

	public function isFocusing(): bool
	{
		return $this->focusing != null;
	}

	public function isBard(): bool
	{
		$inv = $this->getArmorInventory();
		$helmet = $inv->getHelmet()->getId();
		$chest = $inv->getChestplate()->getId();
		$legg = $inv->getLeggings()->getId();
		$boots = $inv->getBoots()->getId();
		return ($helmet == 314 && $chest == 315 && $legg == 316 && $boots == 317);
	}


	public function isMiner(): bool
	{
		$inv = $this->getArmorInventory();
		$helmet = $inv->getHelmet()->getId();
		$chest = $inv->getChestplate()->getId();
		$legg = $inv->getLeggings()->getId();
		$boots = $inv->getBoots()->getId();
		return ($helmet == 306 && $chest == 307 && $legg == 308 && $boots == 309);
	}

	public function getTask()
	{
		return $this->task;
	}

	/**
	 * @param mixed $task
	 */
	public function setTask($task)
	{
		$this->task = $task;
	}


	/**
	 * @return int
	 */
	public function getBardEnergy(): int
	{
		return $this->bardenergy;
	}

	/**
	 * @param int @energy;
	 */
	public function setBardEnergy(int $energy)
	{
		$this->bardenergy = $energy;
	}

	/**
	 * @return int
	 */
	public function getBardDelay(): int
	{
		return $this->barddelay;
	}

	/**
	 * @param int $delay
	 */
	public function setBardDelay(int $delay)
	{
		$this->barddelay = $delay;
	}

	public function checkSets(): void
	{
		if ($this->getClass() !== $this->getOldClass()) {
			$this->removeAllEffects();
			$this->setBardEnergy(0);
		}
		if ($this->isBard()) {
			$this->setClass("Bard");
			if (!$this->hasEffect(Effect::SPEED)) $this->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 9999 * 20, 1));
			if (!$this->hasEffect(Effect::REGENERATION)) $this->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 99999 * 20, 0));
			if (!$this->hasEffect(Effect::RESISTANCE)) $this->addEffect(new EffectInstance(Effect::getEffect(Effect::RESISTANCE), 99999 * 20, 0));
			if (!$this->hasEffect(Effect::FIRE_RESISTANCE)) $this->addEffect(new EffectInstance(Effect::getEffect(Effect::FIRE_RESISTANCE), 99999 * 20, 1));
		} elseif ($this->isArcher()) {
			$this->setClass("Archer");
			if (!$this->hasEffect(Effect::SPEED)) $this->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 99999 * 20, 2));
			if (!$this->hasEffect(Effect::REGENERATION)) $this->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 99999 * 20, 0));
			if (!$this->hasEffect(Effect::RESISTANCE)) $this->addEffect(new EffectInstance(Effect::getEffect(Effect::RESISTANCE), 99999 * 20, 1));
		} elseif ($this->isMiner()) {
			$this->setClass("Miner");
			if (!$this->hasEffect(Effect::HASTE)) $this->addEffect(new EffectInstance(Effect::getEffect(Effect::HASTE), 99999 * 20, 1));
			if (!$this->hasEffect(Effect::NIGHT_VISION)) $this->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 99999 * 20, 3));
			if (!$this->hasEffect(Effect::FIRE_RESISTANCE)) $this->addEffect(new EffectInstance(Effect::getEffect(Effect::FIRE_RESISTANCE), 99999 * 20, 1));
			if ($this->getY() < 30) {
				if (!$this->hasEffect(Effect::INVISIBILITY)) $this->addEffect(new EffectInstance(Effect::getEffect(Effect::INVISIBILITY), 5 * 20, 1));
			}
		} else {
			$this->setClass("Normal");
		}
	}

	/**
	 * @return bool
	 */
	public function isArcherTagged(): bool
	{
		return $this->archertagged;
	}

	/**
	 * @param bool $tagged
	 */
	public function setArcherTagged(bool $tagged)
	{
		$this->archertagged = $tagged;
	}

	/**
	 * @param int $time
	 */
	public function setArchertagTime(int $time)
	{
		$this->archertagtime = $time;
	}

	/**
	 * @return int
	 */
	public function getArchertagTime(): int
	{
		return $this->archertagtime;
	}


	public function getCurrentRegion(): string
	{
		if (Sage::getFactionsManager()->isSpawnClaim($this)) {
			return "Spawn";
		} elseif (Sage::getFactionsManager()->isClaim($this)) {
			return Sage::getFactionsManager()->getClaimer($this->x, $this->z) ?? "Wilderness";
		} else {
			return "Wilderness";
		}
	}

	public function getFaction(): string
	{
		$name = $this->getName();
		$result = Sage::getFactionsManager()->getDb()->query("SELECT * FROM players WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return $array["faction"] ?? "None";
	}


	public function promotetoOfficer(string $faction, int $rank = FactionsManager::OFFICER)
	{
		$name = $this->getName();
		Sage::getFactionsManager()->getDb()->exec("INSERT OR REPLACE INTO players (name, rank, faction) VALUES ('$name', " . $rank . ", '$faction');");
	}

	/**
	 * @param string $faction
	 * @param int $rank
	 */
	public function addToFaction(string $faction, int $rank = FactionsManager::MEMBER)
	{
		$name = $this->getName();
		Sage::getFactionsManager()->getDb()->exec("INSERT OR REPLACE INTO players (name, rank, faction) VALUES ('$name', " . $rank . ", '$faction');");
	}

	public function removeFromFaction()
	{
		$name = $this->getName();
		Sage::getFactionsManager()->getDb()->exec("DELETE FROM players WHERE name = '$name';");
	}

	public function isInFaction(): bool
	{
		$name = $this->getName();
		$result = Sage::getFactionsManager()->getDb()->query("SELECT * FROM players WHERE name = '$name';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}

	public function customSetNameTag($nametag, $players)
	{
		$this->sendData($players, [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $nametag]]);
	}

	public function isArcher(): bool
	{
		$inv = $this->getArmorInventory();
		$helmet = $inv->getHelmet()->getId();
		$chest = $inv->getChestplate()->getId();
		$legg = $inv->getLeggings()->getId();
		$boots = $inv->getBoots()->getId();
		return ($helmet == 298 && $chest == 299 && $legg == 300 && $boots == 301);
	}

	public function updateNametag(): void
	{
		if (!$this->isInFaction()) {
			$this->setNameTag(TextFormat::RED . $this->getName());
		}
		foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player) {
			if ($player instanceof SagePlayer) {
				if ($player->getFaction() == $this->getFaction()) {
					$this->customSetNameTag(TextFormat::GREEN . $this->getName(), [$player]);;
					$this->setScoreTag(TextFormat::YELLOW . $this->getFaction());
				} else {
					$this->customSetNameTag(TextFormat::RED . $this->getName(), [$player]);
					$this->setScoreTag(TextFormat::YELLOW . $this->getFaction());

				}
			}
		}
	}

	public function isInClaim(): bool
	{
		$x = $this->getX();
		$z = $this->getZ();
		$result = Sage::getFactionsManager()->getDb()->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2;");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}


	public function isAtSpawn(): bool
	{
		return false;
	}

	/**
	 * @return bool
	 */
	public function isTeleporting(): bool
	{
		return $this->isteleporting;
	}

	/**
	 * @param bool $isteleporting
	 */
	public function setTeleporting(bool $isteleporting)
	{
		$this->isteleporting = $isteleporting;
	}

	/**
	 * @return int
	 */
	public function getTeleport(): int
	{
		return $this->teleporttype;
	}

	/**
	 * @param int $type
	 */
	public function setTeleport(int $type)
	{
		$this->teleporttype = $type;
	}

	/**
	 * @param $task
	 */
	public function setTeleportTask($task)
	{
		$this->teleporttask = $task;
	}

	public function getTeleportTask()
	{
		return $this->teleporttask;
	}

	/**
	 * @return int
	 */
	public function getTeleportTime(): int
	{
		return $this->teleporttask->getTime();
	}

	public function getLastinvite(): string
	{
		return $this->lastinvite;
	}

	/**
	 * @param string $lastinvite
	 */
	public function setLastinvite(string $lastinvite)
	{
		$this->lastinvite = $lastinvite;
	}

	/**
	 * @return bool
	 */
	public function isInvited(): bool
	{
		return $this->invited;
	}

	/**
	 * @param bool $invited
	 */
	public function setInvited(bool $invited)
	{
		$this->invited = $invited;
	}

	/**
	 * @return int
	 */
	public function getStep(): int
	{
		return $this->step;
	}

	/**
	 * @param int $step
	 */
	public function setStep(int $step)
	{
		$this->step = $step;
	}

	public function buildWall(int $x, int $y, int $z)
	{
		for ($i = $y; $i < $y + 20; $i++) {
			$this->setFakeBlock(new Vector3($x, $i, $z), $this->getRandWallBlock());
		}
	}

	public function removeWall(int $x, int $y, int $z)
	{
		for ($i = $y; $i < $y + 20; $i++) {
			$this->setFakeBlock(new Vector3($x, $i, $z), BlockIds::AIR);
		}
	}

	/**
	 * @param Vector3 $pos
	 * @param int $id
	 * @param int $data
	 */
	public function setFakeBlock(Vector3 $pos, int $id, int $data = 0)
	{
		$block = Block::get($id, $data)->setComponents($pos->getX(), $pos->getY(), $pos->getZ())->setLevel($this->getLevel());
		$this->getLevel()->sendBlocks([$this], [$block], UpdateBlockPacket::FLAG_ALL);
	}

	public function getRandWallBlock(): int
	{
		switch (mt_rand(1, 3)) {
			case 1:

				return BlockIds::COAL_BLOCK;
				break;
			case 2:
				return BlockIds::GLASS;
				break;
			case 3:
				return BlockIds::EMERALD_BLOCK;
				break;
		}
		return BlockIds::GLASS;
	}

	/**
	 * @return bool
	 */
	public function getClaim(): bool
	{
		return $this->claim["claim"];
	}

	/**
	 * @param bool $claim
	 */
	public function setClaim(bool $claim)
	{
		$this->claim["claim"] = $claim;
	}

	/**
	 * @return int
	 */
	public function getClaimCost(): int
	{
		return $this->claim["cost"];
	}

	/**
	 * @param int $cost
	 */
	public function setClaimCost(int $cost)
	{
		$this->claim["cost"] = $cost;
	}


	/**
	 * @return string
	 */
	public function getRegion(): string
	{
		return $this->region;
	}

	/**
	 * @param string $region
	 */
	public function setRegion(string $region)
	{
		$this->region = $region;
	}

	/**
	 * @return int
	 */
	public function getChat(): int
	{
		return $this->chat;
	}

	/**
	 * @param int $chat
	 */
	public function setChat(int $chat)
	{
		$this->chat = $chat;
	}

	public function isClaiming(): bool
	{
		return $this->claiming;
	}

	/**
	 * @param bool $claiming
	 */
	public function setClaiming(bool $claiming)
	{
		$this->claiming = $claiming;
	}

	/**
	 *
	 */
	public function checkClaim()
	{
		$dir = Sage::getInstance()->getDataFolder() . "Data.db";
		$pos1 = $this->getPos1();
		$pos2 = $this->getPos2();
		$x1 = min($pos1->getX(), $pos2->getX());
		$z1 = min($pos1->getZ(), $pos2->getZ());
		$x2 = max($pos1->getX(), $pos2->getX());
		$z2 = max($pos1->getZ(), $pos2->getZ());
		Sage::getInstance()->getServer()->getAsyncPool()->submitTask(new CheckClaimTask($x1, $z1, $x2, $z2, $dir, $this->getName()));
	}

	/**
	 * @return Vector3
	 */
	public function getPos1(): Vector3
	{
		return $this->pos1;
	}

	/**
	 * @param Vector3 $pos1
	 */
	public function setPos1(Vector3 $pos1)
	{
		$this->pos1 = $pos1;
	}

	public function getOldArmour(): array{
		return $this->oldArmor;
	}

	/**
	 * @return array
	 */
	public function getOldinv(): array
	{
		return $this->oldinv;
	}

	public function setOldArmour(array $oldarmour){
		$this->oldArmor = $oldarmour;
    }

	/**
	 * @param array $oldinv
	 */
	public function setOldinv(array $oldinv)
	{
		$this->oldinv = $oldinv;
	}

	/**
	 * @return Vector3
	 */
	public function getPos2(): Vector3
	{
		return $this->pos2;
	}

	/**
	 * @param Vector3 $pos2
	 */
	public function setPos2(Vector3 $pos2)
	{
		$this->pos2 = $pos2;
	}

	/**
	 *
	 */
	public function checkLast()
	{
		$x = $this->getPosition()->getFloorX();
		$y = $this->getPosition()->getFloorY();
		$z = $this->getPosition()->getFloorZ();
		$new = $this->getPosition();
		if ($this->getLast() === null)
			return;
		if ($new->distance($this->getLast()) > 1) {
			$this->setLast(new Vector3($x, $y, $z));
		}
	}

	/**
	 * @return null|Vector3
	 */
	public function getLast(): ?Vector3
	{
		return $this->last;
	}

	/**
	 * @param Vector3 $last
	 */
	public function setLast(Vector3 $last)
	{
		$this->last = $last;
	}


	public function lowerPvpTimer(int $time)
	{
		if ($this->getPvpTimer() >= 1) {
			DataProvider::reducePvPTimer($this->getName(), $time);
		}
	}


	public function getPvpTimer()
	{
		return DataProvider::getPvpTimer($this->getName());
	}

	public function hasPvpTimer(): bool
	{
		if (DataProvider::getPvpTimer($this->getName()) >= 1) {
			return true;
		} else {
			return false;
		}
	}


	public function enterStaffMode()
	{

		$this->setHasStaffMode(true);
		$this->setOldinv($this->getInventory()->getContents());
		$this->setOldArmour($this->getArmorInventory()->getContents());
		$this->getInventory()->clearAll();
		$this->getArmorInventory()->clearAll();
		$this->setLastPostion($this->getX(), $this->getY(), $this->getZ(), $this->getLevel()->getName());
		$this->setGamemode(1);
		$helmet = Item::get(Item::MOB_HEAD, 0, 1)->setCustomName("§r§e§lSTAFF MODE");
		$helmet->getNamedTag()->setTag(new ListTag("ench"));
		$this->getArmorInventory()->setHelmet($helmet);
		$this->sendMessage("§r§e(§6§l!§r§e) §r§eYou have succesfully switched to Moderation Mode.");
		$playerInfo = Item::get(Item::BOOK)->setCustomName("§r§6§lPLAYER INFO §r§7(Hit a Player)");
		$playerInfo->setLore([
			'§r§7Hit a player with this to see:',
			'§r§6§l* §r§7a Players Faction Name',
			'§r§e§l* §r§7a Players Balance',
			'§r§6§l* §r§7a Players Kills',
			'§r§e§l* §r§7a Players Deaths',
			'§r§e§l* §r§7a Players Reclaim Status',
		]);
		$playerInfo->getNamedTag()->setTag(new ListTag("ench"));
		$playerInfo->getNamedTag()->setTag(new StringTag("staff_item_pinfo"));

		$invsee = Item::get(Item::CHEST, 0, 1)->setCustomName("§r§e§lInventory Checker §r§7(Hit a Player)");
		$invsee->setLore([
			'§r§7Hit a player to check their §e§lInventories'
		]);
		$invsee->getNamedTag()->setTag(new ListTag("ench"));
		$invsee->getNamedTag()->setTag(new StringTag("staff_item_inv"));

		$randPlayer = Item::get(Item::CLOCK, 0, 1)->setCustomName("§r§6§lTeleport to a Random Player §r§7(Right-Click)");
		$randPlayer->setLore([
			'§r§7Right click to do one of the following options',
			'',
			'',
			'§r§6§l1. §r§7(Left-Click) §6§lTeleports §r§7to closest player.',
			'§r§6§l2. §r§7(Right-Click) §e§lTeleports §r§7to a random player.',
		]);
		$randPlayer->getNamedTag()->setTag(new ListTag("ench"));
		$randPlayer->getNamedTag()->setTag(new StringTag("staff_item_randplayer"));

		$vanish = Item::get(Item::STAINED_GLASS, 5, 1)->setCustomName("§r§e§lVanish §r§7(Right-Click)");
		$vanish->setLore([
			'§r§7Right-Click to do one of the following options',
			'§r§6§l1. §r§7(Left-Click) §6§lDisables §r§7vanish.',
			'§r§6§l2. §r§7(Right-Click) §e§lEnables §r§7Vanish.',
		]);
		$vanish->getNamedTag()->setTag(new ListTag("ench"));
		$vanish->getNamedTag()->setTag(new StringTag("staff_item_vanish"));
		$phase = Item::get(Item::GOLD_HOE, 0, 1)->setCustomName("§r§e§lPhase §r§7(Right-Click)");
		$phase->setLore([
			'§r§7Right Click to §e§lphase §r§7thru objects.'
		]);
		$phase->getNamedTag()->setTag(new ListTag("ench"));
		$phase->getNamedTag()->setTag(new StringTag("staff_item_phase"));

		$freeze = Item::get(Item::PACKED_ICE, 0, 1)->setCustomName("§r§6§lFreeze §r§7(Right-Click)");
		$freeze->setLore([
			'§r§7Hit a player with this to §6§lfreeze §r§7them.'
		]);
		$freeze->getNamedTag()->setTag(new ListTag("ench"));
		$freeze->getNamedTag()->setTag(new StringTag("staff_item_freeze"));
		$this->getInventory()->setItem(0, $playerInfo);
		$this->getInventory()->setItem(1, $invsee);
		$this->getInventory()->setItem(2, $randPlayer);
		$this->getInventory()->setItem(3, $vanish);
		$this->getInventory()->setItem(4, $phase);
		$this->getInventory()->setItem(5, $freeze);
	}

	public function exitStaffMode()
	{
		#$this->teleport($this->getLastPosition());
		$name = $this->getName();
		$this->sendMessage("§r§e(§6§l!§r§e) §r§eYou have succesfully switched to Player Mode.");
		$this->sendMessage("§l§c[!] §r§cSince you were in §c§lModerator §r§cMode we teleported you to your last position “{$name}”\n§r§o§7This applies everytime you exit Moderator Mode.");
		$this->setHasStaffMode(false);
		$this->setGamemode(self::SURVIVAL);
		$this->setHasVanished(false);
		$this->getInventory()->setContents($this->getOldinv());
		$this->getArmorInventory()->setContents($this->getOldArmour());
		$this->setFlying(false);
		$this->setAllowMovementCheats(false);
		$this->keepMovement = false;
	}
}