<?php
declare(strict_types=1);
namespace vale\hcf\sage\tasks\player;

use pocketmine\scheduler\Task;
use pocketmine\{
	item\Item,
	item\ItemIds,
	entity\Effect,
	entity\EffectInstance};
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class BardTask extends Task
{
	private $plugin;

	/**
	 * SetsTask constructor.
	 *
	 * @param Sage $plugin
	 */
	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * @param int $currentTick
	 * @return void
	 */
	public function onRun(int $currentTick)
	{
		$mgr = Sage::getFactionsManager();
		$server = $this->plugin->getServer();
		$players = $server->getOnlinePlayers();
		foreach ($players as $player) {
			if ($player instanceof SagePlayer) {
				if ($player->isBard()) {
					$item = $player->getInventory()->getItemInHand();
					if ($player->isInFaction()) {
						$f = $player->getFaction();
						$l = $mgr->getOnlineMembers($f);
						switch ($item->getId()) {
							case ItemIds::BLAZE_POWDER:
								foreach ($l as $member) {
									$distance = $member->distanceSquared($player);
									if ($distance <= 64) {
										$member->addEffect(new EffectInstance(Effect::getEffect(Effect::STRENGTH), 20 * 8, 0));
									}
								}
								break;

							case ItemIds::MAGMA_CREAM:

								foreach ($l as $member) {
									$distance = $member->distanceSquared($player);
									if ($distance <= 64) {
										$member->addEffect(new EffectInstance(Effect::getEffect(Effect::FIRE_RESISTANCE), 20 * 8, 0));
									}
								}
								break;


							case ItemIds::GHAST_TEAR:

								foreach ($l as $member) {
									$distance = $member->distanceSquared($player);
									if ($distance <= 64) {
										$member->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 8, 0));
									}
								}
								break;

							case ItemIds::SUGAR:

								foreach ($l as $member) {
									$distance = $member->distanceSquared($player);
									if ($distance <= 64) {
										$member->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 20 * 8, 1));
									}
								}
								break;

							case ItemIds::IRON_INGOT:

								foreach ($l as $member) {
									$distance = $member->distanceSquared($player);
									if ($distance <= 64) {
										$member->addEffect(new EffectInstance(Effect::getEffect(Effect::RESISTANCE), 20 * 8, 0));
									}
								}
								break;

							case ItemIds::DYE:
								foreach ($l as $member) {
									$distance = $member->distanceSquared($player);
									if ($distance <= 64) {
										$member->addEffect(new EffectInstance(Effect::getEffect(Effect::INVISIBILITY), 20 * 8, 2));
									}
								}
								break;

							case ItemIds::FEATHER:
								foreach ($l as $member) {
									$distance = $member->distanceSquared($player);
									if ($distance <= 64) {
										$member->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP), 20 * 8, 0));
									}
								}
								break;
						}
					}


					if ($player->getBardDelay() > 0) $player->setBardDelay($player->getBardDelay() - 1);
					if ($player->getBardEnergy() < 120) $player->setBardEnergy($player->getBardEnergy() + 2);
				}
			}
		}
	}
}