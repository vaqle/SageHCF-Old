<?php

namespace vale\hcf\sage\handlers\events;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\tasks\player\AntiGlitchTask;

class AntiGlitchHandler implements Listener
{


	public static $AntiLag = [];

	public $plugin;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	public function onInteract(PlayerInteractEvent $event)
	{
		$block = $event->getBlock();
		$delay = $event->getPlayer()->getPing() / 2;
		if ($delay < 100) $distance = 1; else if ($delay < 200) $distance = 1.5; else $distance = 2;
		if ($block->getId() === Block::FENCE_GATE && $event->getPlayer()->distance($block) < $distance && $event->isCancelled()) {
			$player = $event->getPlayer();
			if ($player instanceof SagePlayer) {
				switch ($player->getDirection()) {
					case 2:
						$task = new AntiGlitchTask($player, 2);
						Sage::getInstance()->getScheduler()->scheduleDelayedTask($task, $delay);
						self::$AntiLag[$player->getName()] = $task;
						break;

					case 1:
						$task = new AntiGlitchTask($player, 1);
						Sage::getInstance()->getScheduler()->scheduleDelayedTask($task, $delay);
						self::$AntiLag[$player->getName()] = $task;
						break;

					case 3:
						$task = new AntiGlitchTask($player, 3);
						Sage::getInstance()->getScheduler()->scheduleDelayedTask($task, $delay);
						self::$AntiLag[$player->getName()] = $task;
						break;

					case 0:
						$task = new AntiGlitchTask($player, 0);
						Sage::getInstance()->getScheduler()->scheduleDelayedTask($task, $delay);
						self::$AntiLag[$player->getName()] = $task;
						break;
				}
			}
		}
	}

	public function onDamage(EntityDamageEvent $event) {
		$entity = $event->getEntity();
		if($event->getCause() != EntityDamageEvent::CAUSE_PROJECTILE){
			if($event instanceof EntityDamageByEntityEvent and $entity instanceof SagePlayer){
				$damager = $event->getDamager();
				if($damager instanceof SagePlayer){
					$distance = $damager->distance($entity);
					$max = 6;
					if($distance >= $max){
						$event->setCancelled(true);
						foreach(Sage::getInstance()->getServer()->getOnlinePlayers() as $staff){
							if($staff->isStaffMode()){
								$staff->sendMessage("§r§c[Anticheat] §r§7The player " . $damager->getName() . " could be reaching (DISTANCE >= {$distance}). " . " §r§c§lPing §r§7{$entity->getPing()} §r§7MS");
							}
						}
					}
				}
			}
		}
	}


	public function onMoveCheck(PlayerMoveEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			if (!$player->isStaffMode()) {
				$level = $player->getLevel();
				$block1 = $level->getBlock(new Vector3($event->getTo()->getX(), $event->getTo()->getY(), $event->getTo()->getZ()));
				$block2 = $level->getBlock(new Vector3($event->getTo()->getX(), $event->getTo()->getY() + 1, $event->getTo()->getZ()));
				if ($block1->getId() == BlockIds::TRAPDOOR) return;
				if ($block2->getId() == BlockIds::TRAPDOOR) return;
				if ($block1->getId() == BlockIds::OAK_FENCE_GATE) return;
				if ($block2->getId() == BlockIds::OAK_FENCE_GATE) return;
				if ($block1->getId() == BlockIds::WOODEN_TRAPDOOR) return;
				if ($block2->getId() == BlockIds::WOODEN_TRAPDOOR) return;
				if ($block1->getId() == BlockIds::BIRCH_FENCE_GATE) return;
				if ($block1->getId() == BlockIds::ACACIA_FENCE_GATE) return;
				if ($block1->getId() == BlockIds::JUNGLE_FENCE_GATE) return;
				if ($block1->getId() == BlockIds::SPRUCE_FENCE_GATE) return;
				if ($block1->getId() == BlockIds::DARK_OAK_FENCE_GATE) return;
				if ($block1->getId() == BlockIds::PORTAL || $block2->getId() == BlockIds::PORTAL) return;
				if ($block2->isSolid() || $block1->getId() == BlockIds::SAND || $block1->getId() == BlockIds::GRAVEL || $block2->getId() == BlockIds::SAND || $block2->getId() == BlockIds::GRAVEL || $block2->getId() == BlockIds::COBBLESTONE || $block2->getId() == BlockIds::PLANKS) {
					#$player->disableMovement(time() + 5);
					$player->sendTip("§r§c§lNO CLIP ALERT \n  §r§7((We have detected unusual movement so we have teleported you back))");
					#$player->knockBack($player, 0, $player->getX() - $block1->getX(), $playYer->getZ() - $block1->getZ(), 1);
                    $direction = $player->getDirectionVector();
                    $player->knockBack($player,0, $direction->getX() - $direction->getZ(),0.5);
					foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $staff) {
						if ($staff instanceof SagePlayer) {
							if ($staff->isStaffMode()){
								$staff->sendTip("§r§c[Anticheat] §r§7The player " . $player->getName() . " could be phasing / glitching. " . " §r§c§lPing §r§7{$player->getPing()} §r§7MS");
						}
					}
				}
			}
		}
		}
	}
}