<?php

declare(strict_types = 1);

#   /\ /\___ _ __ __ _ ___
#   \ \ / / _ \ '__/ _`|/_\
#    \ V / __/ | | (_| |__/
#     \_/ \___ |_| \__,|\___
#                  |___/

namespace vale\hcf\sage\system\monthlycrates\tasks;

//Base Libraries
use vale\hcf\sage\system\monthlycrates\util\CrateInterface;
use vale\hcf\sage\system\monthlycrates\util\Crate;
use vale\hcf\sage\system\monthlycrates\util\CrateSounds;
//Pocketmine Imports
use pocketmine\{Server, Player};
use pocketmine\scheduler\Task;
use pocketmine\inventory\Inventory;
//Math
use pocketmine\math\Vector3;
//Core
use vale\hcf\sage\Sage;

class FinalAnimationTask extends Task{

	public $outsideGrid = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 15, 16, 17, 18, 19, 20, 24, 25, 26, 27, 28, 29, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 50, 51, 52, 53];
	/**
	 * FinalAnimationTask Constructor
	 * @param $player Player
	 * @param $inv Inventory
	 * @param $type string
	 * @param $slot int
	 * @param $timer int
	 *
	 * @return void
	 */
	public function __construct(Player $player, Inventory $inv , string $type, int $slot, int $timer = 5){
		$this->player = $player;
		$this->inventory = $inv;
		$this->type = $type;
		$this->slot = $slot;
		$this->timer = $timer;

	}

	public function onRun(int $currentTick){
		$this->timer--;
        	if (!$this->player->isOnline()) {
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}
		$red = \pocketmine\item\Item::get(241, 14, 1);
		$red->setCustomName("§r ");
		$orange = \pocketmine\item\Item::get(241, 1, 1);
		$orange->setCustomName("§r ");
		$yellow = \pocketmine\item\Item::get(241, 4, 1);
		$yellow->setCustomName("§r ");
		$glassarray = [$orange, $red, $yellow, $orange, $red, $yellow];
		foreach($this->outsideGrid as $grid) {

			$this->inventory->setItem($grid, $glassarray[array_rand($glassarray)]);
		}
		# $this->inventory->setItem($this->slot, CrateUtils::getCrateReward($this->inventory, $this->type));
		CrateSounds::playSound($this->player, CrateSounds::NOTE_HARP);
		if($this->timer <= 0) {
			$this->player->getInventory()->addItem($this->inventory->getItem($this->slot));
			#CrateSounds::playSound($this->player, CrateSounds::WITHER_BREAK_BLOCK);
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}
	}
}