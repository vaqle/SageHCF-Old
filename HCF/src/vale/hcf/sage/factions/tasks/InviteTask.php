<?php

/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 16/08/2017
 * Time: 22:12
 */


namespace vale\hcf\sage\factions\tasks;

use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;


class InviteTask extends Task {

	private $plugin;

	private $time = 30;

	private $player;


	public function __construct(Sage $owner, SagePlayer $player) {

		$this->setPlugin($owner);

		$this->setPlayer($player);

		$this->setHandler($this->getPlugin()->getScheduler()->scheduleRepeatingTask($this, 20));

	}


	/**
	 * @return Sage
	 */

	public function getPlugin() : Sage {

		return $this->plugin;

	}


	/**
	 * @param Sage $plugin
	 */

	public function setPlugin(Sage $plugin) {

		$this->plugin = $plugin;

	}


	/**
	 * Actions to execute when run
	 *
	 * @param int $currentTick
	 *
	 * @return void
	 */

	public function onRun(int $currentTick) {

		$this->setTime($this->getTime() - 1);

		if($this->getTime() == 0) {

			$this->cancel();

			$this->getPlayer()->setInvited(false);

			$this->getPlayer()->sendMessage("§r§cThe Faction Invite From §6§l ".$this->getPlayer()->getLastinvite()." §r§chas expired!");

		}

	}


	/**
	 * @return int
	 */

	public function getTime() : int {

		return $this->time;

	}


	/**
	 * @param int $time
	 */

	public function setTime(int $time) {

		$this->time = $time;

	}


	/**
	 *

	 */

	public function cancel() {

		$this->getHandler()->cancel();

	}


	/**
	 * @return SagePlayer
	 */

	public function getPlayer() : SagePlayer {

		return $this->player;

	}


	/**
	 * @param SagePlayer $player
	 */

	public function setPlayer(SagePlayer $player) {

		$this->player = $player;

	}

}