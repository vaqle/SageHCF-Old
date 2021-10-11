<?php

namespace vale\hcf\sage;

use Cassandra\Time;
use DateTime;
use pocketmine\entity\{Effect, Entity, EffectInstance, EntityIds};
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\SignPost;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\level\ChunkLoadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerChatEvent,
	PlayerDeathEvent,
	PlayerInteractEvent,
	PlayerJoinEvent,
	PlayerCreationEvent,
	PlayerQuitEvent,
	PlayerMoveEvent};
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\Player;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat AS TE;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\DisconnectPacket;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Server;
use vale\hcf\sage\factions\FactionsManager;
use vale\hcf\sage\floatingtext\TextManager;
use vale\hcf\sage\handlers\events\PlayerListener;
use vale\hcf\sage\models\blocks\Chest;
use vale\hcf\sage\models\util\UtilManager;
use vale\hcf\sage\partneritems\PItemManager;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\provider\MuteProvider;
use vale\hcf\sage\provider\SQLProvider;
use vale\hcf\sage\system\classes\BardClass;
use vale\hcf\sage\system\events\FundEvent;
use vale\hcf\sage\system\events\KillTheKingEvent;
use vale\hcf\sage\system\monthlycrates\util\CrateUtils;
use vale\hcf\sage\system\ranks\RankAPI;
use vale\hcf\sage\tasks\player\BardTask;
use vale\hcf\sage\tasks\player\BarUpdateTask;
use vale\hcf\sage\tasks\player\ScoreBoardTask;
use vale\hcf\sage\models\entitys\TextEntity;
use pocketmine\utils\TextFormat as TF;
use xenialdan\apibossbar\BossBar;
use xenialdan\apibossbar\DiverseBossBar;

class SageListener implements Listener
{

	/** @var array $strCooldown */
	public $strCooldown = [];

	const ELEVATOR_UP = "up", ELEVATOR_DOWN = "down";

	/** @var Sage $plugin */
	public $plugin;

	/** @var bool */
	private $cancel_send = true;


	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	public function onDeath(PlayerDeathEvent $ev)
	{
		$p = $ev->getPlayer();
		if ($p instanceof SagePlayer) {
			$armors = $items = [];

			foreach ($p->getArmorInventory()->getContents() as $slot => $armor) {
				$armors[$slot] = $armor->jsonSerialize();
			}

			foreach ($p->getInventory()->getContents() as $slot => $item) {
				$items[$slot] = $item->jsonSerialize();
			}

			DataProvider::$lastinv->set($p->getName(), [
				"armor" => $armors,
				"items" => $items,
			]);
			DataProvider::$lastinv->save();
		}
	}


