<?php

namespace vale\hcf\sage\tasks\player;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use xenialdan\apibossbar\BossBar;

class BarUpdateTask extends Task{

	public float $percent = 0.00;

	public function onRun(int $currentTick)
	{
		if (Sage::$currentbossbartick > 4) {
			Sage::$currentbossbartick = 0;
		}
		$online = Server::getInstance()->getOnlinePlayers();
		foreach (Server::getInstance()->getOnlinePlayers() as $player) {
			if (count($online) > 0) {
				$fund = DataProvider::getFundData()->get("Fund");
				$total = DataProvider::$fund->get("Total");
				$bar = new BossBar();
				$bar->setTitle("§r§e§l<§6X§e> §6§lWORLD FUND EVENT §r§e§l<§6X§e>");
				$bar->setSubTitle("§6§lTotal §r§e{$fund} §r§7/ {$total} §r§6$");
				$bar->setPercentage($this->percent);
				$bar->addPlayer($player);
				Sage::$currentbossbartick++;

			}
		}
	}
}