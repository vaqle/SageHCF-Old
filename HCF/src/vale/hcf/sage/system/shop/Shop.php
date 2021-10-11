<?php
declare(strict_types=1);
namespace vale\hcf\sage\system\shop;

use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;
use pocketmine\item\Item;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat as C;

class Shop {

	const BUY = 0;
	const SELL = 1;
	private $plugin;
	private $shop;

	/**
	 * @param Sage $plugin
	 */
	public function __construct(Sage $plugin){
		$this->plugin = $plugin;
		$this->shop = new Config(Sage::getInstance()->getDataFolder() . "Shops.json", Config::JSON);
	}

	/**
	 * int $type
	 * int $id
	 * int $damage
	 * int $amount
	 * int $price
	 * Vector3 $pos
	 * string $name
	 */
	public function createShop(int $type, int $id, int $damage, int $amount, int $price, Vector3 $pos, string $name = null){
		$data = [
			"id" => $id,
			"damage" => $damage,
			"amount" => $amount,
			"price" => $price,
			"name" => $name,
			"type" => $type
		];
		$tile = $this->plugin->getServer()->getDefaultLevel()->getTile($pos);
		$loc = $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
		if($type == self::BUY){
			$text1 = "§r§6§l* §r§eBUY";
		} else {
			$text1 = "§r§e§l* §r§6SELL";
		}
		if($name == null){
			$text2 = Item::get($id, $damage)->getName();
		} else {
			$text2 = $name;
		}
		$text3 = "§r§fQuanity: " . $amount;
		$text4 =  "§a§l$";
		$tile->setText($text1, $text2, $text3, $text4);
		if($tile instanceof Sign){
			$tile->setText($text1, $text2, $text3, $text4);
		}
		$this->shop->set($loc, $data);
		$this->shop->save();
	}

	/**
	 * @param Vector3 $pos
	 */
	public function moveShop(Vector3 $pos){
		$loc = $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
		$this->shop->remove($loc);
		$this->shop->save();
	}

	/**
	 * @param Vector3 $pos
	 * @return bool
	 */
	public function isShop(Vector3 $pos): bool{
		$loc = $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
		return $this->shop->exists($loc);
	}

	/**
	 * @param Vector3 $pos
	 * @return int
	 */
	public function getId(Vector3 $pos): int{
		$loc = $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
		return $this->shop->get($loc)["id"];
	}

	/**
	 * @param Vector3 $pos
	 * @return int
	 */
	public function getDamage(Vector3 $pos): int{
		$loc = $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
		return $this->shop->get($loc)["damage"];
	}

	/**
	 * @param Vector3 $pos
	 * @return int
	 */
	public function getAmount(Vector3 $pos): int{
		$loc = $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
		return $this->shop->get($loc)["amount"];
	}

	/**
	 * @param Vector3 $pos
	 * @return int
	 */
	public function getPrice(Vector3 $pos): float{
		$loc = $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
		return $this->shop->get($loc)["price"];
	}

	/**
	 * @param Vector3 $pos
	 * @return string
	 */
	public function getName(Vector3 $pos): string{
		$loc = $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
		return $this->shop->get($loc)["name"];
	}

	/**
	 * @param Vector3 $pos
	 * @return int
	 */
	public function getType(Vector3 $pos): int{
		$loc = $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
		return $this->shop->get($loc)["type"];
	}
}