<?php


namespace vale\hcf\sage\partneritems\tasks;


use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use vale\hcf\sage\factions\FactionsManager;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\cooldowns\Cooldown;

class AntiTrapBeaconTask extends Task
{

    /**
     * AntiTrapBeaconTask constructor.
     * @param SagePlayer $player
     * @param string $faction
     * @param string|null $claim
     * @param \pocketmine\level\Level|null $level
     * @param Block $block
     */

    public $player;
    public $faction;
    public $claim;
    public $level;
    public $block;

    public $time = 30;

    public function __construct($player, string $faction, string $claim, Level $level, Block $block){
    	$this->player = $player;
    	$this->faction = $faction;
    	$this->claim = $claim;
    	$this->level = $level;
    	$this->block = $block;
    }

    public function onRun(int $currentTick)
	{
		--$this->time;
		$block = Sage::getInstance()->getServer()->getLevelByName("hcfmap")->getBlock(new Vector3($this->block->getX(), $this->block->getY(), $this->block->getZ()));
		if ($block->getId() !== BlockIds::MELON_BLOCK) {
			Server::getInstance()->broadcastMessage("block was removed");
			foreach (Sage::getFactionsManager()->getOnlineMembers($this->claim) as $onlineMember){
				if(isset(Cooldown::$antiTrapped[$onlineMember->getName()])){
					unset(Cooldown::$antiTrapped[$onlineMember->getName()]);
			

				}
			}
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());


		}

		if ($this->time === 29) {
			$pos = $this->claim;
			$level = Server::getInstance()->getLevelByName("hcfmap");
			$block = Sage::getInstance()->getServer()->getLevelByName("hcfmap")->getBlock(new Vector3($this->block->getX(), $this->block->getY(), $this->block->getZ()));
			if ($block->getId() === BlockIds::MELON_BLOCK) {
				foreach (Sage::getFactionsManager()->getOnlineMembers($this->claim) as $member){
					if($member instanceof SagePlayer){
						if($member->distanceSquared($block) <= 144){
			
                         Cooldown::$antiTrapped[$member->getName()] = time() + 29;
						}
					}
				}
			}
		}
			if($this->time === 0){
				$level = Server::getInstance()->getLevelByName("hcfmap");
				$block = Sage::getInstance()->getServer()->getLevelByName("hcfmap")->getBlock(new Vector3($this->block->getX(), $this->block->getY(), $this->block->getZ()));
				if ($block->getId() === BlockIds::MELON_BLOCK) {
					$level->setBlock(new Vector3($this->block->getX(), $this->block->getY(), $this->block->getZ()), Block::get(Block::AIR));
					Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			
				}
			}
		}
}