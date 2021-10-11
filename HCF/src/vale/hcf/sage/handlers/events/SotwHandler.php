<?php

namespace vale\hcf\sage\handlers\events;

use pocketmine\event\Listener;
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\Server;
use vale\hcf\sage\factions\FactionsManager;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\tasks\player\StartOfTheWorldTask;
use vale\hcf\sage\Sage;
use vale\hcf\sage\tasks\player\SotwAntiLagTask;

class SotwHandler implements Listener
{

	/** @var Sage */
	protected $plugin;

	/** @var bool */
	protected static $enable = false;

	/** @var Int */
	protected static $time = 0;

	/**
	 * SOTW Constructor.
	 * @param Sage $plugin
	 */
	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * @return bool
	 */
	public static function isEnable(): bool
	{
		return self::$enable;
	}

	/**
	 * @param bool $enable
	 */
	public static function setEnable(bool $enable)
	{
		self::$enable = $enable;
	}

	/**
	 * @param Int $time
	 */
	public static function setTime(int $time)
	{
		self::$time = $time;
	}

	/**
	 * @return Int
	 */
	public static function getTime(): int
	{
		return self::$time;
	}

	/**
	 * @return void
	 */
	public static function start(int $time = 60): void
	{
		self::setEnable(true);
		Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new StartOfTheWorldTask($time), 20);
		Server::getInstance()->broadcastMessage("§r§6§lSOTW HAS COMMENCED");
		Server::getInstance()->broadcastMessage("§r§7((WHILE §6§lSOTW §r§7IS ENABLED IF YOUR IN SPAWN YOU MAY NOT SEE OTHER PLAYERS IF");
		Server::getInstance()->broadcastMessage("§r§7YOU WOULD LIKE TO §6§lSEE §r§7OTHER PLAYERS STEP OUT OF SPAWN TO SEE OTHER PLAYERS))");
		foreach (Server::getInstance()->getOnlinePlayers() as $player) {
			if ($player instanceof SagePlayer) {
				Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new SotwAntiLagTask($player), 20);
			}
		}
	}


	/**
	 * @return void
	 */
	public static function stop(): void
	{
		self::setEnable(false);
	}


	public function disableSuffocation(EntityDamageEvent $event)
	{
		$player = $event->getEntity();
		$cause = $event->getCause();
		if ($player instanceof SagePlayer && $cause === EntityDamageEvent::CAUSE_SUFFOCATION) {
			$event->setCancelled(true);
		}
	}

	public function disableFallDamage(EntityDamageEvent $event)
	{
		$player = $event->getEntity();
		$cause = $event->getCause();
		if ($player instanceof SagePlayer && $cause === EntityDamageEvent::CAUSE_FALL) {
			if($player->hasPvpTimer()){
				$event->setCancelled(true);
			}
		}
	}
	
	public function disableFallDamage3(EntityDamageEvent $event)
	{
		$player = $event->getEntity();
		$cause = $event->getCause();
		if ($player instanceof SagePlayer && $cause === EntityDamageEvent::CAUSE_FALL && self::isEnable()) {
			$event->setCancelled(true);
		}elseif (Sage::getFactionsManager()->isSpawnClaim($player)){
			$event->setCancelled(true);
		}
	}

	/**
     * @param EntityDamageEvent $event
     * @return void
     */
    public function onEntityDamageEvent(EntityDamageEvent $event) : void
	{
		$player = $event->getEntity();
		if ($player instanceof SagePlayer) {
			if (self::isEnable()) {
				$event->setCancelled(true);
			}
		}
	}
}