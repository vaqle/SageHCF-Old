<?php
namespace vale\hcf\sage\factions;
use pocketmine\entity\Zombie;
use pocketmine\item\Item;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Server;
use vale\hcf\sage\handlers\events\PlayerListener;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;
use pocketmine\block\BlockIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use vale\hcf\sage\system\deathban\Deathban;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

class FactionsListener implements Listener
{


	private $plugin;


	/**
	 * FactionListener constructor.
	 *
	 * @param Sage $plugin
	 */

	public function __construct(Sage $plugin)
	{
		$this->setPlugin($plugin);
	}


	/**
	 * @return mixed
	 */

	public function getPlugin(): Sage
	{

		return $this->plugin;

	}


	/**
	 * @param mixed $plugin
	 */

	public function setPlugin($plugin)
	{

		$this->plugin = $plugin;

	}

	public function onDeath(PlayerDeathEvent $event)
	{
		$mgr = Sage::getFactionsManager();
		$player = $event->getPlayer();
		if ($player instanceof SagePlayer) {
			$level = Sage::getInstance()->getServer()->getLevelByName("deathban");
			$plevel = Sage::getInstance()->getServer()->getLevelByName($player->getLevel()->getName());
			if ($plevel !== $level) {
				if ($player->isInFaction()) {
					$fac = $player->getFaction();
					$mgr->reduceDTR($fac);
					$dtr = $mgr->getDtr($fac);
					$mgr->reducePower($fac, 10);
					$member = Sage::getFactionsManager()->getOnlineMembers($player->getFaction());
					foreach ($member as $members) {
						$dtr = Sage::getFactionsManager()->getDTR($fac);
						$ftime = Sage::getFactionsManager()->getFrozenTimeLeft($fac);
						$name = $player->getName();
						$player->setCombatTagged(false);
						$player->setCombatTagTime(0);
						$members->sendMessage("§r§c§l(!) §r§cMember Death: §r§7{$name}");
						$members->sendMessage("§r§c§l(!) §r§cDTR: §r§c({$dtr} amt of DTR)");
						$members->sendMessage("\n");
						$members->sendMessage("§r§cYour faction has been put on DTR Freeze for {$ftime} seconds");
						$members->sendMessage("\n");
						$members->sendMessage("§r§7((IGNORE DUPLICATE MESSAGES))");
					}
				}

				$c = $player->getLastDamageCause();
				if ($c instanceof EntityDamageByEntityEvent) {
					$killer = $c->getDamager();
					if ($killer instanceof SagePlayer) {
						if ($killer->isInFaction()) {
							$killerfac = $killer->getFaction();
							$mgr->addPower($killerfac, 10);
							$mgr->addCrystals($killerfac, 10);
						}
					}
				}
			}
		}
	}


	public function onHit(EntityDamageEvent $event)
	{
		if (Sage::getFactionsManager()->isSpawnClaim($event->getEntity()->asVector3())) {
			$event->setCancelled(true);
		}
		if ($event instanceof EntityDamageByEntityEvent) {
			$damager = $event->getDamager();
			$entity = $event->getEntity();
			$m = Sage::getFactionsManager();
			$damagervec = $damager->asVector3();
			$entityvec = $entity->asVector3();
			if ($entity instanceof Zombie) {
				if ($damager instanceof SagePlayer && $damager->isInFaction()) {
					if ($m->isInFaction($entity->getNameTag())) {
						if ($damager->getFaction() == $m->getFaction($entity->getNameTag())) {
							$event->setCancelled(true);
						}
					}
				}
			}
			if ($damager instanceof SagePlayer && $entity instanceof SagePlayer) {
				if ($m->isSpawnClaim($entityvec)) {
					$event->setCancelled(true);
				}
				if ($m->isSpawnClaim($damagervec)) {
					$event->setCancelled(true);
				}
				if ($damager->hasPvpTimer()) {
					$event->setCancelled(true);
					$damager->sendMessage("§r§cType '§e/pvp enable§c' to remove your timer.");
					$damager->sendMessage("§r§cYou cannot do this while your PvP Timer is active!");
				}
				if ($entity->hasPvpTimer()) {
					$event->setCancelled(true);
					$damager->sendMessage("§c§l{$entity->getName()} §r§chas there PvP Timer for another " . Sage::secondsToTime($entity->getPvPTimer()));
				}
				if ($damager->isInFaction() && $entity->isInFaction()) {
					if ($damager->getFaction() == $entity->getFaction()) {
						$damager->sendMessage("§c§l" . $entity->getName() . " §r§cis in your Faction.");
						$event->setCancelled(true);
					}
				}
			}
		}
	}