	public static function addPermissions(SagePlayer $player, string $rank): void{
		$aesthete = ["hcf.kits.core.builder"];
		$booster = ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "hcf.kits.core.master","hcf.kits.core.builder","fix.cmd"];
		$cupid = ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim","hcf.kits.core.master","hcf.kits.core.builder","fix.cmd"];
		$aegis = ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim","hcf.kits.core.master","hcf.kits.core.builder","fix.cmd"];
		$raven = ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim","fix.cmd", "hcf.kits.core.master","hcf.kits.core.builder"];
		$sage = ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim","fix.cmd", "hcf.kits.core.master","hcf.kits.core.builder"];
		$media = ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim","fix.cmd", "hcf.kits.core.master","hcf.kits.core.builder"];
		$partner = ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim","fix.cmd", "hcf.kits.core.master","hcf.kits.core.builder"];
		$famous = ["hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim","fix.cmd", "hcf.kits.core.master","hcf.kits.core.builder"];
		$trial = ["sage.staff.cmd","hcf.core.seevanished","staff.blacklist","hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "fix.cmd","hcf.kits.core.master","pocketmine.command.teleport","hcf.kits.core.builder"];
		$mod = ["sage.staff.cmd","hcf.core.seevanished","staff.blacklist","hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "hcf.kits.core.master","pocketmine.command.teleport","hcf.kits.core.builder","fix.cmd"];
		$admin = ["core.testpermission","hcf.custom.tag","hcf.kits.core.diamond","hcf.kits.core.bard","hcf.kits.core.miner","core.cmd.reclaim", "hcf.kits.core.master","pocketmine.command.teleport","hcf.kits.core.builder","fix.cmd"];


		switch($rank){
			case "Aesthete":
				foreach($aesthete as $permissions){
					Sage::addPerm($player, $permissions);
				}

				break;
			case "Booster":
				foreach($booster as $permissions){
					Sage::addPerm($player, $permissions);
				}
				break;
			case "Sage":
				foreach($sage as $permissions){
					Sage::addPerm($player, $permissions);
				}
				break;
			case "Cupid":
				foreach($cupid as $permissions){
					Sage::addPerm($player, $permissions);
				}
				break;
			case "Aegis":
				foreach($aegis as $permissions){
					Sage::addPerm($player, $permissions);
				}
				break;
			case "Raven":
				foreach($raven as $permissions){
					Sage::addPerm($player, $permissions);
				}
				break;
			case "Media":
				foreach($media as $permissions){
					Sage::addPerm($player, $permissions);
				}
				break;
			case "Partner":
				foreach($partner as $permissions){
					Sage::addPerm($player, $permissions);
				}
				break;
			case "Famous":
				foreach($famous as $permissions){
					Sage::addPerm($player, $permissions);
				}
				break;
			case "Trial":
				foreach($trial as $permissions){
					Sage::addPerm($player, $permissions);
				}
				break;
			case "Mod":
				foreach($mod as $permissions){
					Sage::addPerm($player, $permissions);
				}
				break;
			case "Admin":
				foreach($admin as $permissions){
					Sage::addPerm($player, $permissions);
				}
	         break;
		}
	}




	public function onSignChangeEvent(SignChangeEvent $event) : void {
		if($event->getLine(0) !== "[Elevator]"){
			return;
		}
		if($event->getLine(1) === "up"||$event->getLine(1) === "Up"||$event->getLine(1) === "down"||$event->getLine(1) === "Down"){
			$event->setLine(0, TE::RED."[elevator]");
			$event->setLine(1, $event->getLine(1));
		}
	}
    
   

	/**
	 * @param PlayerInteractEvent $event
	 * @return void
	 */
	public function onInteractEvent(PlayerInteractEvent $event) : void {
		$player = $event->getPlayer();
		if($player instanceof SagePlayer) {
			$block = $event->getBlock();
			$level = Sage::getInstance()->getServer()->getDefaultLevel();
			$diff = $level->getTileAt($block->getX(), $block->getY(), $block->getZ());
			if ($diff instanceof Sign) {
				if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
					$line = $diff->getText();
					if ($line[0] === TE::RED . "[elevator]") {
						if ($line[1] === "up") {
							$this->teleportToSign($player, new Vector3($block->getX(), $block->getY(), $block->getZ()), $this->getSignText($line[1]));
						} elseif ($line[1] === "down") {
							$this->teleportToSign($player, new Vector3($block->getX(), $block->getY(), $block->getZ()), $this->getSignText($line[1]));
						}
					}
				}
			}
		}
	}

	/**
	 * @param Int $x
	 * @param Int $y
	 * @param Int $z
	 * @return null|Int
	 */
	protected function getTextDown(Int $x, Int $y, Int $z){
		$level = Sage::getInstance()->getServer()->getDefaultLevel();
		for($i = $y - 1; $i >= 0; $i--){
			$pos1 = $level->getBlockAt($x, $i, $z);
			$pos2 = $level->getBlockAt($x, $i + 1, $z);
			if($pos1 instanceof Air && $pos2 instanceof Air){
				return $i;
			}
		}
		return $y;
	}

	/**
	 * @param Int $x
	 * @param Int $y
	 * @param Int $z
	 * @return null|Int
	 */
	protected function getTextUp(Int $x, Int $y, Int $z){
		$level = Sage::getInstance()->getServer()->getDefaultLevel();
		for($i = $y + 1; $i <= 256; $i++){
			$pos1 = $level->getBlockAt($x, $i, $z);
			$pos2 = $level->getBlockAt($x, $i + 1, $z);
			if($pos1 instanceof Air && $pos2 instanceof Air){
				return $i;
			}
		}
		return $y;
	}

