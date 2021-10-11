<?php

namespace vale\hcf\sage\handlers\events;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\nbt\tag\{FloatTag, CompoundTag, DoubleTag, ListTag, ShortTag};
use pocketmine\entity\Entity;
use pocketmine\block\Slab;
use pocketmine\nbt\NBT;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use vale\hcf\sage\partneritems\PItemListener;
use vale\hcf\sage\partneritems\PItemManager;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\system\cooldowns\Cooldown;
use pocketmine\math\Vector3;
use vale\hcf\sage\Sage;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\scheduler\Task;
use pocketmine\block\{Fence, FenceGate};
use pocketmine\level\{Position, Level};
use pocketmine\utils\TextFormat as TF;
use pocketmine\item\{Item, ItemIds};
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\deathban\Deathban;
use vale\hcf\sage\tasks\player\CombatTagTask;

class PlayerListener implements Listener
{

	public static $pearlcd = [];

	public static $chatCooldown = [];

	public $plugin;

	private $blocks = [];

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	public function onDeath(PlayerDeathEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			$cause = $player->getLastDamageCause();
			if ($cause instanceof EntityDamageByEntityEvent) {
				if ($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
					$cause = $cause->getDamager();
					if ($cause instanceof SagePlayer) {
						$level = Sage::getInstance()->getServer()->getLevelByName("deathban");
						$plevel = Sage::getInstance()->getServer()->getLevelByName($player->getLevel()->getName());
						if ($plevel !== $level) {
							#Deathban::setDeathbanned($player, 1000);
							PItemListener::Lightning($player);
							Sage::getSQLProvider()->addDeaths($player->getName(), 1);
							$player->setCombatTagged(false);
							$player->setCombatTagTime(0);
							DataProvider::setPvpTimer($player->getName(),1800);
							Sage::getSQLProvider()->addKills($cause->getName(), 1);
							$dc = DataProvider::getFundData()->get("deathcount");
							DataProvider::$fund->set("deathcount", $dc + 1);
							DataProvider::$fund->save();
							$item = $cause->getInventory()->getItemInHand();
							$name = $item->getName();
							if ($item->hasCustomName()) $name = $item->getCustomName();
							$pname = $player->getName();
							$pkills = Sage::getSQLProvider()->getKills($pname);
							$causen = $cause->getName();
							$causek = Sage::getSQLProvider()->getKills($causen);
							$event->setDeathMessage("§r§c{$pname}§r§4[{$player->getKills()}] §r§ewas slain by {$causen}§r§4[{$causek}] §r§eusing §r§c{$name}§r§e." . " §r§e[#$dc]");
						}
					}
				}


				if ($cause instanceof EntityDamageEvent) {
					$pkills = "§r§4[{$player->getKills()}]";
					if ($cause->getCause() == EntityDamageEvent::CAUSE_PROJECTILE) {
						$event->setDeathMessage(TF::YELLOW . $player->getName() . $pkills . TF::YELLOW . " was bowed to death.");
					}
					if ($cause->getCause() == EntityDamageEvent::CAUSE_SUFFOCATION) {
						$event->setDeathMessage(TF::YELLOW . $player->getName() . $pkills . TF::YELLOW . " suffocated.");
					}
					if ($cause->getCause() == EntityDamageEvent::CAUSE_FALL) {
						$event->setDeathMessage(TF::YELLOW . $player->getName() . $pkills . TF::YELLOW . " thought they had wings");
					}
					if ($cause->getCause() == EntityDamageEvent::CAUSE_FIRE) {
						$event->setDeathMessage(TF::YELLOW . $player->getName() . $pkills . TF::YELLOW . " tried the floor is lava challenge");
					}
					if ($cause->getCause() == EntityDamageEvent::CAUSE_FIRE_TICK) {
						$event->setDeathMessage(TF::YELLOW . $player->getName() . $pkills . TF::YELLOW . " needed to cooldown");
					}
					if ($cause->getCause() == EntityDamageEvent::CAUSE_LAVA) {
						$event->setDeathMessage(TF::YELLOW . $player->getName() . $pkills . TF::YELLOW . " was burnt to a crisp");
					}
					if ($cause->getCause() == EntityDamageEvent::CAUSE_DROWNING) {
						$event->setDeathMessage(TF::YELLOW . $player->getName() . $pkills . TF::YELLOW . " drowned somehow LOL");
					}
					if ($cause->getCause() == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION) {
						$event->setDeathMessage(TF::YELLOW . $player->getName() . $pkills . TF::YELLOW . " had EDP sit on HIM");
					}
					if ($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_EXPLOSION) {
						$event->setDeathMessage(TF::YELLOW . $player->getName() . $pkills . TF::YELLOW . " was blown to pieces");
					}
					if ($cause->getCause() == EntityDamageEvent::CAUSE_VOID) {
						$event->setDeathMessage(TF::YELLOW . $player->getName() . $pkills . TF::YELLOW . " was knocked off into the void");
					}
					if ($cause->getCause() == EntityDamageEvent::CAUSE_MAGIC) {
						$event->setDeathMessage(TF::YELLOW . $player->getName() . $pkills . TF::YELLOW . " died to Harry Potter");
					}
					if ($cause->getCause() == EntityDamageEvent::CAUSE_SUICIDE) {
						$event->setDeathMessage(TF::YELLOW . $player->getName() . $pkills . TF::YELLOW . " commit skooter");
					}
				}
			}
		}
	}

	public function onBreak(BlockBreakEvent $event)
	{
		$block = $event->getBlock();
		$player = $event->getPlayer();
		if ($event->isCancelled()) {
			return;
		}

		if ($block->getId() == BlockIds::DIAMOND_ORE) {
			if (!isset($this->blocks[self::vector3AsString($block->asVector3())])) {
				$count = 0;
				for ($x = $block->getX() - 4; $x <= $block->getX() + 4; $x++) {
					for ($z = $block->getZ() - 4; $z <= $block->getZ() + 4; $z++) {
						for ($y = $block->getY() - 4; $y <= $block->getY() + 4; $y++) {
							if ($player->getLevel()->getBlockIdAt($x, $y, $z) == BlockIds::DIAMOND_ORE) {
								if (!isset($this->blocks[self::vector3AsString(new Vector3($x, $y, $z))])) {
									$this->blocks[self::vector3AsString(new Vector3($x, $y, $z))] = true;
									++$count;
								}
							}
						}
					}
				}
				$this->plugin->getServer()->broadcastMessage("§r§9[FD]§r§7" . $player->getName() . " has found " . $count . " Diamonds.");
			}
		}

	}

	public static function vector3AsString(Vector3 $vector3): string
	{

		return $vector3->getX() . ":" . $vector3->getY() . ":" . $vector3->getZ();

	}

	public function onMove(PlayerMoveEvent $event)
	{
		$mgr = Sage::getFactionsManager();
		$player = $event->getPlayer();
		$x = (int)$player->getX();
		$z = (int)$player->getZ();
		if ($player instanceof SagePlayer) {
			if ($player->isMovementDisabled()) $event->setCancelled(true);
			if ($player->getCombatTagTime() > 0) {
				if ($mgr->isSpawnClaim($event->getTo())) {
					$event->setCancelled(true);
					$player->teleport($event->getFrom());
				}
			}
		}
	}

	public function onEntityDamageEvent(EntityDamageEvent $event): void
	{
		$player = $event->getEntity();
		$mngr = Sage::getFactionsManager();
		if ($event instanceof EntityDamageByEntityEvent) {
			$damager = $event->getDamager();
			if ($event->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK || $event->getCause() === EntityDamageEvent::CAUSE_PROJECTILE) {
				if ($player instanceof SagePlayer && $damager instanceof SagePlayer) {
					if ($player->isTeleporting()) {
						$id = $player->getTeleportTask()->getTaskId();
						Sage::getInstance()->getScheduler()->cancelTask($id);
						$player->setTeleporting(false);
						return;
					}

					if ($mngr->isSpawnClaim($player) || $mngr->isSpawnClaim($damager) || $player->hasPvpTimer() || $damager->hasPvpTimer() || $mngr->isDeathBanClaim($damager) || $mngr->isDeathBanClaim($player)) return;
					if ($player->isCombatTagged()) {
						$player->setCombatTagTime(35);
						$damager->setCombatTagTime(35);
						return;
					}
					if ($player->isInFaction() && $damager->isInFaction() && $player->getFaction() === $damager->getFaction()) return;
					$player->setCombatTagged(true);
					Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new CombatTagTask($player), 20);
					if ($damager->isCombatTagged()) {
						$damager->setCombatTagTime(35);
						$player->setCombatTagTime(35);
						return;
					}
					if ($player->isInFaction() && $damager->isInFaction() && $player->getFaction() === $damager->getFaction()) return;
					$damager->setCombatTagged(true);
					Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new CombatTagTask($damager), 20);
				}
			}
		}
	}






	public function onChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		if($player instanceof SagePlayer){
			$rank = DataProvider::$rankprovider->get($player->getName());
			if(Sage::getMuteProvider()->isMuted($player->getName())){
				return;
			}
			if(!isset(self::$chatCooldown[$player->getName()])) {
				self::$chatCooldown[$player->getName()] = time();
			}else {
				if((time() - self::$chatCooldown[$player->getName()]) < 5 and $rank == "Aesthete" || $rank == "Media" || $rank == "Famous" || $rank == "Partner" ||  $rank == "Booster") {
					$event->setCancelled(true);
					$player->sendMessage("§r§cPublic chat is currently slowed You can only chat every 10 seconds. \n§r§cPlease wait another §c§l" . round(self::$chatCooldown[$player->getName()]  - time()) . "s§r§c.");
					$player->sendMessage("§r§cDefault players can only talk every §r§e10 seconds!  §r§cPurchase a rank at bit.ly/sagebuycraft §r§cto bypass this restriction.");
					return;
				}else {
					self::$chatCooldown[$player->getName()] = time();
				}
			}
			$chatrank = TF::GRAY . $player->getName() . TF::WHITE . ": " . TF::GRAY;
			$tag = $player->getPlayerTag();
			if($rank == "Raven"){
				$chatrank =  "§r§8[§6Raven§r§8]§r§6 " . $player->getName() . " §r§8[§r§e#§f". $tag."§r§8]"  . "§r§f: ";
			} elseif($rank == "Famous"){
				$chatrank = "§r§8[§r§d§oFamous§r§8]§r§d " . $player->getName() . " §r§8[§r§e#§f". $tag."§r§8]"  . "§r§f: ";
			} elseif($rank == "Booster"){
				$chatrank = "§r§8[§d§lNitro§r§d-Booster§r§8]§r§d " . $player->getName() . " §r§8[§r§e#§f". $tag."§r§8]"  . "§r§f: ";
			} elseif($rank == "Admin"){
				$chatrank = "§r§8[§4§lAdmin§r§8]§r§4 " . $player->getName() . " §r§8[§r§e#§f". $tag."§r§8]"  . "§r§f:§r§4 ";
			} elseif($rank == "Aegis"){
				$chatrank = "§r§8[§r§bAegis§r§8]§r§b " . $player->getName() . " §r§8[§r§e#§f". $tag."§r§8]"  . "§r§f: ";
			}elseif ($rank == "Mod"){
				$chatrank = "§r§8[§l§5Mod§r§8] §r§5" . $player->getName() . " §r§8[§r§e#§f". $tag."§r§8]"  . "§r§f:§r§d ";
			} elseif($rank == "Partner"){
				$chatrank = "§r§8[§r§d§lPartner§r§8]§r§d " . $player->getName() . " §r§8[§r§e#§f". $tag."§r§8]"  . "§r§f: ";
			} elseif($rank == "Cupid"){
				$chatrank =  "§r§8[§cCupid§r§8]§r§c ". $player->getName() . " §r§8[§r§e#§f". $tag."§r§8]"  . "§r§f: ";
			} elseif($rank == "Sage") {
				$chatrank = "§r§8§l[§5§lSage§r§8§l]§r§5 " . $player->getName() . " §r§8[§r§e#§f". $tag."§r§8]"  . "§r§f: ";
			}elseif ($rank == "Trial"){
				$chatrank = "§r§8§l[§r§cTrial-Mod§r§8§l]§r§c§l " . $player->getName() . " §r§8[§r§e#§f". $tag."§r§8]"  . "§r§f:§r§c ";
			} elseif($rank == "Media"){
				$chatrank = "§r§8[§r§dMedia§r§8]§r§d " . $player->getName() . " §r§8[§r§e#§f". $tag."§r§8]"  . "§r§f: ";
			} else {
				$chatrank =  "§r§8[§r§fAesthete§r§8]§r§f " . $player->getName() .   "§r§f: ";
			}
			if($player->isInFaction()){
				$fac = $player->getFaction();
				$event->setFormat("§r§6[§r§c" . $fac .  "§r§6] " . $chatrank . $event->getMessage());
			} else {
				$event->setFormat( "§r§6[]" .$chatrank . $event->getMessage());
			}
		}
	}
}
