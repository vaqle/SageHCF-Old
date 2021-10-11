<?php

namespace vale\hcf\sage\commands\player;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use vale\hcf\libaries\Command;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class TagCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		parent::__construct("tag", $plugin);
		$this->plugin = $plugin;
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§l§4SET §ra custom tag.");
		$this->setPermission("hcf.custom.tag");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof SagePlayer) {
			if ($sender->hasPermission("hcf.custom.tag")) {
				if (!isset($args[0])) {
					$sender->sendMessage("§6§l* §r§eYou cannot set your tag to ''!");
					$sender->sendMessage($sender->getFacingDirection());
					return;
				}
				if (!ctype_alnum($args[0])) {
					$sender->sendMessage(TextFormat::RED . "§r§eThe tag name cannot contain numbers.");
					return;
				}
				if (strlen($args[0]) > 5) {
					$sender->sendMessage(TextFormat::RED . "§6§l* §r§eThe tag must be less than 5 characters");
					return;
				}
				$sender->setPlayerTag($args[0]);
				$sender->sendMessage("§6§l* §r§eYou succesfully set your tag to §6§l$args[0]§r§e.");
				$sender->getLevel()->addSound(new AnvilUseSound($sender));
			}
		}
	}
}