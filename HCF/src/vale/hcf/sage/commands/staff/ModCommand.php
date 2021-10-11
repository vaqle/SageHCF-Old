<?php
namespace vale\hcf\sage\commands\staff;

use pocketmine\command\PluginCommand;
use pocketmine\command\{CommandSender, Command};
use pocketmine\level\sound\AnvilFallSound;
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

class ModCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct("mod", $plugin);
		$this->setPermission("sage.staff.cmd");
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§eENABLE §ror §l§eDISABLE §rmoderation mode.");

	}

	public function execute(CommandSender $sender, string $label, array $args): bool
	{
		if($sender instanceof SagePlayer) {
			if (count($args) === 0 && $sender instanceof SagePlayer){
				$sender->sendMessage("§6§l* §r§e/mod on §r§7((enables moderation mode))");
				Sage::getFactionsManager()->addCrystals($sender->getFaction(),199);
				$sender->sendMessage("§e§l* §r§6/mod off §r§7((disables moderation mode))");
				return false;
			}
		}

		if(!$sender->hasPermission("sage.staff.cmd")){
			return false;
		}

		switch ($args[0]) {
			case "on":
				if(!$sender->isStaffMode()){
					$sender->enterStaffMode();
				}elseif ($sender->isStaffMode()){
					$sender->sendMessage("§l§c[!] §r§cIt appears you already have staffmode enabled “{$sender->getName()}”\n§7To disable this run /mod off. If you believe this is an error contact vaqle.");
					$sender->getLevel()->addSound(new AnvilFallSound($sender));
				}
				break;
			case "off":
				if($sender->isStaffMode()){
					$sender->exitStaffMode();
				}elseif (!$sender->isStaffMode()){
					$sender->sendMessage("§l§c[!] §r§cIt appears you already have staffmode disabled “{$sender->getName()}”\n§7To disable this run /mod off. If you believe this is an error contact vaqle.");
					$sender->getLevel()->addSound(new AnvilFallSound($sender));
				}
				break;
		}
		return true;
	}
}