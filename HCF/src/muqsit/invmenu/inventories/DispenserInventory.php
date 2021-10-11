<?php
namespace muqsit\invmenu\inventories;

use muqsit\invmenu\inventories\SingleBlockInventory;

use pocketmine\block\Block;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\tile\Tile;

class DispenserInventory extends SingleBlockInventory{

	public function getBlock() : Block{
		return Block::get(Block::DISPENSER);
	}

	public function getNetworkType() : int{
		return WindowTypes::DISPENSER;
	}

	public function getTileId() : string{
		return "Dispenser";
	}

	public function getName() : string{
		return "Dispenser";
	}

	public function getDefaultSize() : int{
		return 9;
	}
}