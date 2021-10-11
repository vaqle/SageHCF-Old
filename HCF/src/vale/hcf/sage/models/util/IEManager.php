<?php

declare(strict_types = 1);

namespace vale\hcf\sage\models\util;


use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\utils\TextFormat;
use vale\hcf\sage\models\util\SkinConverter;
use vale\hcf\sage\Sage;

class IEManager {


	/** @var Skin */
	public $skin;

	/** @var string */
	public $name;

	/** @var Sage*/
	private $plugin;

	/**
	 * Manager constructor.
	 *
	 * @param Sage $plugin
	 * @param string $path
	 */
	public function __construct(Sage $plugin, string $path) {
		$this->plugin = $plugin;
		$this->path = $path;
		$this->init();
	}

	public function init(): void {
		$path = $this->plugin->getDataFolder() . $this->path;
		$this->skin = SkinConverter::createSkin(SkinConverter::getSkinDataFromPNG($path));

	}
}
