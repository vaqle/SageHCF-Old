<?php
declare(strict_types=1);

namespace vale\hcf\sage\system\classes;

use vale\hcf\sage\models\entitys\TextEntity;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;
use pocketmine\{item\Item, item\ItemIds, event\Listener, scheduler\Task, utils\TextFormat};
use pocketmine\entity\{
	Effect, EffectInstance
};
use pocketmine\event\player\{
	PlayerInteractEvent, PlayerItemHeldEvent
};


class BardClass implements Listener
{

	public $plugin;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * @param PlayerInteractEvent $event
	 */
	public function onTap(PlayerInteractEvent $event)
	{
		$player = $event->getPlayer();
		$manager = Sage::getFactionsManager();
		if ($player instanceof SagePlayer) {
			$item = $event->getItem();
			if ($player->isBard()) {
				$energy = $player->getBardEnergy();
				$delay = $player->getBardDelay();
				$inv = $player->getInventory();
				switch ($item->getId()) {
					case ItemIds::SUGAR:
						if (!$energy <= 20 && $delay !== 0) {
							$player->sendMessage("§r§cYou have §c§l{$energy} Energy §r§cand Your Bard Delay is §c§l{$delay}");
						}
						if ($energy >= 20 && $delay == 0) {
							if ($player->isInFaction()) {
								$mem = $manager->getOnlineMembers($player->getFaction());
								foreach ($mem as $member) {
									$distance = $member->distanceSquared($player);
									if ($distance <= 144) {
										$member->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 20 * 8, 2));
									}
								}
							}
							$player->setBardDelay(6);
							$item->pop();
							$inv->setItemInHand($item);
							$player->setBardEnergy($energy - 20);
							$player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 20 * 8, 1));
						}
						break;

					case ItemIds::FEATHER:
						if (!$energy <= 20 && $delay !== 0) {
							$player->sendMessage("§r§cYou have §c§l{$energy} Energy §r§cand Your Bard Delay is §c§l{$delay}");
						}
						if ($energy >= 20 && $delay == 0) {
							if ($player->isInFaction()) {
								$mem = $manager->getOnlineMembers($player->getFaction());
								foreach ($mem as $member) {
									$distance = $member->distanceSquared($player);
									if ($distance <= 144) {
										$member->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP_BOOST), 20 * 8, 3));
									}
								}
							}
							$item->pop();
							$player->setBardDelay(4);
							$inv->setItemInHand($item);
							$player->setBardEnergy($energy - 20);
							$player->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP_BOOST), 20 * 8, 3));
						}
						break;

					case ItemIds::IRON_INGOT:
						if (!$energy <= 20 && $delay !== 0) {
							$player->sendMessage("§r§cYou have §c§l{$energy} Energy §r§cand Your Bard Delay is §c§l{$delay}");
						}
						if ($energy >= 20 && $delay == 0) {
							if ($player->isInFaction()) {
								$mem = $manager->getOnlineMembers($player->getFaction());
								foreach ($mem as $member) {
									$distance = $member->distanceSquared($player);
									if ($distance <= 144) {
										$member->addEffect(new EffectInstance(Effect::getEffect(Effect::RESISTANCE), 20 * 8, 2));
									}
								}
							}
							$item->pop();
							$player->setBardDelay(6);
							$inv->setItemInHand($item);
							$player->setBardEnergy($energy - 20);
							$player->addEffect(new EffectInstance(Effect::getEffect(Effect::RESISTANCE), 20 * 8, 2));
						}
						break;

					case ItemIds::GHAST_TEAR:
						if (!$energy <= 30 && $delay !== 0) {
							$player->sendMessage("§r§cYou have §c§l{$energy} Energy §r§cand Your Bard Delay is §c§l{$delay}");
						}
						if ($energy >= 30 && $delay == 0) {
							if ($player->isInFaction()) {
								$mem = $manager->getOnlineMembers($player->getFaction());
								foreach ($mem as $member) {
									$distance = $member->distanceSquared($player);
									if ($distance <= 144) {
										$member->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 8, 1));
									}
								}
							}
							$item->pop();
							$inv->setItemInHand($item);
							$player->setBardDelay(7);
							$player->setBardEnergy($energy - 30);
							$player->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 8, 1));
						}
						break;

					case ItemIds::BLAZE_POWDER:
						if (!$energy <= 40 && $delay !== 0) {
							$player->sendMessage("§r§cYou have §c§l{$energy} Energy §r§cand Your Bard Delay is §c§l{$delay}");
						}
						if ($energy >= 40 && $delay == 0) {
							if ($player->isInFaction()) {
								$mem = $manager->getOnlineMembers($player->getFaction());
								foreach ($mem as $member) {
									$distance = $member->distanceSquared($player);
									if ($distance <= 144) {
										$member->addEffect(new EffectInstance(Effect::getEffect(Effect::STRENGTH), 20 * 8, 1));
									}
								}
							}
							$item->pop();
							$inv->setItemInHand($item);
							$player->setBardDelay(8);
							$player->setBardEnergy($energy - 40);
							$player->addEffect(new EffectInstance(Effect::getEffect(Effect::STRENGTH), 20 * 8, 1));
						}
						break;

				}
			}
		}
	}
}