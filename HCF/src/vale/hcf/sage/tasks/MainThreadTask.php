<?php

namespace vale\hcf\sage\tasks;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use vale\hcf\sage\handlers\events\PlayerListener;
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SageListener;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\deathban\Deathban;
use vale\hcf\sage\system\ranks\RankAPI;

class MainThreadTask extends Task
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick)
	{
		foreach (Server::getInstance()->getOnlinePlayers() as $player) {
			if ($player instanceof SagePlayer) {
				$player->checkSets();
				$player->updateNametag();
			}

			if (DataProvider::$rankprovider->exists($player->getName())){
				SageListener::addPermissions($player, RankAPI::getRank($player));
			}

			/*if(Deathban::isDeathBanned($player)) {
				  $time = Deathban::getDeathBanTime($player);
					Deathban::setDeathbanned($player, $time - 1);
				}


			/*if(!Deathban::isDeathBanned($player) && !$player->isOp()){
				$level = Sage::getInstance()->getServer()->getLevelByName("deathban");
				$plevel = Sage::getInstance()->getServer()->getLevelByName($player->getLevel()->getName());
				$mainLevel = Sage::getInstance()->getServer()->getLevelByName("hcfmap")->getSafeSpawn();
				if($plevel === $level){
					$player->teleport($mainLevel);
				}
			}

			/*if(Deathban::isDeathBanned($player)) {
				$level = Sage::getInstance()->getServer()->getLevelByName("deathban");
				$plevel = Sage::getInstance()->getServer()->getLevelByName($player->getLevel()->getName());
				$name = $player->getName();
				if ($plevel !== $level) {
					$player->teleport($level->getSpawnLocation());
					$player->sendMessage("§l§c[!] §r§cWelcome to the §c§lDeathban §r§carena “{$name}“ worthless maggot fight for redeployment or use a life to leave. \n§r§7((§r§o§7To leave the §r§c§lDeathBan §r§7§oarena you need to be revived or wait out your death ban))");
				}
			}



			if(Deathban::isDeathBanned($player)){
				if(Deathban::getDeathBanTime($player) <= 0){
					Deathban::remove($player);
					$level = Sage::getInstance()->getServer()->getLevelByName("hcfmap");
					$player->teleport($level->getSpawnLocation());
				}
			}*/

			if ($player->hasPvpTimer()) {
				if (!Sage::getFactionsManager()->isSpawnClaim($player)) {
					$player->lowerPvpTimer(1);
				}
			}
		}
	}
}