	/**
	 * @param Int $x
	 * @param Int $y
	 * @param Int $z
	 * @return bool
	 */
	protected function isSign(String $signType, Int $x, Int $y, Int $z) : bool {
		$default = false;
		$level = Sage::getInstance()->getServer()->getLevelByName("hcfmap");
		if($signType === self::ELEVATOR_UP){
			for($i = $y + 1; $i <= 256; $i++){
				$pos1 = $level->getBlockAt($x, $i, $z);
				$pos2 = $level->getBlockAt($x, $i + 1, $z);
				if($pos1 instanceof Air && $pos2 instanceof Air){
					$default = true;
				}
			}
		}elseif($signType === self::ELEVATOR_DOWN){
			for($i = $y - 1; $i >= 0; $i--){
				$pos1 = $level->getBlockAt($x, $i, $z);
				$pos2 = $level->getBlockAt($x, $i + 1, $z);
				if($pos1 instanceof Air && $pos2 instanceof Air){
					$default = true;
				}
			}
		}
		return $default;
	}

	/**
	 * @param String $singType
	 * @return null|String
	 */
	protected function getSignText(String $signType) : ?String {
		if($signType === "up"){
			return self::ELEVATOR_UP;
		}
		if($signType === "down"){
			return self::ELEVATOR_DOWN;
		}
		return self::ELEVATOR_UP;
	}

	/**
	 * @param SagePlayer $player
	 * @param Vector3 $position
	 * @param String $signType
	 */
	protected function teleportToSign(SagePlayer $player, Vector3 $position, String $signType = self::ELEVATOR_UP){
		if($this->isSign($signType, $position->getX(), $position->getY(), $position->getZ())){
			if($signType === self::ELEVATOR_UP){
				$location = $this->getTextUp($position->getX(), $position->getY(), $position->getZ());
				$player->teleport(new Vector3($position->getX() + 0.5, $location, $position->getZ() + 0.5, $player->getLevel()));
			}elseif($signType === self::ELEVATOR_DOWN){
				$location = $this->getTextDown($position->getX(), $position->getY(), $position->getZ());
				$player->teleport(new Vector3($position->getX() + 0.5, $location, $position->getZ() + 0.5, $player->getLevel()));
			}
		}
	}




	public function whiteListed(DataPacketSendEvent $event)
	{
		$packet = $event->getPacket();
		$player = $event->getPlayer();
		if ($packet instanceof DisconnectPacket && $packet->message === "Server is white-listed") {
			$packet->message = ("§6§lSage §r§7is currently whitelisted. \n §e§lDiscord§r§7: https://discord.gg/bgCqypNwRD \n §e§lBUYCRAFT§r§7: shop.hcf.net");
			foreach (Server::getInstance()->getOnlinePlayers() as $staff) {
				$staff->sendMessage("§e§lUPDATE §r§6{$player->getName()} §r§7tried connecting but §cdisconnected §r§7due to the server being §f§lWHITELISTED.");
			}
		}
	}



