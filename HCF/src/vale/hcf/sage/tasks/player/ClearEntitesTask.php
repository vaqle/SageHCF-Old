<?php

namespace vale\hcf\sage\tasks\player;

use pocketmine\entity\Human;
use pocketmine\entity\Zombie;
use pocketmine\scheduler\Task;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class ClearEntitesTask extends Task{

	public $plugin;

	public function __construct(Sage $plugin){
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick) {
		$total = 0;
		$server = Sage::getInstance()->getServer();
		foreach($server->getLevels() as $level){
			foreach($level->getEntities() as $entity){
				$total++;
				if(!$entity instanceof SagePlayer && !$entity instanceof Zombie && !$entity instanceof Human){
					$entity->close();
				}
			}
		}
	}
}