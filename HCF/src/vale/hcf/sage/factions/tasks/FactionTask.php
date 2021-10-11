<?php


namespace vale\hcf\sage\factions\tasks;


use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;
use pocketmine\scheduler\Task;

class FactionTask extends Task {
	private $plugin;

	/**
	 * FactionTask constructor.
	 *
	 * @param Sage $plugin
	 */
	public function __construct(Sage $plugin) {
		$this->setPlugin($plugin);
	}

	/**
	 * @return Sage
	 */
	public function getPlugin() : Sage {
		return $this->plugin;
	}

	/**
	 * @param Sage $plugin
	 */
	public function setPlugin(Sage $plugin) {
		$this->plugin = $plugin;
	}
		public function onRun(int $currentTick) {
			$factions = [];
			foreach($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
				if($player instanceof SagePlayer) {
					if($player->isInFaction()) {
						$faction = $player->getFaction();
						if(!in_array($faction, $factions)) {
							$factions[] = $faction;
						}
					}
				}
			}
			if(count($factions) >= 0) {
				foreach($factions as $faction) {
					Sage::getFactionsManager()->addDTR($faction);
				}
			}
		}
	}