<?php
namespace vale\hcf\sage\commands\player;

use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use vale\hcf\sage\{models\util\UtilManager,
	provider\DataProvider,
	provider\SQLProvider,
	Sage,
	SagePlayer,
	system\shop\ShopInventory,
	tasks\player\CyberAttackQueueTask};
use vale\hcf\sage\system\messages\Messages;
use pocketmine\command\PluginCommand;

class BalTopCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		parent::__construct("baltop", $plugin);
		$this->plugin = $plugin;
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§cVIEW §rand §l§cSEE  §rTOP Balances");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): void
	{
		if ($sender instanceof SagePlayer) {
			Sage::getSQLProvider()->sendTopMoney($sender);
		}
	}
}