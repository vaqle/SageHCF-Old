<?php
namespace vale\hcf\sage\tasks\player;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use vale\hcf\sage\models\util\UtilManager;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\kits\KitManager;

class AnnoucementsTask extends Task
{
	public $plugin;

	public $lastMessage = [];

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	public function onRun(int $currenttick)
	{
		$online = count(Server::getInstance()->getOnlinePlayers());
		$rankedSage = UtilManager::getSageRankedPlayers();
        	KitManager::processCooldown();
		$msgs = array(
			"§r§7Found §r§d§l{$online} / §r§5§l160 §r§7players online.",
			"§r§c§lALERT §r§7((§r§cHacking will not be tolerated any use of §r§cmodifications or unfair advantages will result in a §r§4§lPERM BAN§r§7 ))",
			"§r§7Follow us on Twitter to recieve Updates when we Post them or Join Giveaways §b§l@SageNetwork",
		        "§r§8§l[§6BETA§8] §r§7It is a BETA-Map so expect §cBugs. §r§7If you encounter any make sure you report them.",
		        "§r§8§l[§r§6§l2.0§r§8§l] §r§eEverything on §r§cstore.hcf.net §r§eis §c§l65 percent §r§eoff!",
		        "§6Faction Crystals? §r§7Run to spawn to exchange them for §dPartner Items!",
                        "§r§7[§6§lTIP§r§7] §r§eInterested in purchasing Partner Packages, Keys, \n §r§eand more? Head over to bit.ly/sagebuycraft.",
		        "§8§l[§6§l2.0§8] §r§eFound a §ccheater§r§e? §r§eCreate a ticket and report them in our discord with proof!",
		        "§r§8§l[§r§6§l2.0§r§8§l] §r§eIn a §r§asticky §r§esituation? Use §c§l/tl §r§eto alert your faction members \n §r§eor allies of your whereabouts!",
		        "§8§l[§62.0§8] §r§7Interested in exclusive §aPerks? §r§7Such as Tags? or Kits? §r§7Head over to our buycraft!",
			"§8§l[§62.0§8] §r§eIf you are caught glitching or hacking you will be §c§lpunished!",
			"§r§b§lNEW OLYMPUS PERKS + PRICE! \n§r§7Check out our new perks\n§r§7and price for §r§fOlympus Rank§r§7!\n§r§bShop here: §r§fbit.ly/sagebuycraft",
			"§r§b§lONIX CLIENT\n §r§7Hello, fellow gamer we have noticed you are not using the official Onix Client to play on Sage.\n§r§7Download and install it today to get the best experience\n §r§b§l* §r§7FPS BOOST+ \n§r§b§lREAD MORE: §r§fdiscord.gg/yRnQFU7SpY",
			"§r§5~ Online Supporters ~ \n §r§7Want your name to appear? Donate and recieve exclusive rewards! \n §r§d" . $rankedSage
	);
		$rm = array_rand($msgs);
		if (count(Server::getInstance()->getOnlinePlayers()) > 0) {
			Server::getInstance()->broadcastMessage($msgs[$rm]);
            
		}
	}
}
