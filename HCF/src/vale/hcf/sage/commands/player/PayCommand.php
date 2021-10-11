<?php
namespace vale\hcf\sage\commands\player;

use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\level\sound\Sound;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use vale\hcf\sage\{provider\DataProvider,
	Sage,
	SagePlayer,
	system\shop\ShopInventory,
	tasks\player\CyberAttackQueueTask};
use vale\hcf\sage\system\messages\Messages;
use pocketmine\command\PluginCommand;

class PayCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		parent::__construct("pay", $plugin);
		$this->plugin = $plugin;
		$this->setAliases(["send,sendto,sed"]);
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§ePAY  §rother players");
	}

	public
	function execute(CommandSender $sender, string $label, array $args)
	{

		if ($sender instanceof SagePlayer) {
			if (count($args) < 2) {
				$sender->sendMessage("§r§e(§6§lPay Help§r§e)");
				$sender->sendMessage("§r§6/§r§epay - §r§e[player] [amount]");
				return false;
			} elseif (($player = Server::getInstance()->getPlayer($args[0])) !== null && isset($args[1]) && is_numeric($args[1])) {
				if ($player instanceof SagePlayer) {
					$provider = Sage::getSQLProvider();
					if ($provider->getBalance($sender->getName()) >= (int)$args[1] && !is_float($args[1]) && $args[1] > 0) {
						$provider->reduceBalance($sender->getName(), $args[1]);
						$provider->addMoney($player->getName(), (int)$args[1]);
						$player->sendMessage("§l§a+ $" . (int)$args[1] . " §r§afrom {$sender->getName()}");
						$pk = new LevelSoundEventPacket();
						$pk->sound = 62;
						$pk->position = new Vector3($player->x, $player->y, $player->z);

					}
				} elseif (!$player instanceof SagePlayer) {
					$sender->sendMessage("§l§c(!) §r§cThe name of the player you entered is invalid or offline");
				}
			}
		}
		return true;
	}
}