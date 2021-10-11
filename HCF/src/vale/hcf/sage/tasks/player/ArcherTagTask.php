<?php

namespace vale\hcf\sage\tasks\player;

use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as TE;

class ArcherTagTask extends Task {

	/** @var SagePlayer */
	protected $player;

	/**
	 * ArcherTagTask Constructor.
	 * @param SagePlayer $player
	 */
	public function __construct(SagePlayer $player){
		$this->player = $player;
		$player->setArchertagTime(25);
		$player->sendMessage("§r§eYou have been archer-tagged for 25s.");
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
		if(!$player->isArcherTagged()){
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			return;
		}
		if($player->getArchertagTime() === 0){
			$player->setArcherTagged(false);
			$player->sendMessage("§r§eYou are no longer §r§e§larcher-tagged§r§e.");
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}else{
			$player->setArchertagTime($player->getArchertagTime() - 1);
		}
	}
}

?>