	/**
	 * @param PlayerInteractEvent $event
	 */

	public function onInteract(PlayerInteractEvent $event)
	{

		$player = $event->getPlayer();

		$block = $event->getBlock();

		$item = $event->getItem();

		if ($player instanceof SagePlayer) {

			switch ($block->getId()) {

				case BlockIds::FENCE_GATE:
				case BlockIds::WOODEN_DOOR_BLOCK:
				case BlockIds::OAK_DOOR_BLOCK:
				case BlockIds::ACACIA_FENCE_GATE:

				case BlockIds::BIRCH_FENCE_GATE:

				case BlockIds::DARK_OAK_FENCE_GATE:

				case BlockIds::SPRUCE_FENCE_GATE:

				case BlockIds::JUNGLE_FENCE_GATE:

				case BlockIds::IRON_TRAPDOOR:

				case BlockIds::WOODEN_TRAPDOOR:

				case BlockIds::TRAPDOOR:

				case BlockIds::OAK_FENCE_GATE:

					if (Sage::getFactionsManager()->isFactionClaim($block)) {

						if (!$player->isInFaction()) {

							#$player->sendMessage("§eYou may not edit Fencegates " . Sage::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
							$event->setCancelled(true);

						} else {

							if (Sage::getFactionsManager()->getClaimer($block->x, $block->z) != $player->getFaction()) {

								#$player->sendMessage(TextFormat::RED . "You can't do that on " . Sage::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");

								$event->setCancelled(true);

							}

						}

					}

					break;


				case BlockIds::CHEST:

				case BlockIds::TRAPPED_CHEST:

					if (Sage::getFactionsManager()->isFactionClaim($block)) {

						if (!$player->isInFaction()) {

							#	$player->sendMessage("You can't do that on " . Sage::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");

							$event->setCancelled(true);

						} else {

							if (Sage::getFactionsManager()->getClaimer($block->x, $block->z) != $player->getFaction()) {

								#$player->sendMessage(TextFormat::RED . "You can't do that on " . Sage::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");

								$event->setCancelled(true);

							}

						}

					}

					break;

			}

			switch ($item->getId()) {

				case ItemIds::BUCKET:

				case ItemIds::DIAMOND_HOE:

				case ItemIds::GOLD_HOE:

				case ItemIds::IRON_HOE:

				case ItemIds::STONE_HOE:

				case ItemIds::WOODEN_HOE:

				case ItemIds::DIAMOND_SHOVEL:

				case ItemIds::GOLD_SHOVEL:

				case ItemIds::IRON_SHOVEL:

				case ItemIds::STONE_SHOVEL:

				case ItemIds::WOODEN_SHOVEL:

					if (Sage::getFactionsManager()->isFactionClaim($block)) {

						if (!$player->isInFaction()) {

							#$player->sendMessage(TextFormat::RED . "You can't do that on " . Sage::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");

							$event->setCancelled(true);

						} else {

							if (Sage::getFactionsManager()->getClaimer($block->x, $block->z) != $player->getFaction()) {

								#$player->sendMessage(TextFormat::RED . "You can't do that on " . Sage::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");

								$event->setCancelled(true);

							}

						}

					} elseif (Sage::getFactionsManager()->isClaim($block)) {

						if (!$player->isOp()) {

							$event->setCancelled(true);

						}

					}

					break;

			}

		}

	}


	/**
	 * @param PlayerMoveEvent $event
	 */

	/**
	 * @param BlockBreakEvent $event
	 */

	public function onBreaker(BlockBreakEvent $event)
	{

		$player = $event->getPlayer();

		$block = $event->getBlock();


		if (Sage::getFactionsManager()->isFactionClaim($block)) {

			if ($player instanceof SagePlayer) {


				if (!$player->isInFaction()) {

					#$player->sendMessage(TextFormat::RED . "You can't break blocks on " . Sage::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");

					$event->setCancelled(true);


				} else {

					if (Sage::getFactionsManager()->getClaimer($block->x, $block->z) != $player->getFaction()) {

						#$player->sendMessage(TextFormat::RED . "You can't break blocks on " . Sage::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");

						$event->setCancelled(true);


					}

				}

			}

		}

	}


	/**
	 * @param BlockPlaceEvent $event
	 */

	public function onPlace(BlockPlaceEvent $event)
	{

		$player = $event->getPlayer();

		$block = $event->getBlock();


		if (Sage::getFactionsManager()->isFactionClaim($block)) {

			if ($player instanceof SagePlayer) {

				if (!$player->isInFaction()) {

					#$player->sendMessage(TextFormat::RED . "You can't place blocks on " . Sage::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
					$event->setCancelled(true);

				} else {

					if (Sage::getFactionsManager()->getClaimer($block->x, $block->z) != $player->getFaction()) {

						#$player->sendMessage("You can't place blocks on " . Sage::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
						$event->setCancelled(true);


					}

				}

			}

		}

	}


