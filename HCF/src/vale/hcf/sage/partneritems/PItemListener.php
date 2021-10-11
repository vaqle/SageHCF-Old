<?php

namespace vale\hcf\sage\partneritems;

use libs\utils\fireworks\Fireworks;
use libs\utils\Utils;
use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use vale\hcf\sage\models\entitys\TextEntity;
use vale\hcf\sage\partneritems\tasks\FocusedPlayerTask;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Position;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use vale\hcf\sage\factions\FactionsManager;
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\models\entitys\EndermiteEntity;
use vale\hcf\sage\models\entitys\FakeLogger;
use vale\hcf\sage\models\entitys\PlayerLogger;
use vale\hcf\sage\models\entitys\SwapBallEntity;
use vale\hcf\sage\partneritems\tasks\GuardianAttackTask;
use vale\hcf\sage\partneritems\tasks\LoggerBaitTask;
use vale\hcf\sage\partneritems\tasks\NinjaStarTask;
use vale\hcf\sage\partneritems\tasks\PitemCooldownTask;
use vale\hcf\sage\partneritems\tasks\PortableBardTask;
use vale\hcf\sage\models\entitys\PortableBambeEntity;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\partneritems\tasks\LassoTask;
use vale\hcf\sage\system\cooldowns\Cooldown;
use vale\hcf\sage\partneritems\tasks\AntiTrapBeaconTask;
USE vale\hcf\sage\tasks\player\CombatTagTask;
class PItemListener implements Listener
{

	public $plugin;

