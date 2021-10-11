<?php

namespace vale\hcf\sage\partneritems\tasks;


use pocketmine\scheduler\Task;
use pocketmine\Server;
use vale\hcf\sage\models\entitys\FakeLogger;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class LoggerBaitTask extends Task {


	public $time = 15;

	public $player;

	public $logger;

	public function __construct(SagePlayer $player, FakeLogger $logger){
		$this->player = $player;
		$this->logger = $logger;
	}


	public function onRun(int $currentTick)
	{
		--$this->time;
		if($this->time === 0){
			foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player){
				$player->showPlayer($this->player);
			}
		}
		if(!$this->player->isOnline()) {
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			if($this->logger->isAlive()){
				$this->logger->close();;
			}
		}

		if(!$this->logger->isAlive()){
			foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player){
				$player->showPlayer($this->player);
			}
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			#Server::getInstance()->broadcastMessage(" logger died");
		}

		if($this->time === 14){
			foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player){
				$player->hidePlayer($this->player);
			#	Server::getInstance()->broadcastMessage(" hidden");

			}
		}
	}
}