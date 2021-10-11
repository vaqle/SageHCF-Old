<?php

namespace vale\hcf\sage\models\tiles;

use pocketmine\item\Item;
use pocketmine\item\SplashPotion;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Tile;

class PotionSpawner extends Tile
{
	private $time = 20*60;
	/**
	 * PotionSpawner constructor.
	 * @param Level $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt)
	{
		parent::__construct($level, $nbt);
	}


	public function onUpdate() : bool
	{
		$item = Item::get(Item::SPLASH_POTION, 22,1);
		$this->getLevel()->dropItem($this->add(0, 1), $item);
		if ($this->time == 0){
			$this->time = 20*60;
		} else {
			--$this->time;
		}
		return true;
	}

	/**
	 * @return string
	 */
	public function getName() : string
	{
		return "PotionSpawner";
	}

	protected function readSaveData(CompoundTag $nbt): void
	{
	}

	protected function writeSaveData(CompoundTag $nbt): void
	{
		// TODO: Implement writeSaveData() method.
	}
}