<?php

namespace vale\hcf\sage\tasks\player;

use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as TE;

class CombatTagTask extends Task {

	/** @var SagePlayer */
	protected $player;

	/**
	 * CombatTagTask Constructor.
	 * @param SagePlayer $player
	 */
	public function __construct(SagePlayer $player){
		$this->player = $player;
		$player->setCombatTagTime(35);
		$player->sendMessage("§r§cYou have been spawn-tagged for 30s.");
	}

	/**
	 * @param Int $currentTick
	 * @return void
	 */
	public function onRun(Int $currentTick) : void {
		$player = $this->player;
		if(!$player->isOnline()){
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			return;
		}
		if(!$player->isCombatTagged()){
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			return;
		}
		if($player->getCombatTagTime() === 0){
			$player->setCombatTagged(false);
			$player->sendMessage("§r§cYou are no longer spawn-tagged§r§c.");
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}else{
			$player->setCombatTagTime($player->getCombatTagTime() - 1);
		}
	}
}

?>