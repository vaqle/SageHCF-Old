<?php

namespace vale\hcf\sage\tasks\player;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use vale\hcf\libaries\DateTimeFormat;
use vale\hcf\libaries\ScoreboardFactory;
use pocketmine\scheduler\Task;
use vale\hcf\sage\factions\FactionsManager;
use vale\hcf\sage\handlers\events\SotwHandler;
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\handlers\events\PlayerListener;
use vale\hcf\sage\system\deathban\Deathban;
use vale\hcf\sage\system\events\KillTheKingEvent;
use vale\hcf\sage\tasks\player\ArcherTagTask;
use vale\hcf\sage\floatingtext\TextManager;
use pocketmine\utils\TextFormat as TE;
use vale\hcf\sage\system\cooldowns\Cooldown;

class ScoreBoardTask extends Task
{

	public $player;

	public function __construct(SagePlayer $player)
	{
		$this->player = $player;
	}

	public function getFactionsHome(string $fac)
	{
		if (Sage::getFactionsManager()->isFaction($fac))
			if (Sage::getFactionsManager()->isHome($fac)) {
				$home = Sage::getFactionsManager()->getHome($fac);
				$coords = " : " . $home->getX() . " : " . $home->getY() . " : " . $home->getZ();
			}
		$home = Sage::getFactionsManager()->getHome($fac);
		$coords = " : " . $home->getX() . " : " . $home->getY() . " : " . $home->getZ();

		return $coords;
	}

	public function onRun(int $currentTick)
	{
		$player = $this->player;
		if (!$player->isOnline()) {
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			return;
		}
		/** @var ScoreboardFactory $api */
		$api = new ScoreboardFactory();
		$date = new DateTimeFormat();
		/** @var array $scoreboard */
		$scoreboard = [];

		$x = round($player->getX());
		$y = round($player->getY());
		$z = round($player->getZ());
		$scoreboard[] =  "§r§eCoords:§6 {$x} : {$y} : {$z}";

		if($player->isBard()){
			$scoreboard[] = "§r§bUltimate: " . "0.25";
			$scoreboard[] = "§r§bBard Energy:§r§c " .  $player->getBardEnergy() . ".0";
			$scoreboard[] = "§r§cBard Delay: " .  $player->getBardDelay() . ".0";
		}

		if ($player->isCombatTagged()){
			$scoreboard[] = (TextFormat::RED . "§r§4Combat Tag:§r§c " . Sage::IntToString($player->getCombatTagTime()));
		}

		if(Deathban::isDeathBanned($player)){
			$lives = DataProvider::getLives($player->getName());
			$scoreboard[] = (TextFormat::RED . "Deathban: " . Sage::getTimeToFullString(Deathban::getDeathBanTime($player)) . "s");
			$scoreboard[] = "§6§lLives§r§6:§r§c ". $lives;
		}

		if(KillTheKingEvent::isEnabled()){
		
			$king = KillTheKingEvent::getKing();
			$scoreboard[] = KillTheKingEvent::getKingPosition(KillTheKingEvent::getKing());
			$scoreboard[] = "§r§6King§r§6: §c{$king}";
			$scoreboard[] = "§r§eTime§r§6: §c". Sage::getTimeToFullString(KillTheKingEvent::getTime());
		}

		if($player->isEnderPearl()){
			$scoreboard[] = (TextFormat::RED . "§r§2Ender Pearl:§r§c " . Sage::IntToString($player->getEnderPearlTime()));
		}

		if($player->isArcherTagged()){
			$scoreboard[] = (TextFormat::RED . "§r§aArcher Tag:§r§c " . Sage::IntToString($player->getArchertagTime()));

		}

		if (SotwHandler::isEnable()) {
			$scoreboard[] = "§r§6§lSOTW§r§6: " . "§r§e" . Sage::getTimeToFullString(SotwHandler::getTime());
		}

		if ($player->hasPvpTimer()) {
			$scoreboard[] = "§r§6§lPvP Timer§r§6: " . "§r§e" . Sage::getTimeToFullString($player->getPvpTimer());
		}

		if (isset(Cooldown::$antiTrapBeacon[$player->getName()]) && ($cooldown = (time() - Cooldown::$antiTrapBeacon[$player->getName()])) < 300) {
			$scoreboard[] = "§r§3§lAntiBeacon§r§7:§r§c " . (300 - $cooldown) . "s";
		}

		if($player->isTeleporting()){
			if($player->getTeleport() === SagePlayer::HOME){
			$time = Sage::getTimeToFullString($player->getTeleportTime());
			$scoreboard[] = "§r§6§l/F HOME§r§6: §r§e{$time}";
		}elseif ($player->getTeleport() === SagePlayer::STUCK){
				$time = Sage::getTimeToFullString($player->getTeleportTime());
				$scoreboard[] = "§r§e§l/F STUCK§r§e: §r§6{$time}";
			}
		}

		if ($player->isFocusing()) {
			$coords = "not set";
			$focusing = $player->getFocusedFaction();
			if (Sage::getFactionsManager()->isFaction($focusing)) {
				$dtr = Sage::getFactionsManager()->getDTR($focusing);
				$count = count(Sage::getFactionsManager()->getMembers($focusing));
				$scoreboard[] = "§r§6§lFocusing:";
				$scoreboard[] = " §r§e- " . $focusing;
				$scoreboard[] = "§r§6§l* §r§7DTR: {$dtr}";
				$scoreboard[] = "§r§e§l* §r§7Members: {$count}";
				if (Sage::getFactionsManager()->isHome($focusing)) {
					$home = Sage::getFactionsManager()->getHome($focusing);
					$coords = $home->getX() . ":" . $home->getY() . ":" . $home->getZ();
				}
				$scoreboard[] = "§r§6§l* §r§7HQ: {$coords}";
			}
		}
		$scoreboard[] = "§r§7sagehcf.club";
		$api->newScoreboard($player, $player->getName(), "§r§6§lSage §r§8(§r§7Troops #1§r§8)");
		if ($api->getObjectiveName($player) !== null) {
			foreach ($scoreboard as $line => $key) {
				$api->remove($player, $scoreboard);
				$api->newScoreboard($player, $player->getName(), "§r§6§lSage §r§8(§r§7Troops #1§r§8)");
			}
		}
		foreach ($scoreboard as $line => $key) {
			$api->setLine($player, $line + 1, $key);
		}
	}
}