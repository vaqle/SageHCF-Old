<?php

namespace vale\hcf\sage\partneritems\tasks;

use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as TE;

class PitemCooldownTask extends Task {

	/** @var SagePlayer */
	protected $player;

	/**
	 * CombatTagTask Constructor.
	 * @param SagePlayer $player
	 */
	public function __construct(SagePlayer $player){
		$this->player = $player;
		$player->setPartneredTime(25);
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
		if($player->getPitemTime() <= 0){
			$player->setPartnered(false);
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}else{
			$player->setPartneredTime($player->getPitemTIme() - 1);
		}
	}
}

?>