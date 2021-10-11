<?php

namespace vale\hcf\sage\system\deathban;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\level\sound\AnvilFallSound;
use vale\hcf\sage\partneritems\PItemListener;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class DeathbanListener implements Listener
{

	public $plugin;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	public function onRespawn(PlayerRespawnEvent $event)
	{
		$player = $event->getPlayer();
		if (!$player instanceof SagePlayer) {
			return;
		}

		if (!Deathban::isDeathBanned($player)) {
			return;
		}

		if (Deathban::isDeathBanned($player)) {
			$time = Deathban::getDeathBanTime($player);
			if ($time >= 1) {
				$level = Sage::getInstance()->getServer()->getLevelByName("deathban");
				$player->teleport($level->getSpawnLocation());
			}
		}
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
						if ($plevel === $level) {
							PItemListener::Lightning($player);
							$player->setCombatTagged(false);
							$player->setCombatTagTime(0);
							Deathban::setDeathbanned($cause, Deathban::getDeathBanTime($player) - 100);
							$dc = DataProvider::getFundData()->get("deathcount");
							DataProvider::$fund->set("deathcount", $dc + 1);
							DataProvider::$fund->save();
							$item = $cause->getInventory()->getItemInHand();
							$name = $item->getName();
							if ($item->hasCustomName()) $name = $item->getCustomName();
							$pname = $player->getName();
							$pkills = DataProvider::getKills($pname);
							$causen = $cause->getName();
							$causek = DataProvider::getKills($causen);
							$event->setDeathMessage("§r§c (DEATHBAN ARENA) {$pname}§r§4[{$player->getKills()}] §r§ewas slain by {$causen}§r§4[{$causek}] §r§eusing §r§c{$name}§r§e." . " §r§e[#$dc]");
						}
					}
				}
			}
		}
	}




	public function onPlace(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		$level = $player->getLevel()->getName();
		$name = $player->getName();
		if(!$player instanceof SagePlayer){
			return;
		}

		if(!Deathban::isDeathBanned($player)){
			return;
		}

		if(Deathban::isDeathBanned($player)){
			$player->sendMessage("§l§c[!] §r§cPlacing §c§lblocks §r§cin the §c§lDeathban §r§carena is forbidden “{$name}”\n§r§7((§r§o§7To leave the §r§c§lDeathBan §r§7§oarena you need to be revived or wait out your death ban))");
			$player->getLevel()->addSound(new AnvilFallSound($player));
			$event->setCancelled(true);
		}
	}

	public function onCommandPreProccess(PlayerCommandPreprocessEvent $event)
	{
		$player = $event->getPlayer();
		$command = str_split($event->getMessage());
		if (!$player instanceof SagePlayer) {
			return;
		}
		if (!Deathban::isDeathBanned($player)) {
			return;
		}
		if (Deathban::isDeathBanned($player)) {
			if ($command[0] == "/") {
				$player->sendMessage("§l§c[!] §r§cThe command §c§l'' §r§cis restricted in the §c§lDeathban §r§carena “{$player->getName()}”\n§r§7((§r§o§7To leave the §r§c§lDeathBan §r§7§oarena you need to be revived or wait out your death ban))");
				$event->setCancelled(true);
			}
			if ($command[0] == "." && $command[1] == "/") {
				$player->sendMessage("§l§c[!] §r§cThe command §c§l'' §r§cis restricted in the §c§lDeathban §r§carena “{$player->getName()}”\n§r§7((§r§o§7To leave the §r§c§lDeathBan §r§7§oarena you need to be revived or wait out your death ban))");
                $event->setCancelled(true);
			}
		}
	}


	public function onBlockBreakEvent(BlockBreakEvent $event){
		$player = $event->getPlayer();
		$level = $player->getLevel()->getName();
		$name = $player->getName();
		if(!$player instanceof SagePlayer){
			return;
		}

		if(!Deathban::isDeathBanned($player)){
			return;
		}

		if(Deathban::isDeathBanned($player)){
			$player->sendMessage("§l§c[!] §r§cBreaking §c§lblocks §r§cin the §c§lDeathban §r§carena is forbidden “{$name}”\n§r§7((§r§o§7To leave the §r§c§lDeathBan §r§7§oarena you need to be revived or wait out your death ban))");
			$player->getLevel()->addSound(new AnvilFallSound($player));
			$event->setCancelled(true);
		}
	}

	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		if(!$player instanceof SagePlayer){
			return;
		}
		if(!Deathban::isDeathBanned($player)){
			return;
		}
		if(Deathban::isDeathBanned($player)){
			$name = $player->getName();
			$time = Sage::secondsToTime(Deathban::getDeathBanTime($player));
			$player->teleport(Sage::getInstance()->getServer()->getLevelByName("deathban")->getSpawnLocation());
			$player->sendMessage("§l§c[!] §r§c§l{$name} §r§csince you are §c§lDeathbanned §r§cfor another  §r§4“{$time}”\n§r§7((§r§o§7We teleported you back! To leave, Use a life or wait out your deathban!))");

		}
	}

}
