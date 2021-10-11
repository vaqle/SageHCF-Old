<?php


namespace vale\hcf\sage\tasks\player;

use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use vale\hcf\sage\SagePlayer;


class AntiGlitchTask extends Task {
	/**
	 * @var Player $player
	 */
	private $player;

	private $direction;

	private $basePosition;

	private $first = true;

	public function __construct(SagePlayer $player, int $direction) {
		$this->player = $player;
		$this->direction = $direction;
		$this->basePosition = $player->getPosition();
	}

	public function onRun(int $currentTick) {
		if($this->first) {
			$this->getHandler()->cancel();
			$player = $this->player;
			$basePos = $this->basePosition;
			if ($player->isOnline()) {
				// echo "NO GLITCH ACTIVATED \n";
				switch ($this->direction) {
					case 2:
						$direction = $player->getDirectionVector();
						$player->knockBack($player,0, $direction->getX() - $direction->getZ(),0.5);
						break;
					case 1:
						$direction = $player->getDirectionVector();
						$player->knockBack($player,0, $direction->getX() - $direction->getZ(),0.5);
						break;
					case 3:
						$direction = $player->getDirectionVector();
						$player->knockBack($player,0, $direction->getX() - $direction->getZ(),0.5);
						break;
					case 0:
						$direction = $player->getDirectionVector();
						$player->knockBack($player,0, $direction->getX() - $direction->getZ(),0.5);
						break;
				}
			}
		}

		$this->first = false;
	}
}