	public function onPlacee(BlockPlaceEvent $event)
	{

		$block = $event->getBlock();

		$spawn = new Vector3(0, 100, 0);

		$player = $event->getPlayer();


		if ($spawn->distance($block) < 220 and !$player->isOp()) {

			$player->sendMessage("§r§cYou can't place blocks / break them in the warzone!");

			$event->setCancelled(true);

			return;

		}
	}


	public function onBreakeee(BlockBreakEvent $event)
	{

		$block = $event->getBlock();

		$spawn = new Vector3(0, 100, 0);

		$player = $event->getPlayer();


		if ($spawn->distance($block) < 220 and !$player->isOp()) {

			$player->sendMessage("§r§cYou can't place blocks / break them in the warzone!");

			$event->setCancelled(true);

			return;

		}
	}


	public function onBlockBreakEvent(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if ($player instanceof SagePlayer) {
			if ($player->isInFaction()) {
				$playerfac = $player->getFaction();
				if ($block->getId() === BlockIds::BEACON) {
					if (Sage::getFactionsManager()->isClaim($block)) {
						$faction = Sage::getFactionsManager()->getClaimer($block->x, $block->z);
						if (Sage::getFactionsManager()->getDTR($faction) <= 0) {
							$event->setCancelled(false);
							$crystals = Sage::getFactionsManager()->getCrystals($faction);
							Sage::getFactionsManager()->addCrystals($playerfac, $crystals);
							$player->sendMessage("You have succesfully mined {$faction} crystals");
							foreach (Sage::getFactionsManager()->getOnlineMembers($playerfac) as $onlineMember) {
								$onlineMember->sendMessage("§r§e(§r§6§l!§r§e) Your faction member §r§6§l{$player->getName()} §r§ehas mined §r§6§l{$faction} 's §r§5Crystals §r§eand Earned §r§5{$crystals} §r§eCrystals");
							}
							foreach (Sage::getFactionsManager()->getOnlineMembers($faction) as $member) {
								$member->sendMessage("§r§6Your §r§5crystals §r§ehave been §r§5mined.");
							}
							Sage::getFactionsManager()->reduceCrystals($faction, $crystals);
						} else {
							$player->sendMessage("§r§6The faction §r§e{$faction} §r§6is not raidable.");
							$event->setCancelled(false);
						}
					} else {
						$player->sendMessage("§r§cYou are not in the faction");
						$event->setCancelled(false);
					}
				}
			}
		}
	}


	/**
	 * @param PlayerChatEvent $event
	 */

	public function onChat(PlayerChatEvent $event)
	{

		if (!$event->isCancelled()) {

			$player = $event->getPlayer();

			if ($player instanceof SagePlayer) {
				if ($player->isInFaction()) {
					if ($player->getChat() == SagePlayer::FACTION) {

						$event->setCancelled(true);

						foreach (Sage::getFactionsManager()->getOnlineMembers($player->getFaction()) as $member) {

							if ($member instanceof SagePlayer) {

								$member->sendMessage(TextFormat::GRAY . "§e(Team) " . TextFormat::YELLOW . $player->getName() . ": " . "§r§6" . $event->getMessage());

							}

						}

					} else {

						$player->setChat(SagePlayer::PUBLIC);

					}
				}
			}
		}
	}

