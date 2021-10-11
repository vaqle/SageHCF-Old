<?php


namespace vale\hcf\sage\partneritems\tasks;


use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class NinjaStarTask extends Task
{

    /**
     * NinjaStarTask constructor.
     * @param Player|SagePlayer $player
     * @param Player|SagePlayer $teleport
     */


    public $time = 7;

    public $player;
    public $teleport;

    public function __construct(SagePlayer $player, SagePlayer $teleport){
    	$this->player = $player;
    	$this->teleport = $teleport;
    }

    public function onRun(int $currentTick)
	{
		--$this->time;
		if (!$this->player->isOnline()) {
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}
		if(!$this->teleport->isOnline()){
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}
		if($this->time === 5) {
			if ($this->player->isOnline()) {
				$message = "§r§cThe player §c§l{$this->player->getName()} §r§cwill teleport to you in §c§l5 §r§cseconds.";
				$this->teleport->sendMessage($message);
				Sage::playSound($this->teleport, "note.harp");
			}
		}
		if($this->time === 4) {
			if ($this->player->isOnline()) {
				$message = "§r§cThe player §c§l{$this->player->getName()} §r§cwill teleport to you in §c§l4 §r§cseconds.";
				$this->teleport->sendMessage($message);
				Sage::playSound($this->teleport, "note.harp");
			}
		}
		if($this->time === 3) {
			if ($this->player->isOnline()) {
				$message = "§r§cThe player §c§l{$this->player->getName()} §r§cwill teleport to you in §c§l3 §r§cseconds.";
				$this->teleport->sendMessage($message);
				Sage::playSound($this->teleport, "note.harp");
			}
		}
		if($this->time === 2) {
			if ($this->player->isOnline()) {
				$message = "§r§cThe player §c§l{$this->player->getName()} §r§cwill teleport to you in §c§l2 §r§cseconds.";
				$this->teleport->sendMessage($message);
				Sage::playSound($this->teleport, "note.harp");
			}
		}
		if($this->time === 1) {
			if ($this->player->isOnline()) {
				$message = "§r§cThe player §c§l{$this->player->getName()} §r§cwill teleport to you in §c§l1 §r§csecond";
				$this->teleport->sendMessage($message);
				Sage::playSound($this->teleport, "note.harp");
				Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
				$this->player->setLastHit("");
				$this->player->teleport(new Vector3($this->teleport->getX(), $this->teleport->getY(), $this->teleport->getZ()));
			}
		}
	}
}