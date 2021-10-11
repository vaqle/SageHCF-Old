<?php
declare(strict_types=1);
namespace vale\hcf\sage\commands\player;

use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use vale\hcf\sage\{partneritems\PItemManager,
	Sage,
	SagePlayer,
	system\messages\Messages,
	system\monthlycrates\util\CrateUtils,
	system\ranks\RankAPI};

use pocketmine\level\{sound\AnvilFallSound, Level};

use pocketmine\command\{
   CommandSender, PluginCommand
};
use vale\hcf\sage\provider\DataProvider;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class ReclaimCommand extends PluginCommand
{
	/** @var Sage */
	private $plugin;

	public function __construct(Sage $plugin)
	{
		parent::__construct("reclaim", $plugin);
		$this->plugin = $plugin;
		$this->setPermission("core.cmd.reclaim");
		$this->setUsage("/reclaim");
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§eREDEEM §rand §l§6CLAIM §ryour pending packages");
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if (isset($args[0]) && isset($args[1])) {
			$player = Sage::getInstance()->getServer()->getPlayerExact($args[1]);
			if ($sender->hasPermission("core.cmd.reclaim.reset") && $args[0] == "reset") {
				if ($player instanceof SagePlayer) {
					DataProvider::setReclaim($player->getName(), "false");
				   $sender->sendMessage("§r§cThe player §6§l" . $player->getName() . " §r§creclaim was reset.");
					return;
				} else $sender->sendMessage("§r§cThe player §6§l" . $args[1] . " §r§cwas not found in the database.");
			}
		}
		
		$name = $sender->getName();
		if ($sender instanceof SagePlayer) {
			if (DataProvider::getReclaim($sender->getName()) == "true") {
				$sender->sendMessage("§r§cYou already reclaimed your package.");
				return;
			}
			if (RankAPI::hasRank($sender, "Aesthete")) {
				$sender->sendMessage("§l§c[!] §r§cNo reclaim was found for the rank “Player”\n§7This is only available to donators. donate now to gain access");
				$sender->getLevel()->addSound(new AnvilFallSound($sender));
				return;
			} elseif (RankAPI::hasRank($sender, "Raven")) {
				DataProvider::setReclaim($sender->getName(), "true");
				CrateUtils::giveMonthlyCrate($sender, "june2021", (int)1);
				DataProvider::setLives($sender->getName(), 15);
				PItemManager::givePartnerPackage($sender,15);
				PItemManager::giveKeys($sender, "Haze", 15);
				PItemManager::giveKeys($sender, "Summer", 2);
				PItemManager::giveKeys($sender, "Aegis", 11);
				PItemManager::giveKeys($sender, "Sage", 7);
				PItemManager::giveKeys($sender, "Ability", 10);
				PItemManager::giveLootBox($sender, "Summer",3);
				PItemManager::giveLootBox($sender, "Sage",4);
				Server::getInstance()->broadcastMessage("$name §r§7has §r§6/reclaimed §r§7there §6§oRaven Rewards.");
				return;
			}elseif (RankAPI::hasRank($sender, "Sage")){
				DataProvider::setReclaim($sender->getName(), "true");
				CrateUtils::giveMonthlyCrate($sender, "june2021", (int)5);
				DataProvider::setLives($sender->getName(), 15);
				PItemManager::givePartnerPackage($sender,17);
				PItemManager::giveKeys($sender, "Haze", 20);
				PItemManager::giveKeys($sender, "Summer", 3);
				PItemManager::giveKeys($sender, "Aegis", 20);
				PItemManager::giveKeys($sender, "Sage", 16);
				PItemManager::giveKeys($sender, "Ability", 8);
				PItemManager::giveLootBox($sender, "Summer",5);
				PItemManager::giveLootBox($sender, "Sage",5);
				Server::getInstance()->broadcastMessage("$name §r§7has §r§5/reclaimed §r§7there §5§oSage Rewards.");
				return;
			}elseif (RankAPI::hasRank($sender, "Aegis")){
				DataProvider::setReclaim($sender->getName(), "true");
				CrateUtils::giveMonthlyCrate($sender, "june2021", (int)2);
				DataProvider::setLives($sender->getName(), 15);
				PItemManager::givePartnerPackage($sender,5);
				PItemManager::giveKeys($sender, "Haze", 7);
				PItemManager::giveKeys($sender, "Summer", 3);
				PItemManager::giveKeys($sender, "Aegis", 4);
				PItemManager::giveKeys($sender, "Sage", 2);
				PItemManager::giveKeys($sender, "Ability", 5);
				PItemManager::giveLootBox($sender, "Summer",6);
				PItemManager::giveLootBox($sender, "Sage",2);
				Server::getInstance()->broadcastMessage("$name §r§7has §r§b/reclaimed §r§7there §b§oAegis Rewards.");
				return;

			}elseif(RankAPI::hasRank($sender,"Booster")){
				DataProvider::setReclaim($sender->getName(), "true");
				CrateUtils::giveMonthlyCrate($sender, "june2021", (int)3);
				DataProvider::setLives($sender->getName(), 15);
				PItemManager::givePartnerPackage($sender,4);
				PItemManager::giveKeys($sender, "Haze", 7);
				PItemManager::giveKeys($sender, "Summer", 1);
				PItemManager::giveKeys($sender, "Aegis", 5);
				PItemManager::giveKeys($sender, "Sage", 3);
				PItemManager::giveKeys($sender, "Ability", 5);
				PItemManager::giveLootBox($sender, "Summer",1);
				PItemManager::giveLootBox($sender, "Sage",1);
				Server::getInstance()->broadcastMessage("$name §r§7has §r§d/reclaimed §r§7there §d§oBooster Rewards.");

				return;
			}elseif (RankAPI::hasRank($sender, "Cupid")){
				DataProvider::setReclaim($sender->getName(), "true");
				DataProvider::setLives($sender->getName(), 15);
				CrateUtils::giveMonthlyCrate($sender, "june2021", (int)1);
				PItemManager::givePartnerPackage($sender,11);
				PItemManager::giveKeys($sender, "Haze", 9);
				PItemManager::giveKeys($sender, "Summer", 5);
				PItemManager::giveKeys($sender, "Aegis", 3);
				PItemManager::giveKeys($sender, "Sage", 2);
				PItemManager::giveKeys($sender, "Ability", 4);
				PItemManager::giveLootBox($sender, "Summer",1);
				PItemManager::giveLootBox($sender, "Sage",10);
				Server::getInstance()->broadcastMessage("$name §r§7has §r§c/reclaimed §r§7there §c§oCupid Rewards.");
				return;
			}elseif (RankAPI::hasRank($sender, "Media")){
				DataProvider::setReclaim($sender->getName(), "true");
				DataProvider::setLives($sender->getName(), 15);
				PItemManager::givePartnerPackage($sender,15);
				PItemManager::giveKeys($sender, "Haze", 15);
				CrateUtils::giveMonthlyCrate($sender, "june2021", (int)3);
				PItemManager::giveKeys($sender, "Summer", 2);
				PItemManager::giveKeys($sender, "Aegis", 11);
				PItemManager::giveKeys($sender, "Sage", 7);
				PItemManager::giveKeys($sender, "Ability", 10);
				PItemManager::giveLootBox($sender, "Summer",3);
				PItemManager::giveLootBox($sender, "Sage",4);
				Server::getInstance()->broadcastMessage("$name §r§7has §r§d/reclaimed §r§7there §d§oMedia Rewards.");
				return;
			}elseif (RankAPI::hasRank($sender, "Famous")){
				DataProvider::setReclaim($sender->getName(), "true");
				DataProvider::setLives($sender->getName(), 15);
				PItemManager::givePartnerPackage($sender,15);
				PItemManager::giveKeys($sender, "Haze", 32);
				PItemManager::giveKeys($sender, "Summer", 3);
				CrateUtils::giveMonthlyCrate($sender, "june2021", (int)4);
				PItemManager::giveKeys($sender, "Aegis", 15);
				PItemManager::giveKeys($sender, "Sage", 11);
				PItemManager::giveKeys($sender, "Ability", 15);
				PItemManager::giveLootBox($sender, "Summer",7);
				PItemManager::giveLootBox($sender, "Sage",3);
				Server::getInstance()->broadcastMessage("$name §r§7has §r§d/reclaimed §r§7there §d§oFamous Rewards.");
				return;
			}elseif (RankAPI::hasRank($sender, "Partner")){
				DataProvider::setReclaim($sender->getName(), "true");
				DataProvider::setLives($sender->getName(), 15);
				PItemManager::givePartnerPackage($sender,15);
				CrateUtils::giveMonthlyCrate($sender, "june2021", (int)10);
				PItemManager::giveKeys($sender, "Haze", 15);
				PItemManager::giveKeys($sender, "Summer", 2);
				PItemManager::giveKeys($sender, "Aegis", 11);
				PItemManager::giveKeys($sender, "Sage", 7);
				PItemManager::giveKeys($sender, "Ability", 10);
				PItemManager::giveLootBox($sender, "Summer",3);
				PItemManager::giveLootBox($sender, "Sage",4);
				Server::getInstance()->broadcastMessage("$name §r§7has §r§d/reclaimed §r§7there §d§oPartner Rewards.");
				return;
			}elseif (RankAPI::hasRank($sender,"Trial")){
				DataProvider::setReclaim($sender->getName(), "true");
				DataProvider::setLives($sender->getName(), 15);
				CrateUtils::giveMonthlyCrate($sender, "june2021", (int)1);
				PItemManager::givePartnerPackage($sender,10);
				PItemManager::giveKeys($sender, "Haze", 12);
				PItemManager::giveKeys($sender, "Summer", 9);
				PItemManager::giveKeys($sender, "Aegis", 6);
				PItemManager::giveKeys($sender, "Sage", 3);
				PItemManager::giveKeys($sender, "Ability", 5);
				PItemManager::giveLootBox($sender, "Summer",2);
				PItemManager::giveLootBox($sender, "Sage",3);
				Server::getInstance()->broadcastMessage("$name §r§7has §r§c/reclaimed §r§7there §c§oTrial-Mod Rewards.");
				return;
			}elseif (RankAPI::hasRank($sender, "Mod")){
				DataProvider::setReclaim($sender->getName(), "true");
				DataProvider::setLives($sender->getName(), 15);
				CrateUtils::giveMonthlyCrate($sender, "june2021", (int)4);
				PItemManager::givePartnerPackage($sender,10);
				PItemManager::giveKeys($sender, "Haze", 12);
				PItemManager::giveKeys($sender, "Summer", 9);
				PItemManager::giveKeys($sender, "Aegis", 6);
				PItemManager::giveKeys($sender, "Sage", 3);
				PItemManager::giveKeys($sender, "Ability", 5);
				PItemManager::giveLootBox($sender, "Summer",2);
				PItemManager::giveLootBox($sender, "Sage",3);
				Server::getInstance()->broadcastMessage("$name §r§7has §r§c/reclaimed §r§7there §c§oMod Rewards.");
				return;
			}elseif (RankAPI::hasRank($sender, "Admin")){
				DataProvider::setReclaim($sender->getName(), "true");
				CrateUtils::giveMonthlyCrate($sender, "june2021", (int)10);
				DataProvider::setLives($sender->getName(), 15);

				PItemManager::givePartnerPackage($sender,32);
				PItemManager::giveKeys($sender, "Haze", 32);
				PItemManager::giveKeys($sender, "Summer", 9);
				PItemManager::giveKeys($sender, "Aegis", 32);
				PItemManager::giveKeys($sender, "Sage", 32);
				PItemManager::giveKeys($sender, "Ability", 32);
				PItemManager::giveLootBox($sender, "Summer",10);
				PItemManager::giveLootBox($sender, "Sage",10);
				Server::getInstance()->broadcastMessage("$name §r§7has §r§4/reclaimed §r§7there §4§oAdmin Rewards.");
				return;
			}
		}
	}
}