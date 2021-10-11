<?php

declare(strict_types = 1);

namespace vale\hcf\sage\commands\player;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\level\sound\AnvilFallSound;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\tasks\player\VoteTask;

class VoteCommand extends PluginCommand {

	public $plugin;

	public function __construct(Sage $plugin)
	{
		parent::__construct("vote", $plugin);
		$this->plugin = $plugin;
		$this->setUsage("/vote");
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§eVOTE.");
	}
	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof SagePlayer) {
			$sender->sendMessage("§r§7((Apparently Your BaseClass isnt instanceof SagePlayer report to staff now.");
			return;
		}
		if($sender->hasVoted()) {
			$sender->sendMessage("§6§lINFORMATION");
			$sender->sendMessage("§r§7((Our Database Detects that you have already voted you can vote every 12 hours))");
			$sender->getLevel()->addSound(new AnvilFallSound($sender));
			return;
		}
		if($sender->isCheckingForVote()) {
			$sender->sendMessage("§6§lVOTING QUEUE");
			$sender->sendMessage("§r§7((Our Database is taking sometime to verify your vote please wait and rerun this command.))");
			return;
		}
		$this->plugin->getServer()->getAsyncPool()->submitTaskToWorker(new VoteTask($sender->getName()), 1);
		return;
	}
}