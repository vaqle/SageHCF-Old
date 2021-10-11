<?php

namespace vale\hcf\sage\system\monthlycrates;

use pocketmine\Server;
use vale\hcf\sage\Sage;
use vale\hcf\sage\system\monthlycrates\command\MonthlyCrateCommand;
use vale\hcf\sage\system\monthlycrates\event\CrateInteraction;

class MonthlyCrates{

	public Sage $plugin;
	public static array $opening = [];

	public function __construct(Sage $plugin){
		$this->plugin = $plugin;
		$this->plugin->getServer()->getLogger()->info("Enabling MC's");
		$this->init();
	}


	public static function init(): void{
		new CrateInteraction(Sage::getInstance());
		Server::getInstance()->getCommandMap()->register("monthlycrate", new MonthlyCrateCommand(Sage::getInstance()));
	}

}