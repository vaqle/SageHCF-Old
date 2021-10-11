<?php

namespace vale\hcf\sage\commands\player;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use vale\hcf\libaries\Command;
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class SkitCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		parent::__construct("skit", $plugin);
		$this->plugin = $plugin;
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§6VIEW §rand §l§6SELECT §rkits");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if($sender instanceof SagePlayer){
			KitManager::openKitMenu($sender);
			$sender->setPlayerTag("noob");
		}
		return true;
	}
}
