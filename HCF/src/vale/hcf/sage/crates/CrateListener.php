<?php

declare(strict_types = 1);


namespace vale\hcf\sage\crates;

use pocketmine\Server;
//event
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\Listener as L;
use vale\hcf\sage\crates\CrateAPI as API;
use pocketmine\math\Vector3;
use pocketmine\level\particle\{DestroyBlockParticle, FloatingTextParticle};
use pocketmine\network\mcpe\protocol\{LevelSoundEventPacket};
use pocketmine\{block\Block,
	event\block\BlockPlaceEvent as BlockPlace,
	event\player\PlayerJoinEvent,
	item\Item,
	nbt\tag\ListTag};
use vale\hcf\sage\models\util\CratePreviews;
use vale\hcf\sage\partneritems\PItemManager;
use vale\hcf\sage\provider\DataProvider as DB;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use pocketmine\nbt\tag\StringTag;

class CrateListener implements L
{

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}



	public function crateInteract(PlayerInteractEvent $ev)
	{
#Variables
		$player = $ev->getPlayer();
		$block = $ev->getBlock();

		/** @return int * */
		$x = $block->x;
		$y = $block->y;
		$z = $block->z;

		$cratedata = DB::$cratedata;
		$hand = $player->getInventory()->getItemInHand();
		if ($player instanceof SagePlayer) {
			if ($cratedata->exists("Haze-Crate")) {
				if ($block->x == $cratedata->get("Haze-Crate")["x"] && $block->y == $cratedata->get("Haze-Crate")["y"] && $block->z == $cratedata->get("Haze-Crate")["z"] && $ev->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
					$ev->setCancelled(true);
					if ($hand->getId() !== Item::DYE && $hand->getDamage() !== 9 && $hand->getCustomName() !== "§r§d§lHaze §r§7Key (Right-Click)" && $hand->getCount() <= 0) {
						$player->sendMessage("§r§cInvalid crate key mismatch");
					} elseif ($hand->getId() == Item::DYE && $hand->getDamage() == 9 && $hand->getCustomName() == "§r§d§lHaze §r§7Key (Right-Click)" && $hand->getCount() >= 1) {
						//GIVE REWARDS
						$hand->setCount($hand->getCount() - 1);
						$player->getInventory()->setItemInHand($hand);
						$block->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($x, $y, $z), Block::get(Block::STAINED_GLASS, 6)));
						API::giveSageRewards($player);
					}
				} elseif ($block->x == $cratedata->get("Haze-Crate")["x"] && $block->y == $cratedata->get("Haze-Crate")["y"] && $block->z == $cratedata->get("Haze-Crate")["z"] && $ev->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK) {
					//todo send previews
					CratePreviews::sendHazePreview($player,true);
				}
				if ($cratedata->exists("SummerOrb-Crate")) {
					if ($block->x == $cratedata->get("SummerOrb-Crate")["x"] && $block->y == $cratedata->get("SummerOrb-Crate")["y"] && $block->z == $cratedata->get("SummerOrb-Crate")["z"] && $ev->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
						$ev->setCancelled(true);
						if (!$hand->getNamedTag()->hasTag("summerkeyxd") && $hand->getCount() <= 0) {
							$player->sendMessage("§r§cInvalid crate key mismatch");
						} elseif ($hand->getNamedTag()->hasTag("summerkeyxd") && $hand->getCount() >= 1) {
							//GIVE REWARDS
							$hand->setCount($hand->getCount() - 1);
							$player->getInventory()->setItemInHand($hand);
							$block->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($x, $y, $z), Block::get(Block::STAINED_GLASS, 3)));
							API::giveSummerRewards($player);
						}
					} elseif ($block->x == $cratedata->get("SummerOrb-Crate")["x"] && $block->y == $cratedata->get("SummerOrb-Crate")["y"] && $block->z == $cratedata->get("SummerOrb-Crate")["z"] && $ev->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK) {
						//todo send previews
						CratePreviews::sendHazePreview($player,true);

					}
					if ($cratedata->exists("Ability-Crate")) {
						if ($block->x == $cratedata->get("Ability-Crate")["x"] && $block->y == $cratedata->get("Ability-Crate")["y"] && $block->z == $cratedata->get("Ability-Crate")["z"] && $ev->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
							$ev->setCancelled(true);
							if (!$hand->getNamedTag()->hasTag("abilitykeyxd") && $hand->getCount() <= 0) {
								$player->sendMessage("§r§cInvalid crate key mismatch");
							} elseif ($hand->getNamedTag()->hasTag("abilitykeyxd") && $hand->getCount() >= 1) {
								//GIVE REWARDS
								$hand->setCount($hand->getCount() - 1);
								$player->getInventory()->setItemInHand($hand);
								$block->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($x, $y, $z), Block::get(Block::STAINED_GLASS, 8)));
								API::giveAbilityRewards($player);
							}
						} elseif ($block->x == $cratedata->get("Ability-Crate")["x"] && $block->y == $cratedata->get("Ability-Crate")["y"] && $block->z == $cratedata->get("Ability-Crate")["z"] && $ev->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK) {
							//todo send previews
							CratePreviews::sendHazePreview($player,true);

						}
						if ($cratedata->exists("Sage-Crate")) {
							if ($block->x == $cratedata->get("Sage-Crate")["x"] && $block->y == $cratedata->get("Sage-Crate")["y"] && $block->z == $cratedata->get("Sage-Crate")["z"] && $ev->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
								$ev->setCancelled(true);
								if (!$hand->getNamedTag()->hasTag("sagekeyxd") && $hand->getCount() <= 0) {
									$player->sendMessage("§r§cInvalid crate key mismatch");
								} elseif ($hand->getNamedTag()->hasTag("sagekeyxd") && $hand->getCount() >= 1) {
									//GIVE REWARDS
									$hand->setCount($hand->getCount() - 1);
									$player->getInventory()->setItemInHand($hand);
									$block->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($x, $y, $z), Block::get(Block::STAINED_GLASS, 3)));
									API::giveSageRewards($player);
								}
							} elseif ($block->x == $cratedata->get("Sage-Crate")["x"] && $block->y == $cratedata->get("Sage-Crate")["y"] && $block->z == $cratedata->get("Sage-Crate")["z"] && $ev->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK) {
								//todo send previews
								CratePreviews::sendHazePreview($player,true);
							}
							if ($cratedata->exists("Aegis-Crate")) {
								if ($block->x == $cratedata->get("Aegis-Crate")["x"] && $block->y == $cratedata->get("Aegis-Crate")["y"] && $block->z == $cratedata->get("Aegis-Crate")["z"] && $ev->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
									$ev->setCancelled(true);
									if (!$hand->getNamedTag()->hasTag("aegiskeyxd") && $hand->getCount() <= 0) {
										$player->sendMessage("§r§cInvalid crate key mismatch");
									} elseif ($hand->getNamedTag()->hasTag("aegiskeyxd") && $hand->getCount() >= 1) {
										//GIVE REWARDS
										$hand->setCount($hand->getCount() - 1);
										$player->getInventory()->setItemInHand($hand);
										$block->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($x, $y, $z), Block::get(Block::STAINED_GLASS, 3)));
										API::giveSageRewards($player);
									}
								} elseif ($block->x == $cratedata->get("Aegis-Crate")["x"] && $block->y == $cratedata->get("Aegis-Crate")["y"] && $block->z == $cratedata->get("Aegis-Crate")["z"] && $ev->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK) {
									//todo send previews
									CratePreviews::sendHazePreview($player,true);

								}
							}
						}
					}
				}
			}
		}
	}

	public function onPlace(BlockPlace $ev)
	{
#Variables
		$player = $ev->getPlayer();
		$block = $ev->getBlock();
		$hand = $player->getInventory()->getItemInHand();
		$config = DB::$cratedata;
		$level = $block->getLevel()->getName();
		$l = $block->getLevel();
		$names = explode("\n", $hand->getCustomName());
		if ($hand->getId() == 146 && $names[0] == "§r§l§dHaze-Crate") {
			if ($config->exists("Haze-Crate")) {
#$ev->setCancelled();
				$player->sendMessage("§l§c(!) §r§cYou cannot place this Haze Crate because one already exists within database");

			} elseif (!$config->exists("Haze-Crate")) {
				$config->set("Haze-Crate", [
					"x" => (int)$block->x,
					"y" => (int)$block->y,
					"z" => (int)$block->z,
					"level" => (string)$level
				]);
				$config->save();
				$player->sendMessage("§eYou have successfully placed a §l§dHaze Crate");
			}
		}

		if ($hand->getId() == 146 && $names[0] == "§r§l§dAegis-Crate") {
			if ($config->exists("Aegis-Crate")) {
				$player->sendMessage("§l§c(!) §r§cYou cannot place this Aegis-Crate because one already exists within database");
			} elseif (!$config->exists("Aegis-Crate")) {
				$config->set("Aegis-Crate", [
					"x" => (int)$block->x,
					"y" => (int)$block->y,
					"z" => (int)$block->z,
					"level" => (string)$level
				]);
				$config->save();
				$player->sendMessage("§eYou have successfully placed a §l§dAegis-Crate");
			}

		}

		if ($hand->getId() == 146 && $names[0] == "§r§l§dSage-Crate") {
			if ($config->exists("Sage-Crate")) {
				$player->sendMessage("§l§c(!) §r§cYou cannot place this Sage-Crate because one already exists within database");
			} elseif (!$config->exists("Sage-Crate")) {
				$config->set("Sage-Crate", [
					"x" => (int)$block->x,
					"y" => (int)$block->y,
					"z" => (int)$block->z,
					"level" => (string)$level
				]);
				$config->save();
				$player->sendMessage("§eYou have successfully placed a §l§dSage-Crate");
			}

		}

		if ($hand->getId() == 146 && $names[0] == "§r§l§dSummerOrb-Crate") {
			if ($config->exists("SummerOrb-Crate")) {
				$player->sendMessage("§l§c(!) §r§cYou cannot place this SummerOrb-Crate because one already exists within database");
			} elseif (!$config->exists("SummerOrb-Crate")) {
				$config->set("SummerOrb-Crate", [
					"x" => (int)$block->x,
					"y" => (int)$block->y,
					"z" => (int)$block->z,
					"level" => (string)$level
				]);
				$config->save();
				$player->sendMessage("§eYou have successfully placed a §l§dSummerOrb-Crate");
			}

		}
		if ($hand->getId() == 146 && $names[0] == "§r§l§dAbility-Crate") {
			if ($config->exists("Ability-Crate")) {
				$player->sendMessage("§l§c(!) §r§cYou cannot place this SummerOrb-Crate because one already exists within database");
			} elseif (!$config->exists("Ability-Crate")) {
				$config->set("Ability-Crate", [
					"x" => (int)$block->x,
					"y" => (int)$block->y,
					"z" => (int)$block->z,
					"level" => (string)$level
				]);
				$config->save();
				$player->sendMessage("§eYou have successfully placed a §l§dAbility-Crate");
			}
		}
	}
}