	public $pitemCd = [];

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}


	/*public function antiTrapBeaconInteract(PlayerInteractEvent $event)
	{
		$player = $event->getPlayer();
		$item = $event->getItem();
		$block = $event->getBlock();
		if ($event->getBlock()->getId() === Block::FENCE_GATE or Block::TRAPDOOR or Block::WOODEN_TRAPDOOR or Block::OAK_DOOR_BLOCK) {
			if (isset(Cooldown::$antiTrapped[$player->getName()])) {
				if (Cooldown::$antiTrapped[$player->getName()] <= time()) {
					unset(Cooldown::$antiTrapped[$player->getName()]);
				} else {
					if (isset(Cooldown::$antiTrapped[$player->getName()])) {
						$seconds = Cooldown::$antiTrapped[$player->getName()] - time();
						$player->sendMessage("§r§cYou cannot use this for another §r§c§l{$seconds}§r§c seconds.");
						$event->setCancelled(true);
					}
				}
			}
		}
	}*/


	public function antiPlacedBeacon(BlockPlaceEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			$inhand = $player->getInventory()->getItemInHand();
			if (isset(Cooldown::$antiTrapped[$player->getName()])) {
				if (Cooldown::$antiTrapped[$player->getName()] <= time()) {
					unset(Cooldown::$antiTrapped[$player->getName()]);
				} else {
					if (isset(Cooldown::$antiTrapped[$player->getName()])) {
						$seconds = Cooldown::$antiTrapped[$player->getName()] - time();
						$player->sendMessage("§r§cYou cannot use this for another §r§c§l{$seconds}§r§c seconds.");
						$event->setCancelled(true);
					}
				}
			}
		}
	}


	public function antiPlaceBone(BlockPlaceEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			$inhand = $player->getInventory()->getItemInHand();
			if (isset(Cooldown::$bonedTime[$player->getName()])) {
				if (Cooldown::$bonedTime[$player->getName()] <= time()) {
					unset(Cooldown::$bonedTime[$player->getName()]);
				} else {
					if (isset(Cooldown::$bonedTime[$player->getName()])) {
						$seconds = Cooldown::$bonedTime[$player->getName()] - time();
						$player->sendMessage("§r§cYou cannot use this for another §r§c§l{$seconds}§r§c seconds.");
						$event->setCancelled(true);
					}
				}
			}
		}
	}


	public function antiBreak(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			if (isset(Cooldown::$antiTrapped[$player->getName()])) {
				if ($event->getBlock()->getId() !== Block::MELON_BLOCK) {
					if (Cooldown::$antiTrapped[$player->getName()] <= time()) {
						unset(Cooldown::$antiTrapped[$player->getName()]);
					} else {
						if (isset(Cooldown::$antiTrapped[$player->getName()])) {
							$time = Cooldown::$antiTrapped[$player->getName()] - time();
							$player->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
							$event->setCancelled(true);
						}
					}
				}
			}
		}
	}

	public function antiBuildBone(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			if (isset(Cooldown::$bonedTime[$player->getName()])) {
				if ($event->getBlock()->getId() !== Block::MELON_BLOCK) {
					if (Cooldown::$bonedTime[$player->getName()] <= time()) {
						unset(Cooldown::$bonedTime[$player->getName()]);
					} else {
						if (isset(Cooldown::$bonedTime[$player->getName()])) {
							$time = Cooldown::$bonedTime[$player->getName()] - time();
							$player->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
							$event->setCancelled(true);
						}
					}
				}
			}
		}
	}

	public function antiTrapr(BlockPlaceEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			$inhand = $player->getInventory()->getItemInHand();
			if ($inhand->getCustomName() === "§r§5§lMarios's Anti Trap Beacon" && $inhand->getNamedTag()->hasTag("PartnerItem_BEACON")) {
				if (!$player->isInFaction()) {
					$player->sendMessage("§r§7((§6§lINFO§r§7))");
					$player->sendMessage("§r§7To use the §r§5§lAnti Trap Beacon §r§7you must be in a faction. The claim you place this in.");
					$player->sendMessage("§r§7must not be your claim or must not be a claim by the Server. Ensure you are following the correct Steps.");
					$player->sendMessage("§6§lBETA PARTNER ITEM.");
					$player->getLevel()->addSound(new AnvilFallSound($player));
					$event->setCancelled(true);
				} elseif (isset(Cooldown::$antiTrapBeacon[$player->getName()])) {
					$cooldown = (time() - Cooldown::$antiTrapBeacon[$player->getName()]);
					if ($cooldown < 300) {
						$timer = time() - Cooldown::$antiTrapBeacon[$player->getName()];
						$time = 300 - $timer;
						$player->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
						$event->setCancelled(true);
					} elseif (Cooldown::$antiTrapBeacon[$player->getName()] <= time()) {
						unset(Cooldown::$antiTrapBeacon[$player->getName()]);
					}
				} elseif (!isset(Cooldown::$antiTrapBeacon[$player->getName()])) {
					if ($player->isInFaction()) {
						$arr = ["spawn", "Spawn", "Wilderness", "South Road", "West Road", "North Road", "West Road", "East Road", "Cyber Attack"];
						if (!in_array(Sage::getInstance()::getFactionsManager()->getClaimer($event->getBlock()->getX(), $event->getBlock()->getZ()), $arr)) {
							if (Sage::getFactionsManager()->isClaim($event->getBlock())) {
								$claim = Sage::getFactionsManager()->getClaimer($event->getBlock()->getX(), $event->getBlock()->getZ());
								if ($claim !== $player->getFaction()) {
									Sage::getFactionsManager()->createBeacon(new Vector3($event->getBlock()->getX(), $event->getBlock()->getY(), $event->getBlock()->getZ()));
									$event->setCancelled(true);
									Cooldown::$antiTrapBeacon[$player->getName()] = time();
									$x = $event->getBlock()->getX();
									$y = $event->getBlock()->getY();
									$z = $event->getBlock()->getZ();
									$level = $player->getLevel();
									$faction = $player->getFaction();
									Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new AntiTrapBeaconTask($player, $faction, $claim, $level, $event->getBlock()), 20);
								} else {
									$player->sendMessage("§r§7((§6§lINFO§r§7))");
									$player->sendMessage("§r§7To use the §r§5§lAnti Trap Beacon §r§7you must be in a faction. The claim you place this in.");
									$player->sendMessage("§r§7must not be your claim or must not be a claim by the Server. Ensure you are following the correct Steps.");
									$player->sendMessage("§6§lBETA PARTNER ITEM.");
									$player->getLevel()->addSound(new AnvilFallSound($player));
									$event->setCancelled(true);
								}
							} else {
								$player->sendMessage("§r§7((§6§lINFO§r§7))");
								$player->sendMessage("§r§7To use the §r§5§lAnti Trap Beacon §r§7you must be in a faction. The claim you place this in.");
								$player->sendMessage("§r§7must not be your claim or must not be a claim by the Server. Ensure you are following the correct Steps.");
								$player->sendMessage("§6§lBETA PARTNER ITEM.");
								$player->getLevel()->addSound(new AnvilFallSound($player));
								$event->setCancelled(true);
							}
						} else {
							$player->sendMessage("§r§7((§6§lINFO§r§7))");
							$player->sendMessage("§r§7To use the §r§5§lAnti Trap Beacon §r§7you must be in a faction. The claim you place this in.");
							$player->sendMessage("§r§7must not be your claim or must not be a claim by the Server. Ensure you are following the correct Steps.");
							$player->sendMessage("§6§lBETA PARTNER ITEM.");
							$player->getLevel()->addSound(new AnvilFallSound($player));
							$event->setCancelled(true);
						}
					}
				}
			}
		}
	}


	public function onEntityDamage(EntityDamageByEntityEvent $event)
	{
		$damager = $event->getDamager();
		$entity = $event->getEntity();
		if ($entity instanceof SagePlayer) {
			if ($damager instanceof SagePlayer) {
				$inHand = $damager->getInventory()->getItemInHand();
				if ($inHand->getCustomName() === "§r§c§lVales's Bone" && $inHand->getNamedTag()->hasTag("PartnerItem_Bone")) {
					if (!isset(Cooldown::$bonecd[$damager->getName()])) {
						if ($damager->getHits() >= 3) {
							Cooldown::$bonecd[$damager->getName()] = time();
							Cooldown::$bonedTime[$entity->getName()] = time() + 30;
							$damager->sendMessage("§r§c§l " . "§r§6You have succesfully hit §r§f" . $entity->getName() . " §r§6!");
							$damager->sendMessage("§r§c§l " . "§r§6You are now on a cooldown for §r§f300 seconds");
							$damager->setHits(0);

						} else {
							if (!isset(Cooldown::$bonecd[$damager->getName()])) {
								$damager->addHits(1);
							}
						}
					} else {
						$cooldown = (time() - Cooldown::$bonecd[$damager->getName()]);
						if ($cooldown < 300) {

							$timer = time() - Cooldown::$bonecd[$damager->getName()];
							$time = 300 - $timer;
							$damager->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
							$event->setCancelled(true);
						} elseif (Cooldown::$bonecd[$damager->getName()] <= time()) {
							unset(Cooldown::$bonecd[$damager->getName()]);
						}
					}
				}

				if ($inHand->getCustomName() === "§r§b§lBirdo's Lasso" && $inHand->getNamedTag()->hasTag("PartnerItem_Lasso")) {
					if (!Sage::getFactionsManager()->isSpawnClaim($damager)) {
						if (!isset(Cooldown::$lasso[$damager->getName()])) {
							if ($damager->getHits() >= 3) {
								Cooldown::$lasso[$damager->getName()] = time();
								$this->plugin->getScheduler()->scheduleRepeatingTask(new LassoTask($damager, $entity), 20);
								$damager->sendMessage("§r§c§l " . "§r§6You have succesfully hit §r§f" . $entity->getName() . " §r§6!");
								$damager->sendMessage("§r§c§l " . "§r§6You are now on a cooldown for §r§f300 seconds");
								$damager->setHits(0);

							} else {
								if (!isset(Cooldown::$lasso[$damager->getName()])) {
									$damager->addHits(1);
								}
							}
						} else {
							$cooldown = (time() - Cooldown::$lasso[$damager->getName()]);
							if ($cooldown < 300) {
								$timer = time() - Cooldown::$lasso[$damager->getName()];
								$time = 300 - $timer;
								$damager->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
								$event->setCancelled(true);
							} elseif (Cooldown::$lasso[$damager->getName()] <= time()) {
								unset(Cooldown::$lasso[$damager->getName()]);
							}
						}
					}
				}


				if ($inHand->getCustomName() === "§r§3§lFear's Decoy Device" && $inHand->getNamedTag()->hasTag("PartnerItem_DECOY")) {
					if (!isset(Cooldown::$decoy[$damager->getName()])) {
						if ($damager->getHits() >= 3) {
							Cooldown::$decoy[$damager->getName()] = time();
							$damager->sendMessage("§r§c§l " . "§r§6You have succesfully hit §r§f" . $entity->getName() . " §r§6!");
							$damager->sendMessage("§r§c§l " . "§r§6You are now on a cooldown for §r§f300 seconds");
							$inHand->setCount($inHand->getCount() - 1);
							$damager->getInventory()->setItemInHand($inHand);
							$gaurd = new EndermiteEntity($entity->getLevel(), Entity::createBaseNBT($entity->asVector3()));
							$gaurd->spawnToAll();
							$ev = new EntityDamageByEntityEvent($gaurd, $entity, EntityDamageByEntityEvent::CAUSE_ENTITY_ATTACK, 1);
							$this->plugin->getScheduler()->scheduleRepeatingTask(new GuardianAttackTask($gaurd, $entity, $ev, $damager), 20);
							$gaurd->attack($ev);
							$gaurd->attackspecifictARg($entity);
							$damager->setHits(0);
						} else {
							if (!isset(Cooldown::$decoy[$damager->getName()])) {
								$damager->addHits(1);
							}
						}
					} else {
						$cooldown = (time() - Cooldown::$decoy[$damager->getName()]);
						if ($cooldown < 220) {
							$timer = time() - Cooldown::$decoy[$damager->getName()];
							$time = 220 - $timer;
							$damager->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
							$event->setCancelled(true);
						} elseif (Cooldown::$decoy[$damager->getName()] <= time()) {
							unset(Cooldown::$decoy[$damager->getName()]);
						}
					}
				}

				if ($inHand->getCustomName() === "§r§4§lStick of Confusion" && $inHand->getNamedTag()->hasTag("PartnerItem_Stick")) {
					if (!isset(Cooldown::$stick[$damager->getName()])) {
						if ($damager->getHits() >= 3) {
							Cooldown::$stick[$damager->getName()] = time();
							$entity->teleport($entity->getPosition(), $entity->getYaw() + 180, 100);
							$damager->sendMessage("§r§c§l " . "§r§6You have succesfully hit §r§f" . $entity->getName() . " §r§6!");
							$damager->sendMessage("§r§c§l " . "§r§6You are now on a cooldown for §r§f300 seconds");
							$damager->setHits(0);
						} else {
							if (!isset(Cooldown::$decoy[$damager->getName()])) {
								$damager->addHits(1);
							}
						}
					} else {
						$cooldown = (time() - Cooldown::$stick[$damager->getName()]);
						if ($cooldown < 220) {
							$timer = time() - Cooldown::$stick[$damager->getName()];
							$time = 220 - $timer;
							$damager->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
							$event->setCancelled(true);
						} elseif (Cooldown::$stick[$damager->getName()] <= time()) {
							unset(Cooldown::$stick[$damager->getName()]);
						}
					}
				}


				if ($inHand->getCustomName() === "§r§e§lAnt's Focus Mode" && $inHand->getNamedTag()->hasTag("PartnerItem_Focus")) {
					if (!isset(Cooldown::$focusMode[$damager->getName()])) {
						if ($damager->getHits() >= 3) {
							Cooldown::$focusMode[$damager->getName()] = time();
							$damager->setFocusedPlayer($entity->getName());
							$this->plugin->getScheduler()->scheduleRepeatingTask(new FocusedPlayerTask($damager, $entity), 20);
							$damager->sendMessage("§r§c§l " . "§r§6You have succesfully hit §r§f" . $entity->getName() . " §r§6!");
							$damager->sendMessage("§r§c§l " . "§r§6You are now on a cooldown for §r§f300 seconds");
							$damager->setHits(0);
						} else {
							if (!isset(Cooldown::$focusMode[$damager->getName()])) {
								$damager->addHits(1);
							}
						}
					} else {
						$cooldown = (time() - Cooldown::$focusMode[$damager->getName()]);
						if ($cooldown < 220) {
							$timer = time() - Cooldown::$focusMode[$damager->getName()];
							$time = 220 - $timer;
							$damager->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
							$event->setCancelled(true);
						} elseif (Cooldown::$focusMode[$damager->getName()] <= time()) {
							unset(Cooldown::$focusMode[$damager->getName()]);
						}
					}
				}


				if ($inHand->getCustomName() === "§r§b§lCombo Ability" && $inHand->getNamedTag()->hasTag("PartnerItem_Combo")) {
					if (!isset(Cooldown::$comboAbility[$damager->getName()])) {
						if ($damager->getHits() >= 3) {
							Cooldown::$comboAbility[$damager->getName()] = time();
							$damager->addEffect(new EffectInstance(Effect::getEffect(Effect::STRENGTH), 20 * 10, 1));
							$damager->addEffect(new EffectInstance(Effect::getEffect(Effect::HASTE), 20 * 10, 2));
							#Cooldown::$comboed[$entity->getName()] = time() + 5;
							$damager->sendMessage("§r§c§l " . "§r§6You have succesfully hit §r§f" . $entity->getName() . " §r§6!");
							$damager->sendMessage("§r§c§l " . "§r§6You are now on a cooldown for §r§f300 seconds");
							$entity->sendMessage("§r§c§l " . "§r§6You will now take combo ladder KB");
							$inHand->setCount($inHand->getCount() - 1);
							$damager->getInventory()->setItemInHand($inHand);
							$damager->setHits(0);
						} else {
							if (!isset(Cooldown::$comboAbility[$damager->getName()])) {
								$damager->addHits(1);
							}
						}
					} else {
						$cooldown = (time() - Cooldown::$comboAbility[$damager->getName()]);
						if ($cooldown < 220) {
							$timer = time() - Cooldown::$comboAbility[$damager->getName()];
							$time = 220 - $timer;
							$damager->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
							$event->setCancelled(true);
						} elseif (Cooldown::$comboAbility[$damager->getName()] <= time()) {
							unset(Cooldown::$comboAbility[$damager->getName()]);
						}
					}
				}
			}
		}
	}


	public function onEntityDamageEvent(EntityDamageEvent $event): void
	{
		$player = $event->getEntity();
		$x = $player->getX();
		$y = $player->getY();
		$z = $player->getZ();
		$mngr = Sage::getFactionsManager();
		if ($event instanceof EntityDamageByEntityEvent) {
			$damager = $event->getDamager();
			if ($damager instanceof SagePlayer && $player instanceof SagePlayer) {
				if ($damager->getFocusedPlayer() === $player->getName()) {
					$chance = rand(1, 11);
					$event->setBaseDamage($event->getBaseDamage() + 0.25);
					if ($chance === 3) {
						TextEntity::spawnText($player->asPosition(), "§r§l§c25% DAMAGE");
						$player->sendMessage("§r§l§c25% DAMAGE");
						$blockp = new DestroyBlockParticle(new Vector3($x, $y + mt_rand(1, 2), $z), Block::get(100, 14));
						$player->getLevel()->addParticle($blockp);
						if ($chance === 1) {
							TextEntity::spawnText($player->asPosition(), "§r§l§c25% DAMAGE");
							$player->sendMessage("§r§l§c25% DAMAGE");
							$blockp = new DestroyBlockParticle(new Vector3($x, $y + mt_rand(1, 2), $z), Block::get(100, 14));
							$player->getLevel()->addParticle($blockp);
						}
					}
				}
			}
		}
	}

	/*public function comboMode(EntityDamageByEntityEvent $event)
	{
		$entity = $event->getEntity();
		if ($entity instanceof SagePlayer) {
			if (isset(Cooldown::$comboed[$entity->getName()])) {
				if (Cooldown::$comboed[$entity->getName()] <= time()) {
					unset(Cooldown::$comboed[$entity->getName()]);
				} else {
					if (isset(Cooldown::$comboed[$entity->getName()])) {
						$event->setKnockBack(0.350);
						$event->setCancelled(false);
					}
				}
			}
		}
	}*/

	public function onInteractPitem(PlayerInteractEvent $event)
	{
		$player = $event->getPlayer();
		$inHand = $player->getInventory()->getItemInHand();
		if ($player instanceof SagePlayer) {
			if ($inHand->getCustomName() === "§r§e§lBambes's Portable Bard" && $inHand->getNamedTag()->hasTag("PartnerItem_BARD")) {
				if (!isset(Cooldown::$portableBard[$player->getName()])) {
					$event->setCancelled();
					Cooldown::$portableBard[$player->getName()] = time();
					$inHand->setCount($inHand->getCount() - 1);
					$player->getInventory()->setItemInHand($inHand);
					$bard = new PortableBambeEntity($player->getLevel(), Entity::createBaseNBT($player->asVector3()));
					$bard->spawnToAll();
					$this->plugin->getScheduler()->scheduleRepeatingTask(new PortableBardTask($player, $bard), 20);
				} else {
					$cooldown = (time() - Cooldown::$portableBard[$player->getName()]);
					if ($cooldown < 220) {
						$timer = time() - Cooldown::$portableBard[$player->getName()];
						$time = 220 - $timer;
						$player->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
						$event->setCancelled(true);
					} elseif (Cooldown::$portableBard[$player->getName()] <= time()) {
						unset(Cooldown::$portableBard[$player->getName()]);
					}
				}
			}

			if ($inHand->getCustomName() === "§r§c§lStrength II" && $inHand->getNamedTag()->hasTag("PartnerItem_Strength")) {
				if (!isset(Cooldown::$strength[$player->getName()])) {
					$event->setCancelled();
					Cooldown::$strength[$player->getName()] = time();
					$inHand->setCount($inHand->getCount() - 1);
					$player->getInventory()->setItemInHand($inHand);
					$player->addEffect(new EffectInstance(Effect::getEffect(Effect::STRENGTH), 20 * 8, 1));
				} else {
					$cooldown = (time() - Cooldown::$strength[$player->getName()]);
					if ($cooldown < 220) {
						$timer = time() - Cooldown::$strength[$player->getName()];
						$time = 220 - $timer;
						$player->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
						$event->setCancelled(true);
					} elseif (Cooldown::$strength[$player->getName()] <= time()) {
						unset(Cooldown::$strength[$player->getName()]);
					}
				}
			}

			if ($inHand->getCustomName() === "§r§6§lLogger Bait" && $inHand->getNamedTag()->hasTag("PartnerItem_Logger")) {
				if (!isset(Cooldown::$loggerBait[$player->getName()])) {
					$event->setCancelled();
					Cooldown::$loggerBait[$player->getName()] = time();
					$inHand->setCount($inHand->getCount() - 1);
					$player->getInventory()->setItemInHand($inHand);
					$nbt = Entity::createBaseNBT($player->asPosition());
					$villager = new FakeLogger($player->getLevel(), $nbt);
					$villager->setPlayer($player);
					$player->getLevel()->addEntity($villager);
					$villager->spawnToAll();
					Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new LoggerBaitTask($player, $villager),20);
				} else {
					$cooldown = (time() - Cooldown::$loggerBait[$player->getName()]);
					if ($cooldown < 200) {
						$timer = time() - Cooldown::$loggerBait[$player->getName()];
						$time = 200 - $timer;
						$player->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
						$event->setCancelled(true);
					} elseif (Cooldown::$loggerBait[$player->getName()] <= time()) {
						unset(Cooldown::$loggerBait[$player->getName()]);
					}
				}
			}

			if ($inHand->getCustomName() === "§r§c§lCombo's Medkit" && $inHand->getNamedTag()->hasTag("PartnerItem_MedKit")) {
				if (!isset(Cooldown::$medkit[$player->getName()])) {
					$event->setCancelled();
					Cooldown::$medkit[$player->getName()] = time();
					$inHand->setCount($inHand->getCount() - 1);
					$player->getInventory()->setItemInHand($inHand);
					$player->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 8, 4));
					$player->addEffect(new EffectInstance(Effect::getEffect(Effect::ABSORPTION), 20 * 8, 2));
					#	$nbt = Entity::createBaseNBT($player->asPosition());
					#$villager = new FakeLogger($player->getLevel(), $nbt);
					#	$villager->setPlayer($player);
					#$player->getLevel()->addEntity($villager);
					#	$villager->spawnToAll();
					#Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new LoggerBaitTask($player, $villager),20);
				} else {
					$cooldown = (time() - Cooldown::$medkit[$player->getName()]);
					if ($cooldown < 200) {
						$timer = time() - Cooldown::$medkit[$player->getName()];
						$time = 200 - $timer;
						$player->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
						$event->setCancelled(true);
					} elseif (Cooldown::$medkit[$player->getName()] <= time()) {
						unset(Cooldown::$medkit[$player->getName()]);
					}
				}
			}


			if ($inHand->getCustomName() === "§r§5§lNinja Star" && $inHand->getNamedTag()->hasTag("PartnerItem_NINJA")) {
				$teleport = Sage::getInstance()->getServer()->getPlayer($event->getPlayer()->getLastHit());
				if ($teleport === null) {
					$player->sendMessage("§l§c[!] §r§cYou tried using the §5§lNinja Star §r§cbut we couldn't find the player “null”\n§7if you believe this is an error report it.");
					return;
				}
				if (!$teleport instanceof SagePlayer) {
					return;
				}

				if(!isset(Cooldown::$ninjaStar[$player->getName()]) && $teleport->distance($player) > 40) {
					$player->sendMessage("§l§c[!] §r§cThe player you are trying to §5§lNinja Star §r§cto is too far away!\n§7Get closer to use this.");
					return;
				}
				if (isset(Cooldown::$ninjaStar[$player->getName()])) {
					$cooldown = (time() - Cooldown::$ninjaStar[$player->getName()]);
					if ($cooldown < 200) {
						$timer = time() - Cooldown::$ninjaStar[$player->getName()];
						$time = 200 - $timer;
						$player->sendMessage("§r§cYou cannot use this for another §r§c§l{$time}§r§c seconds.");
						$event->setCancelled(true);
					} elseif (Cooldown::$ninjaStar[$player->getName()] <= time()) {
						unset(Cooldown::$ninjaStar[$player->getName()]);
					}
					return;
				}
				if(!isset(Cooldown::$ninjaStar[$player->getName()])){
					Cooldown::$ninjaStar[$player->getName()] = time();
					$player->sendMessage("§r§c§l " . "§r§6You  will teleport to " . $teleport->getName() . " §r§6 in 6 seconds!");
					$player->sendMessage("§r§c§l " . "§r§6You are now on a cooldown for §r§f500 seconds");
					$this->plugin->getScheduler()->scheduleRepeatingTask(new NinjaStarTask($player, $teleport),20);
				}
			}
			if ($inHand->getNamedTag()->hasTag("PartnerPackage")) {
				$inHand->setCount($inHand->getCount() - 1);
				$player->getInventory()->setItemInHand($inHand);
				Sage::playSound($player, "mob.wither.break_block");
				$event->setCancelled(true);
				$rewards = rand(0, 15);
				switch ($rewards) {
					case 0:
						Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_PINK, Fireworks::TYPE_BURST);
						PItemManager::givePartnerItem($player, PItemManager::COMBO_ABLILITY, rand(1, 2));
						break;
					case 1:
						Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_DARK_AQUA, Fireworks::TYPE_BURST);
						PItemManager::givePartnerItem($player, PItemManager::PORTABLE_BARD, rand(1, 2));
						break;
					case 2:
						Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_RED, Fireworks::TYPE_BURST);
						PItemManager::givePartnerItem($player, PItemManager::BONE, rand(1, 2));
						break;
					case 3:
						Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_GOLD, Fireworks::TYPE_BURST);
						PItemManager::givePartnerItem($player, PItemManager::ANTI_TRAP_BEACON, rand(1, 2));
						break;
					case 4:
						Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_DARK_PINK, Fireworks::TYPE_BURST);
						PItemManager::givePartnerItem($player, PItemManager::NINJA_STAR, rand(1, 2));
						break;
					case 5:
						Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_YELLOW, Fireworks::TYPE_BURST);
						PItemManager::givePartnerItem($player, PItemManager::DECOY, rand(1, 2));
						break;
					case 6:
						$helmet = Item::get(Item::DIAMOND_HELMET)->setCustomName("§r§8§l[§6§lBETA§8] §r§eHelemet");
						$helmet->setLore([
							'§r§4Night Vision II',
						]);
						$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
						$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
					

						$chestplate = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
						$chestplate->setCustomName("§r§8§l[§6§lBETA§8] §r§eChestplate");
						$chestplate->setLore([
							'§r§4Magma II',
							'§r§4Repair II',
						]);
						$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
						$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
						$player->getInventory()->addItem($chestplate);
						$player->getInventory()->addItem($helmet);
						break;

					case 7:
						$leggs = Item::get(Item::DIAMOND_LEGGINGS, 0, 1);
						$leggs->setCustomName("§r§8§l[§6§lBETA§8]  §r§eLeggings");
						$leggs->setLore([
							'§r§4Repair II',
						]);
						$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
						$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));

						$boots = Item::get(Item::DIAMOND_BOOTS, 0, 1);
						$boots->setCustomName("§r§8§l[§6§lBETA§8]  §r§eBoots");
						$boots->setLore([
							'§r§4Speed II',
						]);
						$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
						$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
						$player->getInventory()->addItem($boots);
						$player->getInventory()->addItem($leggs);
						break;
					case 8:

						PItemManager::givePartnerItem($player, PItemManager::LOGGER_BAIT, rand(1,2));
						break;

					case 9:

						PItemManager::givePartnerItem($player, PItemManager::STICK, rand(1,2));
						break;

					case 10:
						PItemManager::givePartnerItem($player, PItemManager::STRENGTH, rand(1,2));
						break;

					case 11:
						Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_RED, Fireworks::TYPE_BURST);
						PItemManager::givePartnerItem($player, PItemManager::MEDKIT, rand(1,2));
						break;

					case 12:
						Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_GREEN, Fireworks::TYPE_BURST);
						PItemManager::givePartnerItem($player, PItemManager::LASSO, rand(1,2));
						break;

					case 13:
						Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_DARK_PINK, Fireworks::TYPE_BURST);
						PItemManager::givePartnerItem($player, PItemManager::FOCUSMODE, rand(1,2));
						break;

					case 14:
						Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_DARK_GRAY, Fireworks::TYPE_BURST);
						$player->sendMessage("§l§e(!) §r§eYou've been given §6x1 §6§lKEYALL Voucher 2021 §r§eAbility Key(s)");
						PItemManager::giveKeyAllVoucher($player,PItemManager::ABILITY,1);
						break;

					case 15:
						Utils::spawnFirework(new Position($player->x, $player->y + 3, $player->z, $player->getLevel()), Fireworks::COLOR_PINK, Fireworks::TYPE_BURST);
						$player->sendMessage("§l§e(!) §r§eYou've been given §6x1 §6§lKEYALL Voucher 2021 §r§eSage Key(s)");
						PItemManager::giveKeyAllVoucher($player,PItemManager::SAGE,1);
						break;
				}
			}
		}
	}




	/**
	 * @param EntityDamageEvent $event
	 */
	public function onDamage(EntityDamageEvent $event)
	{
		$entity = $event->getEntity();
		if ($event instanceof EntityDamageByEntityEvent) {
			$damager = $event->getDamager();
			if ($entity instanceof SagePlayer && $damager instanceof SagePlayer) {
				if ($damager->isInFaction() && $entity->isInFaction()) {
					if ($damager->getFaction() == $entity->getFaction()) {
						return;
					}
				}
				$entity->setLastHit($damager->getName());
			}
		}
	}




	public static function Lightning(SagePlayer $player): void
	{
		$light = new AddActorPacket();
		$light->type = "minecraft:lightning_bolt";
		$light->entityRuntimeId = Entity::$entityCount++;
		$light->metadata = [];
		$light->motion = null;
		$light->yaw = $player->getYaw();
		$light->pitch = $player->getPitch();
		$light->position = new Vector3($player->getX(), $player->getY(), $player->getZ());
		Server::getInstance()->broadcastPacket($player->getLevel()->getPlayers(), $light);
		$block = $player->getLevel()->getBlock($player->getPosition()->floor()->down());
		$particle = new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), $block);
		$player->getLevel()->addParticle($particle);
		$sound = new PlaySoundPacket();
		$sound->soundName = "ambient.weather.thunder";
		$sound->x = $player->getX();
		$sound->y = $player->getY();
		$sound->z = $player->getZ();
		$sound->volume = 1;
		$sound->pitch = 1;
		Server::getInstance()->broadcastPacket($player->getLevel()->getPlayers(), $sound);
	}
}