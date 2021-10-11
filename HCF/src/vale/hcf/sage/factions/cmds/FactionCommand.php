<?php


namespace vale\hcf\sage\factions\cmds;

use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\Item;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Server;
use vale\hcf\sage\commands\player\BlockShopCommand;
use vale\hcf\sage\factions\FactionsManager;
use vale\hcf\sage\factions\FactionsPermissionManager;
use vale\hcf\sage\factions\tasks\InviteTask;
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\factions\tasks\TeleportTask;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\SagePlayer;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;
use vale\hcf\sage\Sage;

class FactionCommand extends PluginCommand
{
	private $plugin;

	/**
	 * FactionCommand constructor.
	 *
	 * @param Sage $plugin
	 */
	public function __construct(Sage $plugin)
	{
		parent::__construct("f", $plugin);
		$this->setPlugin($plugin);
		$this->setAliases(["f"]);
	}

	/**
	 * @param Sage $plugin
	 */
	public function setPlugin(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 *
	 * @return bool|mixed|void
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof SagePlayer) {
			if (!isset($args[0])) {
				$sender->sendMessage(TextFormat::RED . "§r§6§l* §r§eThe sub-command 'null' was not found.");
				return;
			}
			switch (strtolower($args[0])) {
				case "help":
				case "?":
					foreach ($this->getHelp() as $help) {
						$sender->sendMessage(TextFormat::GREEN . "" . TextFormat::GRAY . $help);
					}
					break;

				case "setdtr":
					if ($sender->isOp()) {
						if (isset($args[1]) and isset($args[2])) {
							if (Sage::getFactionsManager()->isFaction($args[1])) {
								Sage::getFactionsManager()->setDTR($args[1], $args[2]);
							}
						}
					}
					break;

				case "freeze":
					if (isset($args[1]) and $sender->isOp()) {
						if (Sage::getFactionsManager()->isFaction($args[1])) {
							Sage::getFactionsManager()->setFrozenTime($args[1], time() + 60);
						}
					}
					break;

				case "dtr":
					$mgr = Sage::getFactionsManager();
					if (isset($args[1])) {
						if ($sender->hasPermission("core.facs.setdtr")) {
							if ($mgr->isFaction($args[1])) {
								$mgr->setDTR($args[1], floatval($args[2]));
								$sender->sendMessage("" . "§l§c»» §r§7You have set " . $args[1] . " DTR to " . $args[2] . "!");
							}
						}
					}
					break;

				case "list":
					$sender->sendMessage(Sage::getFactionsManager()->sendList());
					$sender->setChat(SagePlayer::STAFF);
					break;

				case "claim":
					$found = Sage::getFactionsManager()->getDb()->query("SELECT * FROM claims WHERE faction = '" . $sender->getFaction() . "';");
					if (!empty($found->fetchArray())) {
						$sender->sendMessage("§r§c(§l!§r§c) The Team already has a claim.");
						return;
					}
					if ($sender->isInFaction()) {
						if (Sage::getFactionsManager()->getDTR($sender->getFaction()) < 0) {
							$sender->sendMessage("§r§c(§l!§r§c)Claiming whilst raidable is forbidden");
							return;
						}
						if (Sage::getFactionsManager()->getLeader($sender->getFaction()) == $sender->getName()) {
							$sender->setClaim(false);
							$sender->setClaiming(true);
							$sender->setStep(SagePlayer::FIRST);
							$claimingwand = Item::get(Item::GOLD_HOE, 0, 1);
							$claimingwand->setCustomName("§r§c§lClaiming Wand")->setLore([
								"§r§c§l1. §r§7Left click position 1 to set the first position.",
								"§r§c§l2. §r§7Right click position 2 to set the second position.",
								"",
								"",
								"§r§7If you are satisfied please sneak and left click to §c§lconfirm §r§7 your claim.",
								"§r§7((If you hear a ding sound it means you messed up a step))"

							]);
							$claimingwand->getNamedTag()->setTag(new ListTag("ench"));
							$claimingwand->getNamedTag()->setTag(new StringTag("claimingwand"));
							$sender->getInventory()->addItem($claimingwand);
							$sender->sendMessage(TextFormat::GRAY . "§r§cPlease check your inventory for the claiming wand.");
						}
					}
					break;

				case "unclaim":
					if ($sender->isInFaction()) {
						if (Sage::getFactionsManager()->getDTR($sender->getFaction()) < 1) {
							$sender->sendMessage("§r§c(§l!§r§c)Claiming whilst raidable is forbidden");
							return;
						}
						if (Sage::getFactionsManager()->getLeader($sender->getFaction()) == $sender->getName()) {
							$found = Sage::getFactionsManager()->getDb()->query("SELECT * FROM claims WHERE faction = '" . $sender->getFaction() . "';");
							if (!empty($found->fetchArray())) {
								Sage::getFactionsManager()->getDb()->exec("DELETE FROM claims WHERE faction = '" . $sender->getFaction() . "';");
								$sender->sendMessage(TextFormat::GRAY . "§r§c(§l!§r§c) Unclaimed your factions territory.");
							} else {
								$sender->sendMessage(TextFormat::RED . "The faction {$sender->getFaction()}' s claim was not found in the database.");
							}
						}
					}
					break;

				case "create":
				case "make":
					if ($sender->isInFaction()) {
						$sender->sendMessage(TextFormat::RED . "You are already in a team");
						return;
					}
					if (!isset($args[1])) {
						$sender->sendMessage(TextFormat::RED . "§r§6§l* §r§e/f create 'faction'.");
						return;
					}
					if (!ctype_alnum($args[1])) {
						$sender->sendMessage(TextFormat::RED . "§r§6§l* §r§eThe faction may not contain integers.");
						return;
					}
					if (strlen($args[1]) > 10) {
						$sender->sendMessage(TextFormat::RED . "§r§6§l* §r§eThe Team name is too long.");
						return;
					}
					Sage::getFactionsManager()->createFaction($args[1], $sender);
					break;

				case "disband":
				case "delete":
					if (!$sender->isInFaction()) {
						$sender->sendMessage(TextFormat::RED . "You are not in a Team!");
						return;
					}
					if (Sage::getFactionsManager()->getLeader($sender->getFaction()) != $sender->getName()) {
						$sender->sendMessage(TextFormat::RED . "You are not the Leader of {$sender->getFaction()} .");
						return;
					}
					Sage::getFactionsManager()->disbandFaction($sender->getFaction());
					break;

				case "lmao":
					Sage::getFactionsManager()->sendListOfTop10FactionsTo($sender);
						break;

				case "leave":
				case "quit":
					if (!$sender->isInFaction()) {
						$sender->sendMessage(TextFormat::RED . "You are not in a team.");
						return;
					}
					foreach (Sage::getFactionsManager()->getOnlineMembers($sender->getFaction()) as $member) {
						$member->sendMessage(TextFormat::GRAY . $sender->getName() . " §r§6left the team!");
					}
					$sender->removeFromFaction();
					//$sender->setNameTagToAll();
					$sender->sendMessage(TextFormat::GREEN . "§r§cYou left that team!");
					break;

				case "lol":
					$sender->sendMessage(Sage::getFactionsManager()->getFactionTop1());
					break;

				case "top":
					Sage::getFactionsManager()->sendListOfTop10FactionsTo($sender);
					break;
				case "home":
					$mgr = Sage::getFactionsManager();
					if(!$sender->isInFaction()){
						$sender->sendMessage("§r§cYou are not in a team.");
						return;
					}
					if(!$mgr->isHome($sender->getFaction())){
						$sender->sendMessage("§r§cThe factions HQ has not been set.");
						return;
					}

					if($sender->hasPvpTimer()){
						$sender->sendMessage("§r§7((§6§lDISABLE PVP TIMER§r§7))");
						$sender->getLevel()->addSound(new AnvilFallSound($sender));
						return;
					}
					if($sender->isTeleporting()){
						$sender->sendMessage("§r§cYou are currently in queue for a Teleportation.");
						$sender->getLevel()->addSound(new AnvilFallSound($sender));
						return;
					}
					if($sender->isCombatTagged()){
						$sender->sendMessage("§6§lINFORMATION");
						$sender->sendMessage("§r§7((TO DO THIS RUN /F STUCK))");
						$sender->sendMessage("§r§7Teleporting to your faction home whilst in combat tag is strictly forbidden, however");
						$sender->sendMessage("§r§7you may run the command §6§/f stuck §r§7to be teleported outside of the base but you must not be hit again.");
						$sender->getLevel()->addSound(new AnvilFallSound($sender));
						return;
					}
					$h = SagePlayer::HOME;
					$fac = $sender->getFaction();
					$sender->sendMessage("§r§c(§c§l!§r§c) §r§cTeleporting to your factions HQ. You will have to wait 10 seconds.");
					$sender->sendMessage("§r§7((MOVING WILL CANCEL TELEPORTATION))");
					$sender->setTeleport($h);
					$sender->setTeleporting(true);
					$currentpos = new Vector3((int) $sender->getX(), (int) $sender->getY(), (int) $sender->getZ());
					$sender->setTeleportTask(new TeleportTask(Sage::getInstance(), $sender, "§r§6§l* §r§7Teleport has Commenced.", 10, $mgr->getHome($fac), $currentpos));
					break;

				case "stuck":
					$mgr = Sage::getFactionsManager();
					if($sender->isTeleporting()){
						$sender->sendMessage("§r§cYou are currently in queue for a Teleportation.");
						$sender->getLevel()->addSound(new AnvilFallSound($sender));
						return;
					}
					if($sender->hasPvpTimer()){
						$sender->sendMessage("§r§7((§6§lDISABLE PVP TIMER§r§7))");
						$sender->getLevel()->addSound(new AnvilFallSound($sender));
						return;
					}
					if($mgr->isClaim($sender)){
						if($sender->isInFaction()){
							$x = (int) $sender->getX();
							$z = (int) $sender->getZ();
							if($mgr->getClaimer($x, $z) == $sender->getFaction()){
								$sender->sendMessage("" . "§r§7((FSTUCK is forbidden on your own claim))");
								return;
							}
						}
					}
					$h = SagePlayer::STUCK;
					if($mgr->isClaim($sender)){
						$x = (int) $sender->getX();
						$z = (int) $sender->getZ();
						$db = $mgr->getDb();
						$result = $db->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2;");
						$array = $result->fetchArray(SQLITE3_ASSOC);
						$y = $sender->getLevel()->getHighestBlockAt((int) $array["x1"], (int) $array["z1"]);
						$pos = new Vector3($array["x1"] + 1, $y + 4, $array["z1"] + 1);
						$sender->sendMessage("§r§c(§c§l!§r§c) §r§cTeleporting outside the base. You will have to wait 60 seconds.");
						$sender->sendMessage("§r§7((MOVING WILL CANCEL TELEPORTATION))");
						$sender->setTeleport($h);
						$sender->setTeleporting(true);
						$currentpos = new Vector3((int) $sender->getX(), (int) $sender->getY(), (int) $sender->getZ());
						$sender->setTeleportTask(new TeleportTask(Sage::getInstance(), $sender, "§r§6§l* §r§7Teleport has Commenced.", 60, $pos, $currentpos));
					} else $sender->sendMessage("" . "§r§7((TO LIMIT ON TP QUEUES YOU CAN ONLY FSTUCK ON CLAIMED LAND))");
					break;
				case "sethome":
					if ($sender->isInFaction()) {
						if (Sage::getFactionsManager()->getLeader($sender->getFaction()) == $sender->getName()) {
							if (!$sender->hasPvpTimer()) {
								if ($sender->isInClaim()) {
									if (Sage::getFactionsManager()->getClaimer($sender->x, $sender->z) == $sender->getFaction()) {
										Sage::getFactionsManager()->setHome($sender->getFaction(), $sender->getPosition());
										$sender->sendMessage(TextFormat::GREEN . "§aHome set.");
									} else {
										$sender->sendMessage(TextFormat::RED . "You can only set your faction home in your territory");
									}
								} else {
									$sender->sendMessage(TextFormat::RED . "You can only set your faction home in your territory");
								}
							} else $sender->sendMessage(TextFormat::RED . "§r§7Please disable §6§lPvP Timer.");
						} else $sender->sendMessage(TextFormat::RED . "§r§cYou are not the faction Captian.");
					} else $sender->sendMessage(TextFormat::RED . "§r§cYou are not in a team.");
					break;

				case "bal":
					if (!$sender->isInFaction()) {
						$sender->sendMessage(TextFormat::RED . "You must be in a team to use this command.");
						return;
					}
					$sender->sendMessage(TextFormat::GREEN . "§r§6§l" . TextFormat::YELLOW . Sage::getFactionsManager()->getBalance($sender->getFaction()) . "§r§e$");
					break;

				case "deposit":
				case "d":
					if ($sender->isInFaction()) {
						if (isset($args[1])) {
							if (strtolower($args[1]) == 'all') {
								if ($sender->getBalance() > 0) {

									Sage::getFactionsManager()->addBalance($sender->getFaction(), $sender->getBalance());
									foreach (Sage::getFactionsManager()->getOnlineMembers($sender->getFaction()) as $member) {
										$member->sendMessage("§r§e" . $sender->getName() . " has deposited§r§6§l " . $sender->getBalance() . "$" . " §r§ein the faction bank");
									}
									Sage::getSQLProvider()->reduceBalance($sender->getName(),$sender->getBalance());
								} else {
									$sender->sendMessage(TextFormat::RED . "§r§eYou do not have enough money.");
								}
								return;
							}
							if (is_numeric($args[1])) {
								if ($args[1] > 0) {
									if ($sender->getBalance() >= $args[1]) {
										Sage::getSQLProvider()->reduceBalance($sender->getName(),$args[1]);
										Sage::getFactionsManager()->addBalance($sender->getFaction(), $args[1]);
										foreach (Sage::getFactionsManager()->getOnlineMembers($sender->getFaction()) as $member) {
											$member->sendMessage("§r§e" . $sender->getName() . " has deposited§r§6§l" . $args[1]. "$" . " §r§ein the faction bank");
											Sage::getSQLProvider()->reduceBalance($sender->getName(),$args[1]);
										}
									} else $sender->sendMessage(TextFormat::RED . "§r§eYou don't have enough money.");
								} else $sender->sendMessage(TextFormat::RED . "§r§eYou must deposit a positive value.");
							} else $sender->sendMessage(TextFormat::RED . "Invalid number!");
						} else $sender->sendMessage(TextFormat::RED . "§r§cThe correct usage is /f d or /f deposit all");
					} else $sender->sendMessage(TextFormat::RED . "You are not in a team.");
					break;

				case "map":
					Sage::getFactionsManager()->showMap($sender);
					$sender->sendMessage("§6§lSHOWING FACTION CLAIMS NEAR YOU");
					$sender->sendMessage("§r§7((This shows the outline of all faction claims near you to stop showing claims relog.))");
					break;

				case "unfocus":
					if($sender->isFocusing()){
						$sender->setFocusing("null");
					}
					$focusing = $sender->getFocusedFaction();

					$sender->sendMessage("§r§7You have succesfully unfocused that faction");
					break;

				case "focus":
					$sender->sendMessage("§r§c/f focus <faction>");
					if(isset($args[1])) {
						if (Sage::getFactionsManager()->isFaction($args[1])) {
							if ($sender->getFaction() != $args[1]) {
								$sender->setFocusing($args[1]);

								$sender->sendMessage("§r§7You are now focusing §r§6§l" . strtoupper($args[1]));
							} else {
								$sender->sendMessage("You cannot focus yourself!");
							}
						}else{
							$sender->sendMessage("§r§6Please enter a valid faction to focus");
						}
					}
					break;

				case "manage":
					$manager = new FactionsPermissionManager();
					$manager->sendFactionsManagementMenu($sender);
					break;

				case "info":
					if (isset($args[1])) {
						if (Sage::getFactionsManager()->isFaction($args[1])) {
							$fac = $args[1];
							$count = count(Sage::getFactionsManager()->getMembers($fac));
							$online = Sage::getFactionsManager()->getOnlineMembers($fac);
							$onlines = "";
							if (count($online) > 0) {
								foreach ($online as $player) {
									$onlines .= $player->getName() . ", ";
								}
							} else {
								$onlines = "none";
							}
							$ms = Sage::getFactionsManager()->getMembers($fac);
							$members = "";
							if(count($ms) > 0){
								foreach($ms as $member){
									$kills = Sage::getSQLProvider()->getKills($member);
									$members .= "§r§7" . $member . "§r[§c{$kills}§r]" . ", ";
								}
							} else {
								$members = "none";
							}

							$os = Sage::getFactionsManager()->getOfficers($fac);
							$officers = "";
							if(count($os) > 0){
								foreach($os as $officer){
									$officers .= TextFormat::GRAY . $officer . TextFormat::RESET . TextFormat::GRAY . ", ";
								}
							} else {
								$officers = "none";
							}
							$dtr = Sage::getFactionsManager()->getDTR($fac);
							$coords = "not set";
							if (Sage::getFactionsManager()->isHome($fac)) {
								$home = Sage::getFactionsManager()->getHome($fac);
								$coords = " : " . $home->getX() . " : " . $home->getY() . " : " . $home->getZ();
							}

							$max = Sage::getFactionsManager()->getMaxDTR($fac);
							$sender->sendMessage("§r§9" . $fac . " §r§7[§r§f" . $count . "/ 10§r§7]" . " §r§7[§r§f{$coords}§r§7]");
							$sender->sendMessage("§r§aLeader §r§7- " . "§r§a***" . Sage::getFactionsManager()->getLeader($fac));
							$sender->sendMessage("§r§aOfficers §r§7- " . "§r§a". $officers);
							$sender->sendMessage("§r§aOnline §r§7- §a" . $onlines . "");
							$sender->sendMessage("§r§aMembers §r§7- §a" . $members . "");;
							$sender->sendMessage("§r§aBalance §r§7- §r§f$" . Sage::getFactionsManager()->getBalance($fac));
							$sender->sendMessage("§r§aDTR §r§7- §r§c" . Sage::getFactionsManager()->getDTR($fac) . " §r§8(§r§7" . round($dtr). " DTR§r§8)");
							$sender->sendMessage("§r§aFaction Crystals §r§7- §r§f". Sage::getFactionsManager()->getCrystals($fac));
							$sender->sendMessage("§r§aFaction Points §r§7- §r§f". Sage::getFactionsManager()->getPower($fac));
							$sender->sendMessage("§r§aKOTH Wins §r§7- §r§f". Sage::getFactionsManager()->getKothWins($fac));


						} else $sender->sendMessage(TextFormat::RED . "§r§cNo team or member with the name ". $args[1] . " was found in the database");


					} else {


						if ($sender->isInFaction()) {


							$fac = $sender->getFaction();


							$count = count(Sage::getFactionsManager()->getMembers($fac));


							$online = Sage::getFactionsManager()->getOnlineMembers($fac);


							$onlines = "";


							if (count($online) > 0) {


								foreach ($online as $player) {


									$onlines .= $player->getName() . ", ";


								}


							} else {


								$onlines = "none";


							}


							$dtr = Sage::getFactionsManager()->getDTR($fac);


							$coords = "not set";


							$checkfrozen = false;


							$frozentime = 0;


							if (Sage::getFactionsManager()->isFrozen($fac)) {


								$checkfrozen = true;


								$frozentime = Sage::getFactionsManager()->getFrozenTimeLeft($fac);


							}


							if (Sage::getFactionsManager()->isHome($fac)) {


								$home = Sage::getFactionsManager()->getHome($fac);


								$coords = $home->getX() . ":" . $home->getY() . ":" . $home->getZ();


							}
							$ms = Sage::getFactionsManager()->getMembers($fac);
							$members = "";
							if(count($ms) > 0){
								foreach($ms as $member){
									$kills = Sage::getSQLProvider()->getKills($member);
									$members .= "§r§7" . $member . "§e[§c{$kills}§e]" . ", ";
								}
							} else {
								$members = "none";
							}

							$os = Sage::getFactionsManager()->getOfficers($fac);
							$officers = "";
							if(count($os) > 0){
								foreach($os as $officer){
									$officers .= TextFormat::GRAY . $officer . TextFormat::RESET . TextFormat::GRAY . ", ";
								}
							} else {
								$officers = "none";
							}
							$dtr = Sage::getFactionsManager()->getDTR($fac);
							$coords = "not set";
							if (Sage::getFactionsManager()->isHome($fac)) {
								$home = Sage::getFactionsManager()->getHome($fac);
								$coords = " : " . $home->getX() . " : " . $home->getY() . " : " . $home->getZ();
							}

							$max = Sage::getFactionsManager()->getMaxDTR($fac);
							$sender->sendMessage("§r§9" . $fac . " §r§7[§r§f" . $count . "/ 10§r§7]" . " §r§7[§r§f{$coords}§r§7]");
							$sender->sendMessage("§r§aLeader §r§7- " . "§r§a***" . Sage::getFactionsManager()->getLeader($fac));
							$sender->sendMessage("§r§aOfficers §r§7- " . "§r§a**". $officers);
							$sender->sendMessage("§r§aOnline §r§7- §a" . $onlines . "");
							$sender->sendMessage("§r§aMembers §r§7- §a" . $members . "");;
							$sender->sendMessage("§r§aBalance §r§7- §r§f$" . Sage::getFactionsManager()->getBalance($fac));
							$sender->sendMessage("§r§aDTR §r§7- §r§c" . Sage::getFactionsManager()->getDTR($fac) . " §r§8(§r§7" . round($dtr). " DTR§r§8)");
							if ($checkfrozen) {
								$sender->sendMessage("§r§aDTR Freeze §r§7- §r§f" . TextFormat::GRAY . Sage::intToString($frozentime));
							}
							$sender->sendMessage("§r§aFaction Crystals §r§7- §r§f". Sage::getFactionsManager()->getCrystals($fac));
							$sender->sendMessage("§r§aFaction Points §r§7- §r§f". Sage::getFactionsManager()->getPower($fac));
							$sender->sendMessage("§r§aKOTH Wins §r§7- §r§f". "0");




						} else $sender->sendMessage("§r§7/f info <faction>");


					}


					break;
				case "chat":
				case "c":
					if ($sender->isInFaction()) {
						if ($sender->getChat() == SagePlayer::PUBLIC) {
							$sender->setChat(SagePlayer::FACTION);
							$sender->sendMessage(TextFormat::GREEN . "§r§eYou are now in team chat!");
						} else {
							$sender->setChat(SagePlayer::PUBLIC);
							$sender->sendMessage(TextFormat::GREEN . "§r§eYou are now in public chat!");
						}
					} else $sender->sendMessage(TextFormat::RED . "You are not in a team!");
					break;

				case "withdraw":
					if ($sender->isInFaction()) {
						if (Sage::getFactionsManager()->getLeader($sender->getFaction()) == $sender->getName() || Sage::getFactionsManager()->isOfficer($sender->getFaction(), $sender->getName())){
							if (isset($args[1])) {
								if (is_numeric($args[1])) {
									if (Sage::getFactionsManager()->getBalance($sender->getFaction()) >= $args[1] && $args[1] > 0) {
										Sage::getSQLProvider()->addMoney($sender->getName(), $args[1]);
										Sage::getFactionsManager()->reduceBalance($sender->getFaction(), $args[1]);
										foreach (Sage::getFactionsManager()->getOnlineMembers($sender->getFaction()) as $member) {
											$member->sendMessage("§r§e" . $sender->getName() . " has withdrew §r§6§l" . $args[1] . "$" . " §r§efrom the faction bank");
										}
									} else $sender->sendMessage(TextFormat::RED . "§r§eThe team dosen't have enough money to do this!");
								} else $sender->sendMessage("§r§c§l" . $args[1] . "§r§cis not a valid number");
							} else $sender->sendMessage(TextFormat::RED . "Usage: /f withdraw <amount>");
						} else $sender->sendMessage(TextFormat::RED . "§r§eYou are not a §r§6§lofficer.");
					} else $sender->sendMessage(TextFormat::RED . "You are not in a team.");
					break;

				case "kick":
					if (!$sender->isInFaction()) {
						$sender->sendMessage(TextFormat::RED . "You are not in a team");
						return;
					}
					if (!Sage::getFactionsManager()->getLeader($sender->getFaction()) === $sender->getName() || !Sage::getFactionsManager()->isOfficer($sender->getFaction(), $sender->getName())){
                        $sender->sendMessage(TextFormat::RED . "You are not the Team Leader.");
						return;
					}
					if(!isset($args[1])){
						$sender->sendMessage("§r§cThe player §c§l'null' §r§cwas not found in the Team or Player Database.");
						return;
					}
					if (!Sage::getFactionsManager()->isMember($sender->getFaction(), $args[1])) {
						$sender->sendMessage("§r§c" . $args[1] . " §r§cisn't in your team");
						return;
					}
					Sage::getFactionsManager()->kick($args[1]);
					$sender->sendMessage(TextFormat::GREEN . "§r§eYou have kicked §r§6§l" . $args[1] . " §r§efrom your faction!");
					break;

				case "invite":
					if ($sender->isInFaction()) {
						if (Sage::getFactionsManager()->getLeader($sender->getFaction()) == $sender->getName() || Sage::getFactionsManager()->isOfficer($sender->getFaction(), $sender->getName())){
							if (isset($args[1])) {
								if ($this->getSage()->getServer()->getPlayer($args[1]) != null) {
									$player = $this->getSage()->getServer()->getPlayer($args[1]);
									if ($player instanceof SagePlayer) {
										if (count(Sage::getFactionsManager()->getMembers($sender->getFaction())) < 10) {
											if (!$player->isInFaction()) {
												if (!$player->isInvited()) {
													$player->setLastinvite($sender->getFaction());
													$player->setInvited(true);
													$player->setTask(new InviteTask($this->getSage(), $player));

													$player->sendMessage("§6§l{$sender->getFaction()} §r§ehas inivted you to join them");
													$player->sendMessage("§r§7((/F JOIN))");
												} else $sender->sendMessage(TextFormat::RED . "§r§eThe specified player already has a §r§cpending §r§einvite");
											} else $sender->sendMessage(TextFormat::RED . "§r§6§l".$player->getName() . " §r§eis in a faction!");
										} else $sender->sendMessage(TextFormat::RED . "§r§cFaction is full.");
									} else $sender->sendMessage("§r§c§l" . strtolower($args[1]) . "was not found in the Player Database.");
								} else $sender->sendMessage(TextFormat::RED . "§r§6§l{$args[1]} §r§cwas not found in the Player Database.");
							} else $sender->sendMessage(TextFormat::RED . "§r§eUsage: /f invite <player>");
						} else $sender->sendMessage(TextFormat::RED . "§r§cYou must be a higher role to invite players");
					} else $sender->sendMessage(TextFormat::RED . "§r§7You are not in a team.");
					break;

				case "promote":
					if($sender->isInFaction()) {
						$faction = $sender->getFaction();
						if (Sage::getFactionsManager()->getLeader($faction) == $sender->getName()) {
							if (isset($args[1])) {
								$player = $this->plugin->getServer()->getPlayer($args[1]);
								if ($player != null) $name = $player->getName();
								if ($player instanceof SagePlayer) {
									if ($player->getName() != $sender->getName()) {
										if (Sage::getFactionsManager()->isMember($faction, $name)) {
											Sage::getFactionsManager()->setOfficer($faction, $name);
											$sender->sendMessage("§r§eYou have promoted §r§6§l{$name}");
											$player->sendMessage("§r§eYou have been promoted to a §r§6§lOfficer.");
										} else {
											$sender->sendMessage("§r§6§l{$player} §r§emay not be promoted");
										}
									} else {
										$sender->sendMessage("§r§6§l{$player} §r§eis not online.");
									}
								} else {
									#$sender->sendMessage("§r§c/f promote <player name>");
								}
							} else {
								$sender->sendMessage("§r§eYou are not the Leader!");
							}
						} else {
							$sender->sendMessage("§r§7You are not in a team.");
						}
					}
					break;

				case "join":
					if ($sender->isInvited()) {
						$fac = $sender->getLastinvite();
						$sender->getTask()->cancel();
						if (count(Sage::getFactionsManager()->getMembers($fac)) < 10) {
							$sender->addToFaction($fac);
							$sender->setInvited(false);
							foreach (Sage::getFactionsManager()->getOnlineMembers($fac) as $member) {
								$member->sendMessage("§r§e" . $sender->getName() . " §r§6has joined the faction");
							}
						} else $sender->sendMessage(TextFormat::RED . "§r§cThe faction is full.");
					} else $sender->sendMessage(TextFormat::RED . "§r§eYou have no §r§6§lpending §r§einvites.");
					break;

				case "leader":
				case "setleader":
					if ($sender->isInFaction()) {
						if (Sage::getFactionsManager()->getLeader($sender->getFaction()) == $sender->getName()) {
							if (isset($args[1])) {
								$player = $this->getSage()->getServer()->getPlayer($args[1]);
								if ($player and $player instanceof SagePlayer) {
									if (Sage::getFactionsManager()->isMember($sender->getFaction(), $player->getName())) {
										$player->addToFaction($sender->getFaction(), FactionsManager::LEADER);
										$sender->addToFaction($sender->getFaction(), FactionsManager::OFFICER);
										foreach (Sage::getFactionsManager()->getOnlineMembers($sender->getFaction()) as $member) {
											$member->sendMessage("§r§e" .$sender->getName() . " has been given §r§6§lownership §r§e of {$sender->getFaction()} §r§eto §r§6§l{$player->getName()} ");
										}
										$sender->sendMessage("§r§e" .$player->getName() . " has been given §r§6§lownership §r§e of {$sender->getFaction()}");
									} else $sender->sendMessage("§r§c" . $args[1] . "§r§cIs not in your team.");
								} else $sender->sendMessage(TextFormat::RED . "§r§6§l{$args[1]} §r§eis currently §6§loffline");
							} else $sender->sendMessage(TextFormat::RED . "Usage: /f leader <player>");
						} else $sender->sendMessage(TextFormat::RED . "§r§eYou are not the faction Leader.");
					} else $sender->sendMessage(TextFormat::RED . "§r§cYou are not in a team");
					break;

				case "deny":
					if ($sender->isInvited()) {
						$fac = $sender->getLastinvite();
						$sender->getTask()->cancel();
						$sender->setInvited(false);
						$sender->sendMessage(TextFormat::GREEN . "§r§eYou have successfully denied the invitation!");
						foreach (Sage::getFactionsManager()->getOnlineMembers($fac) as $member) {
							$member->sendMessage("§r§e" . $sender->getName() . "§r§6declined the faction invite.");
						}
					} else $sender->sendMessage(TextFormat::RED . "§r§eYou have no §r§6§lpending §r§einvites.");
					break;

			}
		} else {

			Sage::getFactionsManager()->getDb()->exec("SELECT * FROM claims");
		}
	}

	/**
	 * @return array
	 */
	public function getHelp(): array
	{
		$help = [
			"§6§lFaction Help §r§7(Page 1/1)",
			"§r§e§l* §r§f/f manage §r§e- §r§eOpens a GUI to manage the players in your faction.",
			"§r§e§l* §r§f/f list §r§e- §r§eSee a list of all factions.",
			"§r§e§l* §r§f/f top §r§e- §r§eView the factions with largest number of points.",
			"§r§e§l* §r§f/f create §r§e- §r§eCreate a faction.",
			"§r§e§l* §r§f/f disband §r§e- §r§eDisband your faction.",
			"§r§e§l* §r§f/f invite §r§e- §r§eInvite a player to your faction.",
			"§r§e§l* §r§f/f kick §r§e- §r§eKick a player from your faction.",
			"§r§e§l* §r§f/f join §r§e- §r§eAccept a invite to join a faction.",
			"§r§e§l* §r§f/f leave §r§e- §r§eLeave your current faction.",
			"§r§e§l* §r§f/f claim §r§e- §r§eClaim land in the wildnerness for your faction.",
			"§r§e§l* §r§f/f unclaim §r§e- §r§eUnclaim your factions territory.",
			"§r§e§l* §r§f/f stuck §r§e- §r§eTeleport to a safe position.",
			"§r§e§l* §r§f/f home §r§e- §r§eTeleport to your factions HQ.",
			"§r§e§l* §r§f/f chat §r§e - §r§eToggle faction chat mode on or off.",
			"§r§e§l* §r§f/f officer §r§e- §r§ePromote a player.",
			"§r§e§l* §r§f/f withdraw §r§e- §r§eWithdraw money from your factions bank.",
			"§r§e§l* §r§f/f deposit §r§e- §r§eDeposit money into your factions bank.",
			"§r§e§l* §r§f/f crystals §r§e- §r§eCheck your factions Crystals.",
			"§r§e§l* §r§f/f tl §r§e- §r§eTell your factions members your current location.",
		];
		return $help;
	}


	public function checkDtr(string $fac){
		$dtr = Sage::getFactionsManager()->getDTR($fac);
		if($dtr >= 5){
			$dtr .= "§r§a5 ■";
		}
		if($dtr <= 4 && $dtr != 5 or 3 or 2 or 1){
			$dtr.= "§r§e4 ■";
		}
		if($dtr <= 3 && $dtr != 4 or 2 or 1){
			$dtr.= "§r§c3 ■";
		}

		if($dtr <= 2 && $dtr != 3 or 1){
			$dtr.= "§r§d2 ■";
		}
		if($dtr <= 1){
			$dtr.= "§r§41 ■";
		}

	}



	/**
	 * @return Sage
	 */
	public function getSage() : Sage {
		return $this->plugin;
	}
}