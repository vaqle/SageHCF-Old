<?php

declare(strict_types = 1);

#   /\ /\___ _ __ __ _ ___
#   \ \ / / _ \ '__/ _`|/_\
#    \ V / __/ | | (_| |__/
#     \_/ \___ |_| \__,|\___
#                  |___/

namespace vale\hcf\sage\system\monthlycrates\tasks;

//Base Libraries
use vale\hcf\sage\system\monthlycrates\util\CrateSounds;
//Pocketmine Imports
use pocketmine\{Server, Player};
use pocketmine\scheduler\Task;
use pocketmine\inventory\Inventory;
use vale\hcf\sage\system\monthlycrates\util\CrateInterface;
use vale\hcf\sage\system\monthlycrates\util\Crate;
//Math
use pocketmine\math\Vector3;
//Core
use vale\hcf\sage\Sage;
use vale\hcf\sage\system\monthlycrates\util\CrateUtils;

class SlotGridShuffleTask extends Task{

	public $outsideGrid = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 15, 16, 17, 18, 19, 20, 24, 25, 26, 27, 28, 29, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 50, 51, 52, 53];
	public $grid = [12, 13, 14, 21, 22, 23, 30, 31, 32];
	/**
	 * Crate Constructor
	 * @param $player Player
	 * @param $inv Inventory
	 * @param $type string
	 * @param $slot int
	 * @param $timer int
	 *
	 * @return void
	 */
	public function __construct(Player $player, Inventory $inv , string $type, int $slot, int $timer){
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
		$glass = \pocketmine\item\Item::get(241, mt_rand(1, 10), 1);
		$glass->setCustomName("§r ");
		foreach($this->outsideGrid as $grid) {

			$this->inventory->setItem($grid, $glass);
			#  CrateSounds::playSound($this->player, CrateSounds::RANDOM_TOAST);
		}
		foreach($this->grid as $grid){
			if(!$this->inventory->getItem($grid)->getId() == CrateUtils::getCrateReward($this->inventory, $this->type)->getId() && !$this->inventory->getItem($grid)->getDamage() == CrateUtils::getCrateReward($this->inventory, $this->type)->getDamage() && !$this->inventory->getItem($grid)->getLore() == CrateUtils::getCrateReward($this->inventory, $this->type)->getLore()){

				$this->inventory->setItem($this->slot, CrateUtils::getCrateReward($this->inventory, $this->type));
				CrateSounds::playSound($this->player, CrateSounds::NOTE_HARP);
			}else{
				$this->inventory->setItem($this->slot, CrateUtils::getCrateReward($this->inventory, $this->type));
				CrateSounds::playSound($this->player, CrateSounds::NOTE_HARP);
			}
		}

		if($this->timer <= 0) {


			$this->player->getInventory()->addItem($this->inventory->getItem($this->slot));
			CrateSounds::playSound($this->player, CrateSounds::LEVEL_UP, 2);
			# CrateSounds::playSound($this->player, CrateSounds::RANDOM_TOAST);
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());


		}
	}
}