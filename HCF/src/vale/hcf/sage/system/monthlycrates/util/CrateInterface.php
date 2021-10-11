<?php

declare(strict_types = 1);

#   /\ /\___ _ __ __ _ ___
#   \ \ / / _ \ '__/ _`|/_\
#    \ V / __/ | | (_| |__/
#     \_/ \___ |_| \__,|\___
#                  |___/

namespace vale\hcf\sage\system\monthlycrates\util;

//Base LIbraries
use pocketmine\{
    Player,
    Server,
    level\Level,
    entity\Entity,
};
use vale\hcf\sage\SagePlayer;


interface CrateInterface
{

	/**
	 * @return string
	 */
	public function getCrateType(): string;

	/**
	 * @return SagePlayer
	 */
	public function getCrateOpener(): SagePlayer;

	/**
	 * @return Level
	 */
	public function getLevel(): Level;

	/**
	 * @return int
	 */
	public function getSlot(): int;
}