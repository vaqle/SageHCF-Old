<?php

namespace vale\hcf\sage\tasks\player;

use pocketmine\Player;
use pocketmine\Server;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use pocketmine\scheduler\Task;
use vale\hcf\sage\handlers\events\SotwHandler;
use vale\hcf\sage\SageListener;
use vale\hcf\sage\SagePlayer;
use xenialdan\apibossbar\DiverseBossBar;

class StartOfTheWorldTask extends Task
{

	/**
	 * StartOfTheWorldTask Constructor.
	 * @param Int $time
	 */
	public function __construct(int $time = 60)
	{
		SotwHandler::setTime($time);
	}

	/**
	 * @param Int $currentTick
	 * @return void
	 */
	public function onRun(int $currentTick): void
	{
		if (!SotwHandler::isEnable()) {
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			return;
		}
		if (SotwHandler::getTime() === 0) {
			SotwHandler::setEnable(false);
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		} else {
			SotwHandler::setTime(SotwHandler::getTime() - 1);
		}
	}
}