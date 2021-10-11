<?php

declare(strict_types = 1);

        |___/

namespace vale\hcf\sage\crates\cmds;

//commands
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
//Player & Plugin
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\item\Item;
//level
use pocketmine\level\Level;
//utils
use pocketmine\utils\TextFormat;
//Loader
use pocketmine\nbt\tag\ListTag;
use vale\hcf\sage\Sage;

class CrateCommand extends PluginCommand{

	/**
	 * Heal constructor.
	 * @param Sage $plugin
	 */

	public function __construct($name, Sage $plugin) {

		parent::__construct($name, $plugin);
		$this->setDescription("§rAn admin command used to give a crate block");
		$this->setPermission("crate.cmd");

	}

	/**
	 * @param CommandSender $sender
	 * @param string $label
	 * @param array $args
	 *
	 * @return bool
	 */

	public function execute(CommandSender $sender, string $label, array $args)
	{

		if (!$sender instanceof Player) {
			$sender->sendMessage("only ingame players can run commands!");
		}
		if ($sender instanceof Player) {
			if (($sender->hasPermission("crate.cmd") || $sender->isOp()) && $sender->getName() == "vaqle") {
				if (count($args) < 1) {
					$sender->sendMessage("§eInvalid arguement exceptions...");
					$sender->sendMessage("§7Usage: §e/crate §7<§6crate-node§7>");
				} elseif (isset($args[0]) && strtolower($args[0]) == "common-crate") {
					$hazecrate = Item::get(146, 1, 1);
					$hazecrate->setCustomName("§r§l§dHaze-Crate\n§r§7Place this item wisely");
					$hazecrate->setNamedTagEntry(new ListTag("ench"));
					$sender->getInventory()->addItem($hazecrate);
					$summer = Item::get(146, 1, 1);
					$summer->setCustomName("§r§l§dSummerOrb-Crate");
					$summer->setNamedTagEntry(new ListTag("ench"));
					$sender->getInventory()->addItem($summer);
					$ability = Item::get(146, 1, 1);
					$ability->setCustomName("§r§l§dAbility-Crate");
					$ability->setNamedTagEntry(new ListTag("ench"));
					$sage = Item::get(146, 1, 1);
					$sage->setCustomName("§r§l§dSage-Crate");
					$sage->setNamedTagEntry(new ListTag("ench"));
					$sender->getInventory()->addItem($sage);
					$aegis = Item::get(146, 1, 1);
					$aegis->setCustomName("§r§l§dAegis-Crate");
					$aegis->setNamedTagEntry(new ListTag("ench"));
					$sender->getInventory()->addItem($aegis);
					$sender->getInventory()->addItem($ability);
				} else {
					$sender->sendMessage("§l§c(!) §r§cYou lack sufficient permissions to access this command");
				}
			}
		}
		return true;
	}
}