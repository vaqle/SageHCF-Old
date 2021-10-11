<?php

namespace vale\hcf\sage\commands\staff;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use vale\hcf\sage\partneritems\PItemListener;
use vale\hcf\sage\partneritems\PItemManager;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;
use vale\hcf\sage\system\bansystem\BanSystem;

class BlacklistCommand extends PluginCommand
{

	public $plugin;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct("blacklist", $plugin);
		$this->setPermission("staff.blacklist");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): void
	{
		if (!$sender instanceof SagePlayer) {
			return;
		}
		if (!$sender->hasPermission("staff.blacklist")) {
			return;
		}

		if (!isset($args[0])) {
			$sender->sendMessage("§r§cEnter a Valid Player Name.");
			return;
		}


		if (!isset($args[1])) {
			$sender->sendMessage("§r§cProvide a Reason.");
			return;
		}
		if (!is_string($args[1])) {
			$sender->sendMessage("§r§cProvide a Reason.");
			return;
		}


		if (($player = $sender->getServer()->getPlayerExact($args[0])) instanceof SagePlayer) {
			PItemListener::Lightning($sender);
			$name = array_shift($args);
			$reason =  strval(implode("", $args));
			$player->close("§r§l§4BLACKLISTED §r§cby §r§c{$sender->getName()}  §r§4for: $reason ", 1, 1);
			Sage::getInstance()->getServer()->broadcastMessage("§r§c{$name} " .  "§cwas §l§4BLACKLISTED §r§cby {$sender->getName()} for ". "§4". strval(implode("", $args)));
			$webhook = new Webhook("https://discord.com/api/webhooks/855901033687154748/p_WKFwRdjz6BhLMhb-dnEKyUxFDu0IynHOQulP5XwLtz6etybv2jwWXNxeveQ_azllnd");
			$message = new Message();
			$message->setUsername("Sage HCF Bans");
			$message->setContent(" **BLACKLISTED** \n Please Provide Proof For the ban.");
			$embed = new Embed();
			$embed->addField("FIELD ONE", "Player: {$name}");
			$embed->addField("FIELD TWO", "Reason: {$reason}");
			$embed->addField("FIELD THREE","Banned By: {$sender->getName()}");
			$message->addEmbed($embed);
			$webhook->send($message);
		} else {
			PItemListener::Lightning($sender);
			$name = array_shift($args);
			$sender->getServer()->getNameBans()->addBan($name, strval(implode("", $args)), null, $sender->getName());
			Sage::getInstance()->getServer()->broadcastMessage("§r§c{$name} " .  "§cwas §l§4BLACKLISTED §r§cby {$sender->getName()} for ". "§4". strval(implode("", $args)));
			$webhook = new Webhook("https://discord.com/api/webhooks/855901033687154748/p_WKFwRdjz6BhLMhb-dnEKyUxFDu0IynHOQulP5XwLtz6etybv2jwWXNxeveQ_azllnd");
			$message = new Message();
			$message->setUsername("Sage HCF Bans");
			$message->setContent(" **BLACKLISTED** \n Please Provide Proof For the ban.");
			$embed = new Embed();
			$reason =  strval(implode("", $args));
			$embed->addField("Player Name", "Player: {$name}");
			$embed->addField("Network Blacklist", "Reason: {$reason}");
			$embed->addField("Issuer","Banned By: {$sender->getName()}");
			$message->addEmbed($embed);
			$webhook->send($message);
		}
	}
}