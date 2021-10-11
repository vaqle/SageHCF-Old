<?php

namespace vale\hcf\sage\partneritems\tasks;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use vale\hcf\sage\models\entitys\PortableBambeEntity;

class PortableBardTask extends Task{

	public $bard;
	public $player;
	public $btime = 60;

	public function __construct(SagePlayer $player, PortableBambeEntity $bard){
		$this->player = $player;
		$this->bard = $bard;
	}

	public function onRun(int $currentTick){
		--$this->btime;
		if(!$this->bard->isAlive()){
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}

		if(!$this->player->isOnline()){
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}

		if($this->btime === 50){
			if($this->bard->isAlive()){
				$this->player->sendMessage("§r§7You portable bard is still alive" . "\n" . "You portable bard currently has 75 §r§6§lEnergy" . "\n" .
					"§r§7((§6You recieved Resistance 3 and Regeneration for 7 seconds§r§7))");
				$this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::RESISTANCE), 20 * 8, 0));
				$this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 8, 1));

			}
		}
		if($this->btime === 35){
			if($this->bard->isAlive()){
				$this->player->sendMessage("§r§7You portable bard is still alive" . "\n" . "You portable bard currently has 50 §r§6§lEnergy" . "\n" .
					"§r§7((§6You recieved Strength 2 and Regeneration 3  for 7 seconds§r§7))");
				$this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 8, 2));
				$this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::STRENGTH), 20 * 8, 1));
			}
		}

		if($this->btime === 20){
			if($this->bard->isAlive()){
				$this->player->sendMessage("§r§7You portable bard is still alive" . "\n" . "You portable bard currently has 25 §r§6§lEnergy" . "\n" .
					"§r§7((§6You recieved Strength 2 and Invisbility for 5 seconds§r§7))");
				$this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::INVISIBILITY), 20 * 8, 0));
				$this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::STRENGTH), 20 * 8, 1));
			}
		}

		if($this->btime === 10){
			if($this->bard->isAlive()){
				$this->player->sendMessage("§r§7You portable bard is still alive" . "\n" . "You portable bard currently has 15 §r§6§lEnergy" . "\n" .
					"§r§7((§6You recieved Jumpboost 8 and Speed 3  for 5 seconds§r§7))");
				$this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP_BOOST), 20 * 8, 7));
				#$this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 20 * 8, 2));
			}
		}

		if($this->btime === 5){
			if($this->bard->isAlive()){
				$this->player->sendMessage("§r§7You portable bard is still alive" . "\n" . "You portable bard currently has 5 §r§6§lEnergy" . "\n" .
					"§r§7((§6You recieved Strength 2 and Regen for 5 seconds§r§7))");
				$this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::STRENGTH), 20 * 8, 1));
				$this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 8, 1));
			}
		}
		if($this->btime === 1){
			if($this->bard instanceof PortableBambeEntity){
				$this->bard->close();
			#	Server::getInstance()->broadcastMessage("entity closed.");
				Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			}
		}
	}



	public function getPlayer(): SagePlayer{
		return $this->player;
	}

	public function getBard(){
		return $this->bard;
	}

	public function setTime(int $time){
		$this->btime = $time;
	}

	public function getTime(): int{
		return $this->btime;
	}

}