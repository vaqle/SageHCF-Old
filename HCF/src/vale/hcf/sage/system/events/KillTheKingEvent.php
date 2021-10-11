<?php

namespace vale\hcf\sage\system\events;


use libs\utils\Utils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Server;
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\ranks\RankAPI;

class KillTheKingEvent implements Listener
{

	public static string $king = "";
	public static $isEnabled = false;
	public static $time = 9000;


	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	public static function setEnabled(bool $isEnabledBool)
	{
		self::$isEnabled = $isEnabledBool;
	}

	public static function isEnabled(): bool
	{
		if (self::$isEnabled === true) {
			return true;
		} else {
			return false;
		}
	}


	public function onDeath(PlayerDeathEvent $event)
	{
		$player = $event->getPlayer();
		$cause = $player->getLastDamageCause();
		if ($cause instanceof EntityDamageByEntityEvent) {
			if ($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
				$cause = $cause->getDamager();
				if ($cause instanceof SagePlayer && $player instanceof SagePlayer){
					if (KillTheKingEvent::isKing($cause)) {
						Server::getInstance()->broadcastMessage("§r§eThe §6§lKing §r§ehas killed §r§7{$player->getName()}");
					}
					if (KillTheKingEvent::isKing($player)) {
						Server::getInstance()->broadcastMessage("§r§eThe §6§lKing §r§ewas killed by §r§7{$cause->getName()}");
						Server::getInstance()->broadcastMessage("§r§6§l{$cause->getName()} §r§ehas won the §6§lKill The King Event.");
						RankAPI::setRank($cause, "Cupid");
						self::setKing(null);
					}
				}

			}
		}
	}



	public static function getKingPosition(string $name): string
	{
		$name = Server::getInstance()->getPlayer($name);
		if ($name !== null) {
			if ($name->isOnline() && $name instanceof SagePlayer) {
				$x = round($name->getX());
				$y = round($name->getY());
				$z = round($name->getZ());
				return "§r§eCoords:§6 {$x} : {$y} : {$z}";
			} else {
				return "Offline";
			}
		}
		return false ?? "Null??";
	}


	public static function getTime(): int{
		if(self::$time <= 1) {
			self::setEnabled(false);
			$ok = self::getKing();
			Sage::getInstance()->getServer()->broadcastMessage("§r§e* §r§6§lKill The King Event§r§e*\n §r§6Has ended. \n §r§7The winner is {$ok}");
			$player = Server::getInstance()->getPlayer(self::$king);
			if ($player instanceof SagePlayer) {
				RankAPI::setRank($player, "Cupid");
			}
		}
		return self::$time;
	}


	public static function getKing(): string
	{
		$king = self::$king;
		$player = Sage::getInstance()->getServer()->getPlayer($king);
		if($player == null){
			self::setEnabled(false);
			Sage::getInstance()->getServer()->broadcastMessage("§r§e* §r§6§lKill The King Event§r§e*\n §r§6Has ended.");
		}

		if(is_null($player)){
			self::setEnabled(false);
			Sage::getInstance()->getServer()->broadcastMessage("§r§e* §r§6§lKill The King Event§r§e*\n §r§6Has ended.");
		}

		return self::$king;
	}

	public static function setKing(SagePlayer $player)
	{
		self::setEnabled(true);
		self::$king = $player->getName();
		KitManager::giveKit($player, "King");
		Server::getInstance()->broadcastMessage("§r§e* §r§6§lKill The King Event§r§e* \n  §r§e* §r§7A §r§eKill The King Event §r§7has taken place on §r§6§lSage.  §r§7First person to kill the King Recieves §6§lRewards. \n §r§6King: §r§e{$player->getName()}");
	}

	public static function isKing(SagePlayer $player): bool{
		if(self::$king === $player->getName()){
			return true;
		}else{
			return false;
		}
	}
}