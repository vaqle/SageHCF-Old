<?php
namespace vale\hcf\sage\commands\player;

use pocketmine\command\PluginCommand;
use pocketmine\command\{CommandSender, Command};
use pocketmine\entity\Human;
use pocketmine\entity\Zombie;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TE;
use vale\hcf\sage\{handlers\events\SotwHandler,
	provider\DataProvider,
	Sage,
	SagePlayer,
	tasks\player\BarUpdateTask};
use vale\hcf\sage\system\events\FundEvent;
use vale\hcf\sage\tasks\player\CyberAttackQueueTask;

class FundCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct("fund", $plugin);
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§aDONATE §rand §l§aSEE §rthe world fund");

	}

	public function execute(CommandSender $sender, string $label, array $args): bool
	{
		if (count($args) === 0 && $sender instanceof SagePlayer) {
			$sender->sendMessage("§r§e§l<§6X§e> §6§lWORLD FUND EVENT §r§e§l<§6X§e>");
			$sender->sendMessage("§6§l* §r§edonate §r§7((donates 500$ to the world fund))");
			$sender->sendMessage("§6§l* §r§end §r§7((ends the world fund))");
			$sender->sendMessage("§6§l* §r§reset §r§7((resets the world fund))");

			return false;
		}

		switch ($args[0]) {
            case "cyber":
                $this->plugin->getScheduler()->scheduleRepeatingTask(new CyberAttackQueueTask(), 20);
                break;
			case "reset":
				$fund = DataProvider::getFundData()->get("Fund");
				if ($sender->isOp()) {
					DataProvider::$fund->set("Fund", 0);
					Server::getInstance()->broadcastMessage(("§r§e§l<§6X§e> §6§lWORLD FUND EVENT §r§e§l<§6X§e> §r§7has reset!"));
				}
				break;

			case "clear":
				$server = Sage::getInstance()->getServer();
				foreach($server->getLevels() as $level) {
					foreach ($level->getEntities() as $entity) {
						if (!$entity instanceof SagePlayer && !$entity instanceof Zombie && !$entity instanceof Human) {
							$entity->close();
						}
					}
				}
				break;

			case "end":
				$fund = DataProvider::getFundData()->get("Fund");
				if ($fund >= 10000 && $sender->isOp()) {
					Server::getInstance()->broadcastMessage("§r§e§l<§6X§e> §r§7The world fund event has ended And everyone online has recieved the following");
					Server::getInstance()->broadcastMessage("§r§e§l<§6X§e> §r§b§l* §r§b5x Aidrops");
					Server::getInstance()->broadcastMessage("§r§e§l<§6X§e> §r§5§l* §r§53x PartnerPackages");
					Server::getInstance()->broadcastMessage("§r§e§l<§6X§e> §r§d§l* §r§d7x Summer Orbs");
					FundEvent::rewardAllPlayers();
				} else {
					$sender->sendMessage("§r§cThe fund has not reached its goal");
				}
				break;
			case "donate":
				$fund = DataProvider::getFundData()->get("Fund");
				if ($sender instanceof SagePlayer) {
					if (!$sender->getBalance() >= 500) {
						$sender->sendMessage("§r§cInsuffecient funds.");
					}
					if ($fund >= 10000) {
						$sender->sendMessage("§r§cThe fund has reached its goal");
					}
					if ( $fund <= 10000 && $sender->getBalance() >= 500) {
						$fund = DataProvider::getFundData()->get("Fund");
						DataProvider::$fund->set("Fund", $fund + 500);
						DataProvider::$fund->save();
						$sender->reduceBalance(500);
						$sender->sendMessage(TE::RED . "§c§l- 500$");
						Server::getInstance()->broadcastMessage("§r§e§l<§6X§e> §r§7{$sender->getName()} has donated §6§l500$ §r§7to the World Fund.");
						break;
					}
				}
		}
		return true;
	}
}