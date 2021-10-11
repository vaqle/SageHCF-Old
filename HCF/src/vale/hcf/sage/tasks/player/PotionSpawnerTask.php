<?php


namespace vale\hcf\sage\tasks\player;


use pocketmine\item\Item;
use vale\hcf\sage\models\tiles\DispenserBlock;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\models\tiles\PotionSpawner;
use pocketmine\entity\Human;
use pocketmine\scheduler\Task;
use pocketmine\tile\Chest;
use pocketmine\utils\TextFormat;

class PotionSpawnerTask extends Task
{
	private $plugin;
	private $time = 10;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
		$this->plugin->getScheduler()->scheduleRepeatingTask($this, 20);
	}

	/**
	 * Actions to execute when run
	 *
	 * @param int $currentTick
	 *
	 * @return void
	 */
	public function onRun(int $currentTick)
	{
		--$this->time;
		if ($this->time == 0) {
			$this->time = 10;
			foreach ($this->getPlugin()->getServer()->getLevels() as $level) {
				foreach ($level->getTiles() as $tile) {
					if ($tile instanceof PotionSpawner) {
						//$level->dropItem($tile->add(0, 1), new SplashPotion(22, mt_rand(1, 2)));
						$tile = $level->getTile($tile->add(0, 1,0));
						if ($tile instanceof Chest) {
							$pots = Item::get(Item::SPLASH_POTION, 22, 2);
							$tile->getInventory()->addItem($pots);
						}
					}
				}
			}
		}
	}

	public function cancel()
	{
		$this->getHandler()->cancel();
	}

	/**
	 * @return mixed
	 */
	public function getPlugin () : Sage
	{
		return $this->plugin;
	}

	/**
	 * @param mixed $plugin
	 */
	public function setPlugin (Sage $plugin)
	{
		$this->plugin = $plugin;
	}
}