<?php

namespace vale\hcf\sage\tasks\player;

use pocketmine\Player;
use vale\hcf\sage\Sage;
use pocketmine\scheduler\Task;
use vale\hcf\sage\system\events\FundEvent;
use vale\hcf\sage\SageListener;
use vale\hcf\sage\SagePlayer;

class FundEventStartTask extends Task {

	/**
	 * StartOfTheWorldTask Constructor.
	 * @param Int $time
	 */
	public function __construct(Int $time = 60){
		FundEvent::setTime($time);
	}

	/**
	 * @param Int $currentTick
	 * @return void
	 */
	public function onRun(Int $currentTick) : void {
		if(!FundEvent::isEnable()){
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			return;
		}
		if(FundEvent::getTime() === 0){
			FundEvent::setEnable(false);
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}else{
			FundEvent::setTime(FundEvent::getTime() - 1);
		}
	}
}