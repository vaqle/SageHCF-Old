<?php


namespace vale\hcf\sage\tasks\player;


use pocketmine\scheduler\Task;
use pocketmine\Server;
use vale\hcf\sage\handlers\events\SotwHandler;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class SotwAntiLagTask extends Task
{

	public $player;

    public function __construct(SagePlayer $player)
	{
		$this->player = $player;
	}
	public function onRun(int $currentTick)
	{
		$mngr = Sage::getFactionsManager();
		if (!SotwHandler::isEnable()){
			foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $onlinePlayer) {
				$onlinePlayer->showPlayer($this->player);
				$this->player->showPlayer($onlinePlayer);
				Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			}
		}
		$mngr = Sage::getFactionsManager();
		if(!$mngr->isSpawnClaim($this->player)){
			foreach (Server::getInstance()->getOnlinePlayers() as $player){
				$player->showPlayer($this->player);
			}
		}
    	foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $Onlineplayer){
    		if(SotwHandler::isEnable()){
    			$manager = Sage::getFactionsManager();
    			if($manager->isSpawnClaim($Onlineplayer)){
    				$Onlineplayer->hidePlayer($this->player);
    				$this->player->hidePlayer($Onlineplayer);
				}
			}
		}
    }
}