<?php


namespace vale\hcf\sage\tasks\player;


use pocketmine\scheduler\Task;

use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class PearlingTask extends Task
{
	/** @var Player */
	protected $player;

	/**
	 * EnderPearlTask Constructor.
	 * @param Player $player
	 */
	public function __construct(SagePlayer $player){
		$this->player = $player;
		$player->setEnderPearlTime(20);
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
		if($player->isEnderPearl()){
			if($player->getEnderPearlTime() === 0){
				$player->setEnderPearl(false);
				Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			}else{
				$player->setEnderPearlTime($player->getEnderPearlTime() - 1);
			}
		}else{
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			return;
		}
	}
}
