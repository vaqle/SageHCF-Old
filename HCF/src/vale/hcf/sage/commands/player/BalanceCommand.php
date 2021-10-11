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

class BalanceCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		parent::__construct("balance", $plugin);
		$this->plugin = $plugin;
		$this->setAliases(["money,cash,bal"]);
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§cVIEW §rand §l§cSEE  §rother players balances");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): void
	{
		if (!$sender instanceof SagePlayer) {
			return;
		}
		if(!isset($args[0])) {
			$bal = Sage::getSQLProvider()->getBalance($sender->getName());
			$sender->sendMessage("§r§6Your current Balance: §c$".$bal);
			Sage::getSQLProvider()->sendTopMoney($sender);
			return;
		}
		if (!$p = Server::getInstance()->getPlayer($args[0])) {
			$sender->sendMessage(str_replace(["&", "{playerName}"], ["§", $args[0]], Messages::NOT_ONLINE));
			return;
		}
		$provider = Sage::getSQLProvider();
		$target = $p;
		$senderbal = $sender->getBalance();
		$balance = $provider->getBalance($target->getName());
		$sender->sendMessage("§r§6§l{$target->getName()} §r§chas a balance of §r§6$$balance.");
		if($balance === $provider->getBalance($sender->getName())) {
			$sender->sendMessage("§r§7((§6Your balance is equal§r§7))");
		}elseif ($balance > $senderbal){
			$sender->sendMessage("§r§7((§6They are richer then you§r§7))");
		}else{
			$sender->sendMessage("§r§7((§6You are richer then them§r§7))");
		}
	}
}