<?php
declare(strict_types=1);

namespace vale\hcf\sage\items\item;

use vale\hcf\sage\SagePlayer;
use pocketmine\nbt\tag\ByteTag;

class BetterSnowballItem extends \pocketmine\item\Snowball{

	public function getProjectileEntityType() : string {
		if(!$this->getNamedTag()->hasTag("Partner_Item_Snowball")){
			return "Snowball";
		}
		return "KenzoBall";
	}
}
