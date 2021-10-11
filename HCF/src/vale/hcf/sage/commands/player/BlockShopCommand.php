<?php
namespace vale\hcf\sage\commands\player;

use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use vale\hcf\sage\{provider\DataProvider,
	Sage,
	SagePlayer,
	system\shop\ShopInventory,
	tasks\player\CyberAttackQueueTask};
use vale\hcf\sage\system\messages\Messages;
use pocketmine\command\PluginCommand;

class BlockShopCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		parent::__construct("blockshop", $plugin);
		$this->plugin = $plugin;
		$this->setAliases(["bs,bsh,bsp"]);
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§5BUY §rand §l§5SEE §rpurchasable items.");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): void
	{
		if ($sender instanceof SagePlayer) {
		$sender->sendMessage("§r§7shop proccessing...");
         ShopInventory::openBlocksShop($sender);
		}
	}
}