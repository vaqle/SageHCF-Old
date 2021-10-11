<?php

namespace vale\hcf\sage\handlers\events;

use libs\utils\fireworks\Fireworks;
use libs\utils\Utils;
use pocketmine\block\Fence;
use pocketmine\block\FenceGate;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\level\Position;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Server;
use vale\hcf\sage\partneritems\PItemManager;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\cooldowns\Cooldown;
use vale\hcf\sage\system\messages\Messages;
use vale\hcf\sage\tasks\player\AirDropTask;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\entity\projectile\SplashPotion;
use vale\hcf\sage\tasks\player\PearlingTask;

class ItemsHandler implements Listener
{

	public $plugin;
	public static $level;
	public static $x;
	public static $y;
	public static $z;
	public static $cd = [];

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	public function onHit(ProjectileHitBlockEvent $event)
	{
		$projectile = $event->getEntity();
		if (!$projectile instanceof SplashPotion) return;
		if ($projectile->getPotionId() !== 22) return;
		$player = $projectile->getOwningEntity();
		if (!$player) return;
		$distance = $projectile->distance($player);
		if ($player instanceof SagePlayer && $distance <= 3 or 4 or 5 && $player->isAlive())
			$player->setHealth($player->getHealth() + 5);
	}


	public function onInteracted(PlayerInteractEvent $event)
	{
		$player = $event->getPlayer();
		$hand = $player->getInventory()->getItemInHand();
		$nbt = $hand->getNamedTag();
		$name = $player->getName();
		$action = $event->getAction();
		if ($player instanceof SagePlayer) {
			if (($action == PlayerInteractEvent::RIGHT_CLICK_AIR || $action == PlayerInteractEvent::RIGHT_CLICK_BLOCK)) {
				if ($hand->getCustomName() == "§r§l§dSummer Lootbox §r§7(Right-Click) §f(#0054)" && $hand->getId() == ItemIds::CHEST) {
					Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_DARK_PINK, Fireworks::TYPE_BURST);
					Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_RED, Fireworks::TYPE_BURST);
					$event->setCancelled(true);
					$hand->setCount($hand->getCount() - 1);
					Sage::playSound($player, "mob.wither.break_block");
					$player->getInventory()->setItemInHand($hand);
					PItemManager::giveLootboxItems($player);
					PItemManager::giveLootboxItems($player);
					PItemManager::giveLootboxItems($player);
				}
			}
		}

		if ($player instanceof SagePlayer && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
			$block = $event->getBlock();
			if ($block instanceof FenceGate) {
				if ($player->getInventory()->getItemInHand()->getId() === ItemIds::ENDER_PEARL) {

					$event->setCancelled(false);

				}
			}

			if ($hand->getId() == 401 && $hand->getName() == "§r§l§5Sage Crate Key ALL §r§7(Right-Click)" && ($action == PlayerInteractEvent::RIGHT_CLICK_AIR || $action == PlayerInteractEvent::RIGHT_CLICK_BLOCK)) {
				$event->setCancelled();
				$hand->setCount($hand->getCount() - 1);
				$player->getInventory()->setItemInHand($hand);
				foreach (Server::getInstance()->getOnlinePlayers() as $player) {
					#Sage::playSound($player, "random.toast");
					PItemManager::giveKeys($player, "Sage", 1);
				}
				Server::getInstance()->broadcastMessage("\n§l§e(!) §r§eEveryone online has received a §6Sage key §ethanks to §6$name\n");
			}
		}

		if ($hand->getId() == 401 && $hand->getName() == "§r§l§cAbility Crate Key ALL §r§7(Right-Click)" && ($action == PlayerInteractEvent::RIGHT_CLICK_AIR || $action == PlayerInteractEvent::RIGHT_CLICK_BLOCK)) {
			$event->setCancelled();
			$hand->setCount($hand->getCount() - 1);
			$player->getInventory()->setItemInHand($hand);
			foreach (Server::getInstance()->getOnlinePlayers() as $player) {
				#Sage::playSound($player, "random.toast");
				PItemManager::giveKeys($player, "Ability", 1);
			}
			Server::getInstance()->broadcastMessage("\n§l§e(!) §r§eEveryone online has received a §6Ability key §ethanks to §6$name\n");
		}


			if (($action == PlayerInteractEvent::RIGHT_CLICK_AIR || $action == PlayerInteractEvent::RIGHT_CLICK_BLOCK)) {
				if ($hand->getCustomName() == "§r§l§gSage Lootbox §r§7(Right-Click) §f(#0054)" && $hand->getId() == ItemIds::CHEST) {
					Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_DARK_PINK, Fireworks::TYPE_BURST);
					Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_RED, Fireworks::TYPE_BURST);
					$event->setCancelled(true);
					$hand->setCount($hand->getCount() - 1);
					Sage::playSound($player, "mob.wither.break_block");
					$player->getInventory()->setItemInHand($hand);
					PItemManager::giveLootboxItems($player);
					PItemManager::giveLootboxItems($player);
					PItemManager::giveLootboxItems($player);
					PItemManager::giveLootboxItems($player);
				}
			}
		}

	public function Block(BlockPlaceEvent $event)
	{
		$player = $event->getPlayer();
		$inHand = $player->getInventory()->getItemInHand();
		$block = $event->getBlock();
		$l = $block->getLevel();
		if ($player instanceof SagePlayer) {
			if ($inHand->getCustomName() === "§r§l§f*§b*§f*§r§bAir Drop§r§l§f*§b*§f*" && $inHand->getNamedTag()->hasTag("REWARD_AIRDROP")) {
				if (!$player->isInFaction()) {
					$player->sendMessage("§b§lAIRDROPS");
					$player->sendMessage("§r§7((To use an §r§bAirdrop §r§7you must be in a faction))");
					$player->getLevel()->addSound(new AnvilFallSound($player));
					$event->setCancelled(true);
					return;
				}
				if ($player->getCurrentRegion() != $player->getFaction()) {
					$player->sendMessage("§b§lAIRDROPS");
					$player->sendMessage("§r§7((§r§bAirdrops §r§7can only be placed inside your claim))");
					$player->getLevel()->addSound(new AnvilFallSound($player));
					$event->setCancelled(true);
					return;
				}
				
				
				Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new AirDropTask($player, $l, new Vector3($block->x, $block->y + 1, $block->z), 10, "elite"), 20);
			}
		}
	}
}