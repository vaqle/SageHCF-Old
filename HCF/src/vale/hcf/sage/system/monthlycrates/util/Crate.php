<?php

declare(strict_types = 1);

#   /\ /\___ _ __ __ _ ___
#   \ \ / / _ \ '__/ _`|/_\
#    \ V / __/ | | (_| |__/
#     \_/ \___ |_| \__,|\___
#                  |___/

namespace vale\hcf\sage\system\monthlycrates\util;

//Base Libraries
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\monthlycrates\util\CrateInterface;
use vale\hcf\sage\system\monthlycrates\util\CrateUtils;
use vale\hcf\sage\system\monthlycrates\util\CrateSounds;
//Pocketmine Imports
use pocketmine\{level\Level, Server, Player};
//Math
use pocketmine\math\Vector3;

class Crate
{

	/**
	 * Crate Constructor
	 * @param $player Player
	 * @param $vector Vector3
	 * @param $type string
	 * @param $slot int
	 */
	public function __construct(Player $player, Vector3 $vector, string $type, int $slot)
	{
		$this->player = $player;
		$this->vector = $vector;
		$this->type = $type;
		$this->slot = $slot;

	}

}