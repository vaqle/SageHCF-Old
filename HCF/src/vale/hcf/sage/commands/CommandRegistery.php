<?php

namespace vale\hcf\sage\commands;


use vale\hcf\sage\commands\player\BalanceCommand;
use vale\hcf\sage\commands\player\BalTopCommand;
use vale\hcf\sage\commands\player\BlockShopCommand;
use vale\hcf\sage\commands\player\LivesCommand;
use vale\hcf\sage\commands\player\Fix;
use vale\hcf\sage\commands\player\PayCommand;
use vale\hcf\sage\commands\player\SendLivesCommand;
use vale\hcf\sage\commands\player\SupportersCommand;
use vale\hcf\sage\commands\player\TagCommand;
use vale\hcf\sage\commands\player\VoteCommand;
use vale\hcf\sage\commands\staff\BlacklistCommand;
use vale\hcf\sage\commands\staff\DoubleVoteRewardsCommand;
use vale\hcf\sage\commands\player\FundCommand;
use vale\hcf\sage\commands\staff\AreaCommand;
use vale\hcf\sage\commands\staff\KeyCommand;
use vale\hcf\sage\commands\staff\KingCommand;
use vale\hcf\sage\commands\staff\LastInventoryCommand;
use vale\hcf\sage\commands\staff\LootBoxCommand;
use vale\hcf\sage\commands\staff\ModCommand;
use vale\hcf\sage\commands\staff\MuteCommand;
use vale\hcf\sage\commands\staff\NotifyCommand;
use vale\hcf\sage\commands\staff\ReviveCommand;
use vale\hcf\sage\commands\staff\SotwCommand;
use vale\hcf\sage\commands\staff\UnblacklistCommand;
use vale\hcf\sage\commands\staff\WarnCommand;
use vale\hcf\sage\Sage;
use vale\hcf\sage\factions\cmds\FactionCommand;
use vale\hcf\sage\crates\cmds\CrateCommand;
use vale\hcf\sage\commands\player\ReclaimCommand;
use vale\hcf\sage\commands\staff\SetRankCommand;
use vale\hcf\sage\models\util\SpawnEntityCommand;
use pocketmine\command\Command;
use pocketmine\command\CommandMap;
use vale\hcf\sage\commands\staff\KeyAllCommand;
use vale\hcf\sage\system\events\KillTheKingEvent;
use vale\hcf\sage\waypoints\commands\WaypointCommand;
use vale\hcf\sage\commands\player\SkitCommand;
use vale\hcf\sage\commands\player\PvPCommand;
use vale\hcf\sage\commands\player\PingCommand;
use pocketmine\Server;

class CommandRegistery{
    
    public static function init(): void{
      $sage = Sage::getInstance();
      self::registerCommand(new WaypointCommand());
      $sage->getServer()->getCommandMap()->register("crate", new CrateCommand("crate", $sage));
	  $sage->getServer()->getCommandMap()->register("f", new FactionCommand($sage));
	  $sage->getServer()->getCommandMap()->register("setrank", new SetRankCommand("setrank",$sage));
      $sage->getServer()->getCommandMap()->register("reclaim", new ReclaimCommand($sage));
	  $sage->getServer()->getCommandMap()->register("se", new SpawnEntityCommand("se", $sage));
	  $sage->getServer()->getCommandMap()->register("sotw", new SotwCommand());
	  $sage->getServer()->getCommandMap()->register("area", new AreaCommand($sage));
	  $sage->getServer()->getCommandMap()->register("pay", new PayCommand($sage));
		$sage->getServer()->getCommandMap()->register("sendlives", new SendLivesCommand($sage));
	  $sage->getServer()->getCommandMap()->register("balance", new BalanceCommand($sage));
		$sage->getServer()->getCommandMap()->register("baltop", new BalTopCommand($sage));

		$sage->getServer()->getCommandMap()->register("fund", new FundCommand($sage));
	  $sage->getServer()->getCommandMap()->register("doublevote", new DoubleVoteRewardsCommand($sage));
	  $sage->getServer()->getCommandMap()->register("skit", new SkitCommand($sage));
		$sage->getServer()->getCommandMap()->register("supporters", new SupportersCommand($sage));
		$sage->getServer()->getCommandMap()->register("pvp", new PvPCommand($sage));
	   $sage->getServer()->getCommandMap()->register("pvp", new PingCommand($sage));
	  $sage->getServer()->getCommandMap()->register("blockshop", new BlockShopCommand($sage));
	  $sage->getServer()->getCommandMap()->register("vote", new VoteCommand($sage));
	  $sage->getServer()->getCommandMap()->register("tag", new TagCommand($sage));
	  $sage->getServer()->getCommandMap()->register("blacklist", new BlacklistCommand($sage));
	  $sage->getServer()->getCommandMap()->register("mod", new ModCommand($sage));
	  $sage->getServer()->getCommandMap()->register("key", new KeyCommand("key",$sage));
  $sage->getServer()->getCommandMap()->register("fix", new Fix("fix",$sage));
	  $sage->getServer()->getCommandMap()->register("lives", new LivesCommand($sage));
	  $sage->getServer()->getCommandMap()->register("revive", new ReviveCommand("revive",$sage));
	  $sage->getServer()->getCommandMap()->register("lootbox", new LootBoxCommand("lootbox",$sage));
	  $sage->getServer()->getCommandMap()->register("keyall", new KeyAllCommand($sage));
	  $sage->getServer()->getCommandMap()->register("lastinv", new LastInventoryCommand($sage));
	  #$sage->getServer()->getCommandMap()->register("killtheking", new KingCommand("killtheking",$sage));
		$sage->getServer()->getCommandMap()->register("unblacklist", new UnblacklistCommand("unblacklist",$sage));
		$sage->getServer()->getCommandMap()->register("notify", new NotifyCommand("notify",$sage));
		$sage->getServer()->getCommandMap()->register("warn", new WarnCommand("warn",$sage));
		$sage->getServer()->getCommandMap()->register("warn", new MuteCommand($sage));







		$sage->getLogger()->info("REGISTERED COMMANDS");
	  self::unregister("ban-ip");
	  self::unregister("me");
	  self::unregister("ver");
	  self::unregister("say");
	  self::unregister("pl");


	}

		public static function unregister(string $commands){
			$map = Server::getInstance()->getCommandMap();
			$command = $map->getCommand($commands);
			$map->unregister($command);
		}

   public static function registerCommand(Command $command): void {
        $commandMap = Sage::getInstance()->getServer()->getCommandMap();
        $existingCommand = $commandMap->getCommand($command->getName());
        if($existingCommand !== null) {
            $commandMap->unregister($existingCommand);
        }
        $commandMap->register($command->getName(), $command);
    }

}