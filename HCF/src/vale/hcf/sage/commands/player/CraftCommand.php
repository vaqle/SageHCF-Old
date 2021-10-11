<?php

namespace vale\hcf\sage\commands\player;

use MongoDB\Driver\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class CraftCommand extends PluginCommand{

	public $plugin;

	public function __construct(string $name, Sage $plugin)
	{
		parent::__construct($name, $plugin);
		$this->plugin = $plugin;
		$this->setPermission("hcf.sage.craft");
		$this->setDescription("§r§frun this command to".  str_repeat("§r§7", 4) . "§r§e§lOPEN §r§fthe crafting grid");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if(!$sender instanceof SagePlayer){
			return;
		}

		if(!$sender->hasPermission("hcf.sage.craft")){
			$sender->sendMessage("§l§c[!] §r§cYou dont have permission for this command “{$sender->getName()}”\n§7This is only available to donators. donate now to gain access");
			$sender->getLevel()->addSound(new AnvilFallSound($sender));
		}
		$sender->sendMessage("§r§7Opening Crafting Inventory...");
	}

}