	public function onQuit(PlayerQuitEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			$tag = $player->getPlayerTag();
			DataProvider::setData($player->getName(), "tag", $player->getPlayerTag());
			$event->setQuitMessage("");
			if($player->isStaffMode()){
				$player->exitStaffMode();
			}
		}
	}




	public function onLoadChunk(ChunkLoadEvent $event){
		$chunk = $event->getChunk();
		if($event->getLevel()->getName() === "hcfmap"){
			$tiles = $chunk->getTiles();
			foreach($tiles as $tile){
				if($tile instanceof Chest){
				$event->getLevel()->loadChunk($tile->getX(), $tile->getZ());
				}
			}
		}
	}

	public function onChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		if($player instanceof SagePlayer){
			$provider = Sage::getMuteProvider();
			if($provider->isMuted($player->getName())) {
				if ($player->getChat() == SagePlayer::FACTION) {
					return;
				}
					$time = $provider->getMuteTimeLeft($player->getName());
					$player->sendMessage("§r§cYou are currently muted for another " . Sage::secondsToTime($time));
					$player->sendMessage("§r§cYou can purchase a §c§lunmute §r§con our buycraft!");
					$event->setCancelled(true);
				}
			}
		}



	public function onJoin(PlayerJoinEvent $event)
	{
		$player = $event->getPlayer();
		$event->setJoinMessage("");
		if ($player instanceof SagePlayer && !$player->hasPlayedBefore()) {
			UtilManager::sendFirstReclaimForm($player);
			DataProvider::createConfig($player);
			TextManager::start($player);
			Sage::getSQLProvider()->setDeaths($player->getName(),0);
			Sage::getSQLProvider()->setKills($player->getName(),0);
			Sage::getSQLProvider()->setBalance($player->getName(),150);
			$this->plugin->getScheduler()->scheduleRepeatingTask(new ScoreBoardTask($player), 20);
			$player->setPlayerTag("n00b");
			DataProvider::setLives($player->getName(), 5);
			DataProvider::setPvpTimer($player->getName(), 3600);
			DataProvider::setPartnerKeys($player->getName(),1);
			PItemManager::giveKeys($player, "Haze", 4);
			PItemManager::giveKeys($player, "Sage", 2);
			PItemManager::givePartnerPackage($player, 5);
			PItemManager::giveLootBox($player, "Sage",1);
			$block = Item::get(Item::OBSERVER, 1, 1);
			$block->setCustomName("§r§c§lPOTION SPAWNER §r§7(Right-Click)");
			$block->setLore([
				'',
				'',
				'§r§7Place this §c§lSpawner §r§7in your claim to generate unlimited potions!',
				'§r§7The §c§lspawner §r§7goes under a Chest!',
				'',
				'§r§7Pot Spawn Rate: (2) * (3)'
			]);
			$player->getInventory()->addItem($block);
			  $item = Item::get(Item::WRITTEN_BOOK, 0, 1);
        $item->setTitle("§r§6§lGuide");
        $item->setPageText(0, "§r§7Welcome to the §6§lSageHCF §r§7Network \n §r§7Here you can find many features from Java Servers.");
        $item->setPageText(1, "§r§e§lSTORE§r§e: §r§7https://sage-network.tebex.io \n §r§e§lDISCORD§r§e: discord.gg/sagehcf");
        $item->setAuthor("§r§6Staff Team");
        $player->getInventory()->addItem($item);
			CrateUtils::giveMonthlyCrate($player, "june2021", (int)1);
			RankAPI::setRank($player, "Aesthete");
		} elseif ($player instanceof SagePlayer && $player->hasPlayedBefore()) {
			$this->plugin->getScheduler()->scheduleRepeatingTask(new ScoreBoardTask($player), 20);
			TextManager::start($player);
			$fund = DataProvider::getFundData()->get("Fund");
			$total = DataProvider::getFundData()->get("Total");
			$player->sendMessage("§8      §6");
			$player->sendMessage("§8       §6");
			$player->sendMessage("§8      §6§lSageHCF     ");
			$player->sendMessage("§8  §6Welcome to Map §f#1-BETA");
			$player->sendMessage("§8 §b");
			$player->sendMessage("§l§eSTORE: §r§fhttps://bit.ly/sagebuycraft");
			$player->sendMessage("§l§eTWITTER: §r§f@SageMCPENetwork");
			$player->sendMessage("§l§eDISCORD: §r§fdiscord.gg/sagehcf");
			$player->sendMessage("§l§eVOTE: §r§fbit.ly/sagevotemcpe");
			$player->sendMessage("§8       §6");
			$player->sendMessage("§8       §6");
			$player->sendMessage("§r§7Welcome to the §6§lSageHCF §r§7Network, §r§7a friendly HCF Server \n§r§7that eliminates the pay to win feel and implements never seen before features.");
			$player->setPlayerTag(DataProvider::getTag($player->getName()));

			#KillTheKingEvent::setKing($player);
			#Sage::getSQLProvider()->addMoney($player->getName(),100);

		}
	}

	/**
	 * @param PlayerCreationEvent $event
	 */
	public function onCreation(PlayerCreationEvent $event)
	{
		$event->setPlayerClass(SagePlayer::class);
	}

}