<?php

namespace vale\hcf\sage\handlers\events;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\MenuIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\Player;
use pocketmine\Server;
use vale\hcf\sage\system\ranks\RankAPI;
use vale\hcf\sage\{provider\DataProvider, Sage, SagePlayer};

use pocketmine\event\Listener;

class StaffHandler implements Listener
{

	public $plugin;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	public function onDrop(PlayerDropItemEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			if ($player->isStaffMode()) {
				$event->setCancelled(true);
			}
		}
	}

	/**
	 * @param InventoryPickupItemEvent $event
	 */
	public function onPickup(InventoryPickupItemEvent $event)
	{
		if ($event->getInventory() instanceof PlayerInventory) {
			$player = $event->getInventory()->getHolder();
			if ($player instanceof SagePlayer) {
				if ($event->getInventory()->getHolder()->isStaffMode()) {
					$event->setCancelled(true);
				}
			}
		}
	}

	/**
	 * @param BlockBreakEvent $event
	 */
	public function onBreak(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			if ($player->isStaffMode()) {
				$event->setCancelled(true);
			}
		}
	}

	public function onMove(PlayerMoveEvent $event){
		$player = $event->getPlayer();
		if($player instanceof SagePlayer){
			if($player->isFrozen()){
				$event->setCancelled(true);
			}
		}
	}

	/**
	 * @param BlockPlaceEvent $event
	 */
	public function onPlace(BlockPlaceEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			if ($player->isStaffMode()) {
				$event->setCancelled(true);
			}
		}
	}

	public function onItemTransaction(InventoryTransactionEvent $event)
	{
		$player = $event->getTransaction()->getSource();
		if ($player instanceof SagePlayer) {
			if ($player->isStaffMode()) {
				$event->setCancelled(true);
			}
		}
	}
	/**
	 * @param PlayerExhaustEvent $event
	 */
	public function onHunger(PlayerExhaustEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			if ($player->isStaffMode()) {
				$event->setCancelled(true);
			}
		}
	}


	public function onHit(EntityDamageEvent $event)
	{
		$entity = $event->getEntity();
		if ($entity instanceof SagePlayer) {
			if ($entity->isStaffMode()) {
				$event->setCancelled(true);
			}
			if ($event instanceof EntityDamageByEntityEvent) {
				$damager = $event->getDamager();
				if ($damager instanceof SagePlayer) {
					if ($entity instanceof SagePlayer) {
						if ($damager->isStaffMode()) {
							$event->setCancelled(true);
							$item = $damager->getInventory()->getItemInHand();
							$nbt = $item->getNamedTag();
							if ($item->getNamedTag()->hasTag("staff_item_pinfo")) {
								$kills = $entity->getKills();
								$deaths = $entity->getDeaths();
								$reclaim = DataProvider::getReclaim($entity->getName());
								$rank = RankAPI::getRank($entity);
								$damager->sendMessage("§6§l* §r§e§lPLAYER INFO §6§l*");
								$damager->sendMessage("§r§7((§r§7§oIn order to make §r§6§lSageHCF §r§7§oa safe and secure server \n §r§o§7As a staff member You §r§6§lagree §r§o§7to not leak any personal information of this player§r§7➰))");
								$damager->sendMessage("§r§6§l* §r§7Name: §r§e{$entity->getName()}");
								$damager->sendMessage("§r§e§l* §r§7Address: §r§e127.0.0.1");
								$damager->sendMessage("§r§6§l* §r§7CID: §r§e{HASHED}");
								$damager->sendMessage("§r§e§l* §r§7Kills: §r§e{$kills}");
								$damager->sendMessage("§r§6§l* §r§7Deaths: §6{$deaths}");
								$damager->sendMessage("§r§e§l* §r§7Reclaim-status: §e{$reclaim}");
								$damager->sendMessage("§r§6§l* §r§7Rank: §6{$rank}");
							}
							if ($item->getNamedTag()->hasTag("staff_item_inv")) {
								$menu = InvMenu::create(MenuIds::TYPE_DOUBLE_CHEST);
								$menu->readonly(true);
								$menu->setName("§r§6§l" . $entity->getName() . "'§r§7 s Inventory");
								$helmet = $entity->getArmorInventory()->getHelmet();
								$chestplate = $entity->getArmorInventory()->getChestplate();
								$leggings = $entity->getArmorInventory()->getLeggings();
								$boots = $entity->getArmorInventory()->getBoots();
								$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
								$entityInventory = $entity->getInventory()->getContents();
								$menu->getInventory()->setContents($entityInventory);
								$menu->getInventory()->setItem(47, $helmet);
								$menu->getInventory()->setItem(48, $chestplate);
								$menu->getInventory()->setItem(50, $leggings);
								$menu->getInventory()->setItem(51, $boots);
								$menu->send($damager);
								$damager->sendMessage("§r§e(§6§l!§r§e) Checking Inventory.");

							}
							if ($item->getNamedTag()->hasTag("staff_item_freeze")) {
								if (!$entity->isFrozen()) {
									$entity->setFrozen(true);
									$entity->sendMessage("§r§e(§6§l!§r§e) You have been §6§lfrozen.");
									$damager->sendMessage("§r§e(§6§l!§r§e) You succesfully §6§lfroze §r§e{$entity->getName()} .");
								} elseif ($entity->isFrozen()) {
									$entity->setFrozen(false);
									$entity->sendMessage("§r§e(§6§l!§r§e) You have been §6§lunfrozen.");
									$damager->sendMessage("§r§e(§6§l!§r§e) You succesfully §6§lunfroze §r§e{$entity->getName()} .");
								}
							}
						}
					}
				}
			}
		}
	}

	public function onInteract(PlayerInteractEvent $event)
	{
		$player = $event->getPlayer();
		$action = $event->getAction();
		$inHand = $player->getInventory()->getItemInHand();
		$nbt = $inHand->getNamedTag();
		if (!$player instanceof SagePlayer) {
			return;
		}
		if ($player->isStaffMode()) {
			if ($inHand->getNamedTag()->hasTag("staff_item_randplayer") && $action === PlayerInteractEvent::RIGHT_CLICK_AIR) {
				$player->sendMessage("§r§e(§6§l!§r§e) Teleporting you to a random player.");
				$onlinePlayers = [];
				foreach ($player->getServer()->getOnlinePlayers() as $onlinePlayer) {
					$onlinePlayers[] = $onlinePlayer;
				}
				$number = count($onlinePlayers);
				$rand = $onlinePlayers[mt_rand(0, $number - 1)];
				$playerName = $rand->getPlayer()->getName();
				$player->teleport($rand);
			}
			if ($inHand->getNamedTag()->hasTag("staff_item_randplayer") && $action === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
				$player->sendMessage("§r§e(§6§l!§r§e) Teleporting you to the closets player.");
				$closest = $player;
				$lastSquare = -1;
				foreach ($player->getLevel()->getPlayers() as $p) {
					if ($p !== $player) {
						$x = $p->x - $player->x;
						$z = $p->z - $player->z;
						$square = abs($x) + abs($z);
						if ($lastSquare === -1 or $lastSquare > $square) {
							$closest = $p;
							$lastSquare = round($square);
						}
					}
				}
				$player->teleport($closest);
			}

			if($inHand->getNamedTag()->hasTag("staff_item_phase") && $action === PlayerInteractEvent::RIGHT_CLICK_AIR) {
				if ($player->getGamemode() != Player::SPECTATOR) {
					$player->setGamemode(Player::SPECTATOR);
					$player->sendMessage("§r§e(§6§l!§r§e) You have toggled §6§lphase.");
				}elseif ($player->getGamemode() != Player::CREATIVE){
					$player->setGamemode(Player::CREATIVE);
					$player->sendMessage("§r§e(§6§l!§r§e) You have succesfully untoggled §6§lphase.");
				}
			}
			if ($inHand->getNamedTag()->hasTag("staff_item_vanish") && $action === PlayerInteractEvent::RIGHT_CLICK_AIR) {
				if (!$player->isVanished()) {
					$player->setHasVanished(true);
					$player->sendMessage("§r§e(§6§l!§r§e) You are now §6§lvanished.");
					foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $players) {
						if (!$players->hasPermission("hcf.core.seevanished")) {
							$players->hidePlayer($player);
						}
					}
				} else {
					$player->sendMessage("§r§e(§6§l!§r§e) You are no longer §6§lvanished.");
					$player->setHasVanished(false);
					foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $players) {
						$players->showPlayer($event->getPlayer());
					}
				}
			}
		}
	}
}