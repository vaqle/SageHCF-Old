<?php

namespace vale\hcf\sage\handlers\events;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\FurnaceBurnEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use vale\hcf\sage\Sage;
use pocketmine\utils\TextFormat as TE;
use vale\hcf\sage\SagePlayer;

class RegionListener implements Listener
{

	public static $pearlcd = [];

	public $plugin;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}
	
	
	   public function onBorder(PlayerMoveEvent $event){
      $server = $this->plugin->getServer();
      $level = $server->getDefaultLevel();
      $safe = $level->getSafeSpawn();
      $player = $event->getPlayer();
      $x = 800;
      $z = 800;
      $xp = $event->getPlayer()->getFloorX();
      $zp = $event->getPlayer()->getFloorZ();
      $xs = $safe->getFloorX() + $x;
      $zs = $safe->getFloorZ() + $z;
      $x1 = abs($xp);
      $z1 = abs($zp);
      $x2 = abs($xs);
      $z2 = abs($zs);
      if($x1 >= $x2){
         $player->sendMessage("§r§cYou have reached the §c§lWorld Border§r§c.");
          $event->setCancelled();
      }
      if($z1 >= $z2){
       $player->sendMessage("§r§cYou have reached the §c§lWorld Border§r§c.");
         $event->setCancelled();
      }
   }
   
      /**
     * @param BlockPlaceEvent $event
     */
   public function onPlace(BlockPlaceEvent $event){
      $server = $this->plugin->getServer();
      $level = $server->getDefaultLevel();
      $safe = $level->getSafeSpawn();
      $player = $event->getPlayer();
      $x = 800;
      $z = 800;
      $xp = $event->getBlock()->getFloorX();
      $zp = $event->getBlock()->getFloorZ();
      $xs = $safe->getFloorX() + $x;
      $zs = $safe->getFloorZ() + $z;
      $x1 = abs($xp);
      $z1 = abs($zp);
      $x2 = abs($xs);
      $z2 = abs($zs);
      if($x1 >= $x2){
         $player->sendMessage("§r§cYou have reached the §c§lWorld Border§r§c.");
          $event->setCancelled();
      }
      if($z1 >= $z2){
         $player->sendMessage("§r§cYou have reached the §c§lWorld Border§r§c.");
         $event->setCancelled();
      }
   }
   /**
     * @param BlockBreakEvent $event
     */
   public function onBreak(BlockBreakEvent $event){
      $server = $this->plugin->getServer();
      $level = $server->getDefaultLevel();
      $safe = $level->getSafeSpawn();
      $player = $event->getPlayer();
      $x = 800;
      $z = 800;
      $xp = $event->getBlock()->getFloorX();
      $zp = $event->getBlock()->getFloorZ();
      $xs = $safe->getFloorX() + $x;
      $zs = $safe->getFloorZ() + $z;
      $x1 = abs($xp);
      $z1 = abs($zp);
      $x2 = abs($xs);
      $z2 = abs($zs);
      if($x1 >= $x2){
        $player->sendMessage("§r§cYou have reached the §c§lWorld Border§r§c.");
          $event->setCancelled();
      }
      if($z1 >= $z2){
       $player->sendMessage("§r§cYou have reached the §c§lWorld Border§r§c.");
         $event->setCancelled();
      }
   }

	

	public function onBlockPlace(BlockPlaceEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			if (!$player->isOp()) {
				$arr = ["South Road", "West Road", "North Road", "West Road", "East Road", "Cyber Attack"];
				if (in_array(Sage::getInstance()::getFactionsManager()->getClaimer($event->getBlock()->getX(), $event->getBlock()->getZ()), $arr)) {
					$event->setCancelled(true);
				}
			}
		}
	}

	public function BlockBreak(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			if (!$player->isOp()) {
				$arr = ["South Road", "West Road", "North Road", "West Road", "East Road", "Cyber Attack"];
				if (in_array(Sage::getInstance()::getFactionsManager()->getClaimer($event->getBlock()->getX(), $event->getBlock()->getZ()), $arr)) {
					$event->setCancelled(true);
				}
			}
		}
	}

	public function onPlayerMoveEvent(PlayerMoveEvent $event): void
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			if ($player->isMovementDisabled()) {
				$event->setCancelled(true);
			}
			if ($player->getRegion() !== $player->getCurrentRegion()) {
				if ($player->getCurrentRegion() === "Spawn") {
					$player->sendMessage(TE::GRAY . "§r§7Now Leaving: " . TE::RED . $player->getRegion() . TE::YELLOW . " (" . TE::RED . "Deathban" . TE::YELLOW . ")");
					$player->sendMessage(TE::GRAY . "§r§eNow Entering: " . TE::GREEN . "Spawn" . TE::YELLOW . " (" . TE::GREEN . "Non-Deathban" . TE::YELLOW . ")");
				} else {
					if ($player->getRegion() === "Spawn") {
						$player->sendMessage(TE::GRAY . "§r§eNow Leaving: " . TE::GREEN . "Spawn" . TE::YELLOW . " (" . TE::GREEN . "Non-Deathban" . TE::YELLOW . ")");
						$player->sendMessage(TE::GRAY . "§r§7Now Entering: " . TE::RED . $player->getCurrentRegion() . TE::YELLOW . " (" . TE::RED . "Deathban" . TE::YELLOW . ")");
					} else {
						$region = $player->getRegion() === $player->getFaction() ? TE::GREEN . $player->getRegion() : TE::RED . $player->getRegion();
						$currentRegion = $player->getCurrentRegion() === $player->getFaction() ? TE::GREEN . $player->getCurrentRegion() : TE::RED . $player->getCurrentRegion();
						$player->sendMessage(TE::GRAY . "§r§7Now Leaving: " . $region . TE::YELLOW . " (" . TE::RED . "Deathban" . TE::YELLOW . ")");
						$player->sendMessage(TE::GRAY . "§r§eNow Entering: " . TE::RED . $currentRegion . TE::YELLOW . " (" . TE::RED . "Deathban" . TE::YELLOW . ")");
					}
				}
				$player->setRegion($player->getCurrentRegion());
			}
		}
	}
}