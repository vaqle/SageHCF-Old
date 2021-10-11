<?php
namespace vale\hcf\sage;

use muqsit\invmenu\{
InvMenu, InvMenuHandler
};
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\plugin\PluginBase;
use vale\hcf\sage\factions\{
FactionsManager
};
use pocketmine\scheduler\Task;
use vale\hcf\sage\items\ItemManager;

use vale\hcf\sage\provider\MuteProvider;
use vale\hcf\sage\provider\SQLProvider;
use vale\hcf\sage\system\deathban\Deathban;
use vale\hcf\sage\system\monthlycrates\MonthlyCrates;
use vale\hcf\sage\system\shop\Shop;
#use vale\hcf\sage\tasks\player\PermissionManager;
use vale\hcf\sage\tasks\player\PotionSpawnerTask;
#use vale\hcf\sage\tasks\sql\PingTask;
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\handlers\EventRegistery;
use vale\hcf\sage\commands\CommandRegistery;
use vale\hcf\sage\tasks\TaskRegistery;
use vale\hcf\sage\models\ModelManager;
use vale\hcf\sage\waypoints\WaypointManager;
use vale\hcf\sage\models\tiles\SessionManager;
use mysqli;
use xenialdan\apibossbar\API;
use xenialdan\apibossbar\DiverseBossBar;
use vale\hcf\sage\tasks\player\BarUpdateTask;

class Sage extends PluginBase
{
	public static $sessionManager;
	public static $instance;
	public static $netherLevel;
	public $provider;
	public static $factionsManager;
	public static $shopManager;
	public $mysqli;
	public static $waypointmanager;
	public static $kitManager;
	public static $overworldLevel = "hcfmap";
	/** @var array $worlds */
	public $worlds = [];
	public $bossbar;
	public static $currentbossbartick;
	public static $deathBanManager;
	public static MuteProvider $muteProvider;
	public static SQLProvider $SQLProvider;


	public function onLoad()
	{
		foreach ($this->worlds as $world) {
			if (!$this->getServer()->isLevelLoaded($world)) {
				$this->getServer()->loadLevel($world);
			}
		}
	}

	public function onEnable()
	{
		if (!InvMenuHandler::isRegistered()) {
			InvMenuHandler::register($this);
		}
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		self::$instance = $this;
		self::$currentbossbartick = 0;
		$this->provider = new DataProvider();
		self::$factionsManager = new FactionsManager($this);
		self::$muteProvider = new MuteProvider($this);
		self::$SQLProvider = new SQLProvider($this);
		self::$kitManager = new KitManager();
		self::$waypointmanager = new WaypointManager($this);
		self::$sessionManager = new SessionManager();
		self::$shopManager = new Shop($this);
		new MonthlyCrates($this);
		EventRegistery::init();
		ModelManager::init();
		ItemManager::init();
		CommandRegistery::init();
		TaskRegistery::init();
		Deathban::initalizeDeathBanArenas();
		new PotionSpawnerTask($this);
		$this->provider->initProvider();
		$this->getServer()->getNetwork()->setName("§r§6§lSage§r§7");
    }



	public static function getSQLProvider(): SQLProvider{
		return self::$SQLProvider;
	}


	public static function getShopManager(): Shop
	{
		return self::$shopManager;
	}

	public static function getMuteProvider(): MuteProvider{
		return self::$muteProvider;
	}


	public function getDataProvider(): DataProvider
	{
		return $this->provider;
	}

	public static function getWayPointManager(): WaypointManager
	{
		return self::$waypointmanager;
	}

	public static function getKitManager(): KitManager
	{
		return self::$kitManager;
	}

	public static function getFactionsManager(): FactionsManager
	{
		return self::$factionsManager;
	}

	public function setPlayerDatabase($database)
	{
		$this->mysqli = $database;
	}

	public function getRankPermissions()
	{
		return DataProvider::$permissions;
	}

	public function getPlayerDatabase(): mysqli
	{
		return $this->mysqli;
	}

	public static function getInstance(): Sage
	{
		return self::$instance;
	}

	public static function addPerm(SagePlayer $player, string $permission): void
	{
		if (!$player->hasPermission($permission)) {
			$player->addAttachment(Sage::getInstance(), $permission, true);
			# $player->sendMessage('§6permission' . "§7 $permission §6" . ' has been added');
		}
	}

	public static function intToFullString(int $time): string
	{
		$hours = null;
		$minutes = null;
		$seconds = floor($time % 60);
		if ($time >= 60) {
			$minutes = floor(($time % 3600) / 60);
			if ($time >= 3600) {
				$hours = floor(($time % (3600 * 24)) / 3600);
			}
		}
		return ($minutes !== null ? ($hours !== null ? ($hours < 10 ? "0" : "") . "$hours" . ":" : "") . ($minutes < 10 ? "0" : "") . "$minutes" . ":" : "") . ($seconds < 10 ? "0" : "") . "$seconds";
	}

	public static function getTimeToFullString(int $time): string
	{
		return gmdate("H:i:s", $time);

	}

	public static function getSessionManager(): SessionManager
	{
		return self::$sessionManager;
	}

	public static function intToString(int $int): string
	{
		$m = floor($int / 60);
		$s = floor($int % 60);
		return (($m < 10 ? "0" : "") . $m . ":" . ($s < 10 ? "0" : "") . $s);
	}

	public static function secondsToTime(int $secs)
	{
#Variables
		$s = $secs % 60;
		$m = floor(($secs % 3600) / 60);
		$h = floor(($secs % 86400) / 3600);
		$d = floor(($secs % 2592000) / 86400);
		$M = floor($secs / 2592000);

		return "$d days $h hours $m minutes $s seconds";
	}

	public static function addItem(SagePlayer $player, Item $item): void{
		# foreach($items as $item){
		#  if($item instanceof Item){
		if(!$player->getInventory()->canAddItem($item)){
			$player->sendMessage("§l§4FULL INVENTORY" . " §r§7your items will fall on the ground");
			//$player->sendMessage("§cyour items will be dropped");
			$player->getLevel()->dropItem($player, $item);
		}else{
			$player->getInventory()->addItem($item);
			self::playSound($player, "random.pop", 1, 0.7);
		}
	}


	public function onDisable()
	{
		$players = $this->getServer()->getOnlinePlayers();
		$logger = $this->getServer()->getLogger();
		$logger->notice("Saving Data.");
        foreach($players as $player){
            $player->transfer("23.158.176.101",19132);
        }
	}


	public static function playSound(Entity $player, string $sound, $volume = 1, $pitch = 1, int $radius = 5): void
	{
		if ($player instanceof SagePlayer) {
			if ($player === null) {
				return;
			}
			if (!$player->isOnline()) {
				return;
			}
			

			if ($player->isOnline()) {
				$spk = new PlaySoundPacket();
				$spk->soundName = $sound;
				$spk->x = $player->getX();
				$spk->y = $player->getY();
				$spk->z = $player->getZ();
				$spk->volume = $volume;
				$spk->pitch = $pitch;
				$player->dataPacket($spk);
			}
		}
	}
}
