<?php

namespace vale\hcf\sage\handlers\events;

use pocketmine\block\FenceGate;
use pocketmine\item\ItemIds;
use vale\hcf\sage\models\entitys\EnderPearl;
use vale\hcf\sage\models\util\UtilManager;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use pocketmine\block\Block;
use pocketmine\block\Fence;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\Server;
use pocketmine\block\EndPortalFrame;
use pocketmine\block\Chest;
use pocketmine\block\EnderChest;
use pocketmine\block\CobblestoneWall;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\block\Air;
use vale\hcf\sage\tasks\player\PearlingTask;

class EnderPearlHandler implements Listener
{

	public $plugin;


	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * @param PlayerInteractEvent $event
	 * @return void
	 */
	public function onPlayerInteractEvent(PlayerInteractEvent $event): void
	{
		$player = $event->getPlayer();
		$item = $event->getItem();
		$block = $event->getBlock();
		if ($player instanceof SagePlayer) {
			if ($item instanceof \vale\hcf\sage\items\item\EnderPearl && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR) {
				if ($player->isEnderPearl()) {
					#$player->sendMessage("lol" . $player->getEnderPearlTime());
					$event->setCancelled(true);
					return;
				}
				$nbt = UtilManager::createWith($player);
				$entity = Entity::createEntity("EnderPearl", $player->getLevel(), $nbt, $player);
				if ($entity instanceof EnderPearl) {
					$entity->setMotion($entity->getMotion()->multiply($item->getThrowForce()));
					if ($player->isSurvival()) {
						$item->setCount($item->getCount() - 1);
						$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : Item::get(Item::AIR));
					}
					$entity->spawnToAll();
					$player->setEnderPearl(true);
					Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new PearlingTask($player), 20);
				}
			}
		}
		if ($item instanceof \vale\hcf\sage\items\item\EnderPearl && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
			if ($block instanceof Fence || $block instanceof FenceGate) {
				$event->setCancelled(true);
				if ($player->isEnderPearl()) {
					#$player->sendMessage("lol" . $player->getEnderPearlTime());
					$event->setCancelled(true);
					return;
				}
				$nbt = UtilManager::createWith($player);
				$entity = Entity::createEntity("EnderPearl", $player->getLevel(), $nbt, $player);
				if ($entity instanceof EnderPearl) {
					$entity->setMotion($entity->getMotion()->multiply($item->getThrowForce()));
					if ($player->isSurvival()) {
						$item->setCount($item->getCount() - 1);
						$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : Item::get(Item::AIR));
					}
					$entity->spawnToAll();
					$player->setEnderPearl(true);
					Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new PearlingTask($player), 20);
				}
			}
		}
	}
}