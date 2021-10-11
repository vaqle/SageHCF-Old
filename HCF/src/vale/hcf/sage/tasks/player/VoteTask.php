<?php

declare(strict_types = 1);

namespace vale\hcf\sage\tasks\player;

use pocketmine\item\Item;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use vale\hcf\sage\partneritems\PItemManager;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\SagePlayer;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;
use pocketmine\utils\TextFormat;
use vale\hcf\sage\system\monthlycrates\util\CrateUtils;

class VoteTask extends AsyncTask
{

	const API_KEY = "kj6G4tftiWEWUh2vwWX1CUKIyNrRVb3Ulnu";

	const STATS_URL = "https://minecraftpocket-servers.com/api/?object=servers&element=detail&key=" . self::API_KEY;

	const CHECK_URL = "http://minecraftpocket-servers.com/api-vrc/?object=votes&element=claim&key=" . self:: API_KEY . "&username={USERNAME}";

	const POST_URL = "http://minecraftpocket-servers.com/api-vrc/?action=post&object=votes&element=claim&key=" . self:: API_KEY . "&username={USERNAME}";

	const VOTED = "voted";

	const CLAIMED = "claimed";

	/** @var string[] */
	private $player;

	/**
	 * CheckVoteTask constructor.
	 *
	 * @param string $player
	 */
	public function __construct(string $player)
	{
		$this->player = $player;
	}

	public function onRun()
	{
		$result = [];
		$get = Internet::getURL(str_replace("{USERNAME}", $this->player, self::CHECK_URL));
		if ($get === false) {
			return;
		}
		$get = json_decode($get, true);
		if ((!isset($get["voted"])) or (!isset($get["claimed"]))) {
			return;
		}
		$result[self::VOTED] = $get["voted"];
		$result[self::CLAIMED] = $get["claimed"];
		if ($get["voted"] === true and $get["claimed"] === false) {
			$post = Internet::postURL(str_replace("{USERNAME}", $this->player, self::POST_URL), []);
			if ($post === false) {
				$result = null;
			}
		}
		$this->setResult($result);
	}

	/**
	 * @param Server $server
	 *
	 */
	public function onCompletion(Server $server)
	{
		$player = $server->getPlayer($this->player);
		if ((!$player instanceof SagePlayer) or $player->isClosed()) {
			return;
		}
		$result = $this->getResult();
		if (empty($result)) {
			$player->sendMessage("§r§7((§r§6It appears we had a error on our side Report it to STAFF§r§7))");
			return;
		}
		$player->setCheckingForVote(false);
		if ($result[self::VOTED] === true) {
			if ($result[self::CLAIMED] === true) {
				$player->setVoted();
				$player->sendMessage("§6§lINFORMATION");
				$player->sendMessage("§r§7((Our Database Detects that you have already voted you can vote every 12 hours))");
				return;
			}
			$player->setVoted();
			DataProvider::addVotes($player->getName(), 1);
			$currentvotes = DataProvider::getVoteCount($player->getName());
			$key = Item::get(Item::DYE, 9, 3);
			$key->setCustomName("§r§d§lHaze §r§7Key (Right-Click)");
			$key->setLore([
				"§r§7Right-Click this key on the §l§dHaze",
				"§r§l§dCrate §r§7located at §dspawn §7to obtain rewards.",
				"§r",
				"§r§dstore.buycraft.net"
			]);
			$key->setNamedTagEntry(new ListTag("ench"));
			$lol = Item::get(Item::DISPENSER, 0 ,3);
			$lol->setCustomName("§r§l§f*§b*§f*§r§bAir Drop§r§l§f*§b*§f*");
			$lol->setLore([
				'§r§7a unique cache of equipment that can be won from crates',
				'§r§7or bought from our buycraft',
				'§r§7((This can only be placed in your factions territory or wilderness)',
				'',
				'§r§7Winning §r§b§lRanks §r§7is possible from an §r§b§lAirDrop.',
				'',
				'§r§b§o§lshop.hcf.net',
			]);
			$lol->getNamedTag()->setTag(new StringTag("REWARD_AIRDROP"));
			$player->getInventory()->addItem($key);
			$player->getInventory()->addItem($lol);
			PItemManager::givePartnerPackage($player, 1);
			$active = DataProvider::getFundData()->get("doublevote");
			if ($active === "true") {
				$player->sendMessage("§6§lDOUBLE VOTE REWARDS");
				$player->sendMessage("§l§6* §r§7Since §r§ddouble vote rewards §r§7are active you recieved x2 the rewards! ➰");
				$player->sendMessage("\n");
				$player->sendMessage("§l§6* §r§7Tell your friends about it! ➰");
				PItemManager::giveLootBox($player, "Summer",1);
				PItemManager::giveKeys($player, "Sage",4);
				PItemManager::giveKeys($player, "Summer",1);
				PItemManager::giveKeys($player, "Haze",4);
				PItemManager::givePartnerPackage($player,4);
				CrateUtils::giveMonthlyCrate($player, "june2021", (int)1);
			}
			$server->broadcastMessage("§l§6(!) §r§e{$player->getName()} §7has voted with §e/vote §7and received the following rewards:\n§l§6* §r§7x3 §l§dHaze Keys\n§l§6* §r§7x1-3 §l§bAir Drops\n§l§6* §r§7x2 §l§aVote Keys \n§l§6* §r§7x2 §l§cAbility Keys ➰");
			$server->broadcastMessage("\n");
			$server->broadcastMessage("§e§lVOTE LINK: §r§7bit.ly/sagevote");
			$player->sendMessage("§6§lCURRENT VOTES: §r§7{$currentvotes}");
			$server->broadcastMessage("§l§6* §r§7After entering your username and completing the captcha come back to the server and run the command §6§l/vote! ➰");
			return;
		}
		$player->sendMessage("§6§lINFORMATION");
		$player->sendMessage("§r§7((Our Database Detects that you havent you can find our vote link with the command /links))");
		$player->setVoted(false);
	}
}