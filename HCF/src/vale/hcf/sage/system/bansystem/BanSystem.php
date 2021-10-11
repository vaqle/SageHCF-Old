<?php

namespace vale\hcf\sage\system\bansystem;

use pocketmine\event\inventory\FurnaceBurnEvent;
use pocketmine\Server;
use vale\hcf\sage\SagePlayer;

class BanSystem
{

	public $reason = "";

	public $bannedBy = "";

	 const BAN_MESSAGE = "§cwas §l§4BLACKLISTED §r§cby Joe for §4";

	public $bannedPlayer = "";


	public function __construct(string $bannedPlayer, string $bannedBy, string $banMessage, string $reason){
		$this->bannedPlayer = $bannedPlayer;
		$this->bannedBy = $bannedBy;
		$this->banMessage = $banMessage;
		$this->reason = $reason;
	}

	public function announceBan(){
		$player = Server::getInstance()->getPlayer($this->bannedPlayer);
		$player->setBanned(true);
		$reason = $this->getReason();
		$bannedBy = $this->getBannedBy();
		Server::getInstance()->broadcastMessage("§r§c"."{$player->getName()}" . " §r§cwas §4§lBLACKLISTED §r§cby " . $bannedBy . " §r§cfor§4 " . $reason . ".");
		$player->kill();
		$player->kick("§c§lYOU ARE BLACKLISTED \n §r§4By:{$bannedBy} \n §r§4§lREASON: §r§c{$reason} \n §r§cAppeal: https://discord.gg/W6qceFRFYf");
		$player->setBanned(true);
	}


	/**
	 * @param string $reason
	 */
	public function setReason(string $reason): void
	{
		$this->reason = $reason;
	}

	/**
	 * @param string $bannedBy
	 */
	public function setBannedBy(string $bannedBy): void
	{
		$this->bannedBy = $bannedBy;
	}


	public function setBannedPlayer(string $bannedPlayer): void
	{
		$this->bannedPlayer = $bannedPlayer;
	}

	/**
	 * @param string $banMessage
	 */
	public function setBanMessage(string $banMessage): void
	{
		$this->banMessage = $banMessage;
	}

	/**
	 * @return string
	 */
	public function getBannedBy(): string
	{
		return $this->bannedBy;
	}


	public function getBannedPlayer() : string
	{
		return $this->bannedPlayer;
	}

	/**
	 * @return string
	 */
	public function getBanMessage(): string
	{
		return $this->banMessage;
	}

	/**
	 * @return string
	 */
	public function getReason(): string
	{
		return $this->reason;
	}

}