	public function onClaim(PlayerInteractEvent $event)
	{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$x = $block->getX();
		$y = $block->getY();
		$z = $block->getZ();
		$vec = $block->asVector3();
		$mgr = Sage::getFactionsManager();
		$spawn = $block->getLevel()->getSpawnLocation()->asVector3();
		$blockvector = new Vector3(abs($x), abs($y), abs($z));
		if ($player instanceof SagePlayer) {
			if ($player->isClaiming() and $player->isInFaction()) {
				if ($vec->distance($spawn) >= 200) {
					switch ($player->getStep()) {
						case SagePlayer::FIRST:
							$arr = ["South Road", "West Road", "North Road", "East Road", "Cyber Attack"];
							if (!in_array(Sage::getInstance()::getFactionsManager()->getClaimer($event->getBlock()->getX(), $event->getBlock()->getZ()), $arr)) {
								if ($player->getInventory()->getItemInHand()->getNamedTag()->hasTag("claimingwand") && $event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
									$player->setPos1($vec);
									$player->buildWall($x, $y, $z);
									$player->setStep(SagePlayer::SECOND);
									$player->sendMessage(TextFormat::RED . "§r§eSet the location of claim selection §r§f1 §r§eto: §r§6(§r§f{$block->x},{$block->z}§r§6)");
									$player->sendMessage("§r§eClaim selection cost: §r§6{$player->getClaimCost()}$");
								} elseif (in_array(Sage::getInstance()::getFactionsManager()->getClaimer($event->getBlock()->getX(), $event->getBlock()->getZ()), $arr)) {
									$player->sendMessage("§r§c(§c§l!§r§c) Claiming Server Claimed Regions is Forbidden");
								}
							}
							break;
						case SagePlayer::SECOND:
							$arr = ["South Road", "West Road", "North Road", "West Road", "East Road", "Cyber Attack"];
							if (!in_array(Sage::getInstance()::getFactionsManager()->getClaimer($event->getBlock()->getX(), $event->getBlock()->getZ()), $arr)) {
								if ($player->getInventory()->getItemInHand()->getNamedTag()->hasTag("claimingwand") && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
									$one = $player->getPos1();
									if ($one->distance($block) > 4) {
										$player->setPos2($vec);
										$player->buildWall($x, $y, $z);
										$player->checkClaim();
									} elseif ($one->distance($block) < 4) {
										$player->sendMessage("§r§c(§c§l!§r§c) Please increase the claim size.");
									} elseif (in_array(Sage::getInstance()::getFactionsManager()->getClaimer($event->getBlock()->getX(), $event->getBlock()->getZ()), $arr)) {
										$player->sendMessage("§r§c(§c§l!§r§c) Claiming Server Claimed Regions is Forbidden");
									}
								}
							}
							break;

						case SagePlayer::CONFIRM:
							if ($player->isSneaking()) {
								$fac = $player->getFaction();
								if ($mgr->getBalance($fac) >= $player->getClaimCost()) {
									$mgr->reduceBalance($fac, $player->getClaimCost());
									$x1 = $player->getPos1()->getFloorX();
									$z1 = $player->getPos1()->getFloorZ();
									$x2 = $player->getPos2()->getFloorX();
									$z2 = $player->getPos2()->getFloorZ();
									$player->setStep(SagePlayer::FIRST);
									$player->setClaiming(false);
									$player->setClaim(false);
									$player->removeWall((int)$player->getPos1()->getFloorX(), (int)$player->getPos1()->getFloorY(), (int)$player->getPos1()->getFloorZ());
									$player->removeWall((int)$player->getPos2()->getFloorX(), (int)$player->getPos2()->getFloorY(), (int)$player->getPos2()->getFloorZ());
									$mgr->claim($fac, $player->getPos1(), $player->getPos2(), FactionsManager::CLAIM);
									foreach (Sage::getFactionsManager()->getOnlineMembers($player->getFaction()) as $member) {
										$member->sendMessage("§r§6**{$player->getName()} §r§fhas claimed land for the Faction.");
									}
									$wand = Item::get(Item::GOLDEN_HOE);
									$wand->getNamedTag()->setTag(new StringTag("claimingwand"));
									if ($player->getInventory()->contains($wand)) {
										$player->getInventory()->remove($wand);
									}
								} else {
									$player->setStep(SagePlayer::FIRST);
									$player->setClaiming(false);
									$player->setClaim(false);
									$balance = Sage::getFactionsManager()->getBalance($player->getFaction());
									$player->sendMessage(TextFormat::RED . "§r§cYour faction bank only has $ {$balance}, the claim cost is $ {$player->getClaimCost()}.");
								}
							} else {
								if ($player->getInventory()->getItemInHand()->getNamedTag()->hasTag("claimingwand") && $event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
									$one = $player->getPos1();
									if ($one->distance($block) > 4) {
										$player->removeWall((int)$player->getPos2()->getFloorX(), (int)$player->getPos2()->getFloorY(), (int)$player->getPos2()->getFloorZ());
										$player->setPos2($vec);
										$player->buildWall($x, $y, $z);
										$player->checkClaim();
										$player->setClaiming(false);
									}
								}
							}
							break;
					}
				}
			}
		}
	}



	public function onClaimChat(PlayerChatEvent $event)
	{
		$player = $event->getPlayer();
		$message = strtolower($event->getMessage());
		if ($message == "cancel" || $message == "'cancel'") {
			if ($player instanceof SagePlayer) {
				if ($player->isClaiming()) {
					$event->setCancelled(true);
					$player->setStep(SagePlayer::FIRST);
					$player->setClaiming(false);
					$player->setClaim(false);
					$player->sendMessage(TextFormat::YELLOW . "§r§cSuccesfully cancled claiming proccess.");
				}
			}
		}
	}
}