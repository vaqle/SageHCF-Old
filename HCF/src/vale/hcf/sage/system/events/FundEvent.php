<?php

namespace vale\hcf\sage\system\events;


use pocketmine\item\Item;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Server;
use vale\hcf\sage\partneritems\PItemManager;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;
use vale\hcf\sage\tasks\player\FundEventStartTask;
use xenialdan\apibossbar\BossBar;
use xenialdan\apibossbar\DiverseBossBar;

class FundEvent
{

	/** @var Sage */
	protected $plugin;

	/** @var bool */
	protected static $enable = false;

	/** @var Int */
	protected static $time = 0;

	/**
	 * FundEvent Constructor.
	 * @param Sage $plugin
	 */
	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * @return bool
	 */
	public static function isEnable(): bool
	{
		return self::$enable;
	}

	/**
	 * @param bool $enable
	 */
	public static function setEnable(bool $enable)
	{
		self::$enable = $enable;
	}

	/**
	 * @param Int $time
	 */
	public static function setTime(int $time)
	{
		self::$time = $time;
	}

	/**
	 * @return Int
	 */
	public static function getTime(): int
	{
		return self::$time;
	}

	public static function rewardAllPlayers(){
		$rand = rand(1,7);
		$summerob = Item::get(Item::ENDER_EYE, 2 , $rand)->
		setCustomName("§r§5§kkeeueueu§r§d§lSummer Orb 2.0§r§5§kkeeueueu")->
		setLore([
			'§r§7Tap this §r§d§lsacred §r§7item at the Orb Extractor',
			'§r§7To recieve rewards such as',
			'§r§d(§r§7Airdrops§r§d, §r§7Keys§r§d, §r§7Portable Kits§r§d',
			'§r§7Ranks§r§d, §r§7and More!§r§d)',
			'',
			'§r§d§lstore.hcf.net'
		]);
		$summerob->getNamedTag()->setTag(new StringTag("summerkeyxd"));
		$lol = Item::get(Item::DISPENSER, 0 ,7);
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
		$summerob->setNamedTagEntry(new ListTag("ench"));
		foreach (Server::getInstance()->getOnlinePlayers() as $player){
			$player->getInventory()->addItem($summerob);
			PItemManager::givePartnerPackage($player, 4);
			$player->getInventory()->addItem($lol);

		}
	}

	/**
	 * @return void
	 */
	public static function start(int $time = 60): void
	{
		self::setEnable(true);
		Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new FundEventStartTask($time), 20);
	}

	public static function fundEventStart(SagePlayer $player)
	{
		$fund = DataProvider::getFundData()->get("Fund");
		$total = DataProvider::getFundData()->get("Total");
		$bar = new BossBar();
		$bar->setTitle("§r§e§l<§6X§e> §6§lWORLD FUND EVENT §r§e§l<§6X§e>");
		$bar->setSubTitle("§6§lTotal §r§e{$fund} §r§7/ {$total} §r§6$");
		$bar->setPercentage($fund);
	}
}
