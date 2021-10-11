<?php
declare(strict_types=1);
namespace vale\hcf\sage\factions\tasks;

use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\SmokeParticle;
use vale\hcf\sage\{Sage, SagePlayer};
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;

class TeleportTask extends Task {
	/** var Sage */
	private $plugin;
	private $time = 0;
	private $player;
	private $msg = "You’ve been teleported!";
	private $pos;
	private $currentpos;

	/**
	 * TeleportTask constructor.
	 *
	 * @param Sage $plugin
	 * @param SagePlayer $player
	 * @param string $message
	 * @param int $time
	 * @param Vector3 $pos
	 * @param Vector3 $currentpos
	 */
	public function __construct(Sage $plugin, SagePlayer $player, string $message, int $time = 1, Vector3 $pos, Vector3 $currentpos){
		$this->plugin = $plugin;
		$this->setPlayer($player);
		$this->setMessage($message);
		$this->setTime($time);
		$this->setPos($pos);
		$this->setCurrentPos($currentpos);
		$player->setTeleporting(true);
		Sage::getInstance()->getScheduler()->scheduleRepeatingTask($this, 20);
	}

	/**
	 * @param int $currentTick
	 * @return void
	 */
	public function onRun(int $currentTick): void {
		$svr = Sage::getInstance();
		$id = $this->getTaskId();
		$player = $this->getPlayer();
		if(!$player->isOnline()){
			$svr->getScheduler()->cancelTask($id);
			$player->setTeleporting(false);
			return;
		}
		$msg = $this->getMessage();
		$this->setTime($this->getTime() - 1);
		if($player->isOnline()) {
			$this->player->getLevel()->addParticle(new ExplodeParticle(new Vector3($player->getX() + rand(1, 3), $player->getY() + rand(1, 3), $player->getZ())));
			$this->player->getLevel()->addParticle(new SmokeParticle(new Vector3($player->getX() + rand(1, 3), $player->getY() + rand(1, 3), $player->getZ())));
			if (!$player->isTeleporting()) {
				$svr->getScheduler()->cancelTask($id);
				$player->setTeleporting(false);
				return;
			}
		}
		$nowpos = new Vector3((int) $player->getX(), (int) $player->getY(), (int) $player->getZ());
		$distance = $this->getCurrentPos()->distance($nowpos);
		if($player->getTeleport() == 1 && $distance >= 1){
			$player->setTeleporting(false);
			$player->sendMessage("§6§lINFORMATION");
			$player->sendMessage("§r§7((You moved causing the Teleporation Queue To Cancel))");
			$svr->getScheduler()->cancelTask($id);
		}
		if($player->getTeleport() == 2 && $distance >= 2){
			$player->setTeleporting(false);
			$player->sendMessage("§6§lINFORMATION");
			$player->sendMessage("§r§7((You moved causing the Teleporation Queue To Cancel))");
			$svr->getScheduler()->cancelTask($id);
		}
		if($this->getTime() == 0){
			$level = Sage::$overworldLevel;
			$player->teleport(new Position((int)$this->getPos()->x, (int)$this->getPos()->y, (int)$this->getPos()->z, Sage::getInstance()->getServer()->getLevelByName("hcfmap")));
			$player->sendMessage($msg);
			$player->setTeleporting(false);
			$svr->getScheduler()->cancelTask($id);
		}
	}
	/**
	 * @return int
	 */
	public function getTime(): int {
		return $this->time;
	}

	/**
	 * @param int $time
	 */
	public function setTime(int $time) {
		$this->time = $time;
	}
	/**
	 * @return string
	 */
	public function getMessage(): string {
		return $this->message;
	}
	/**
	 * @param string $message
	 */
	public function setMessage(string $message) {
		$this->message = $message;
	}
	/**
	 * @return mixed
	 */
	public function getPlayer(): SagePlayer {
		return $this->player;
	}

	/**
	 * @param mixed $player
	 */
	public function setPlayer(SagePlayer $player) {
		$this->player = $player;
	}

	/**
	 * @return Vector3
	 */
	public function getPos(): Vector3 {
		return $this->pos;
	}

	/**
	 * @param Vector3 $pos
	 */
	public function setPos(Vector3 $pos) {
		$this->pos = $pos;
	}

	/**
	 * @return Vector3
	 */
	public function getCurrentPos(): Vector3 {
		return $this->currentpos;
	}
	/**
	 * @param Vector3 $currentpos
	 */
	public function setCurrentPos(Vector3 $currentpos) {
		$this->currentpos = $currentpos;
	}
}