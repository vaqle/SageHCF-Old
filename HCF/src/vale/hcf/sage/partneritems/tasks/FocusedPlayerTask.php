<?php


namespace vale\hcf\sage\partneritems\tasks;


use pocketmine\entity\Entity;
use pocketmine\scheduler\Task;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class FocusedPlayerTask extends Task
{

    /**
     * FocusedPlayerTask constructor.
     * @param Entity|SagePlayer $damager
     * @param SagePlayer $focused
     */

    public $damager;
    public $focused;
    public $time = 10;
    public function __construct(SagePlayer $damager, SagePlayer $focused){
    	$this->damager = $damager;
    	$this->focused = $focused;
    	$damager->sendMessage("§r§eYou are now focusing §r§6l{$focused->getName()}");
    	$focused->sendMessage("§r§eYou are being focused by §6§l{$damager->getName()}");
    }

	/**
	 * @param int $currentTick
	 */
	public function onRun(int $currentTick)
	{
		--$this->time;
		if($this->time === 0){
			#$this->damager->sendMessage("over");
			$this->damager->setFocusedPlayer("");
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}

		if(!$this->focused->isOnline()){
			$this->damager->setFocusedPlayer("");
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}

		if(!$this->damager->isOnline()){
			$this->damager->setFocusedPlayer("");
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}
	}
}