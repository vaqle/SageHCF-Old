<?php

namespace vale\hcf\sage\handlers;

use vale\hcf\sage\handlers\events\PlayerLogHandler;
use vale\hcf\sage\{handlers\events\RegionListener,
	handlers\events\StaffHandler,
	SagePlayer,
	Sage,
	SageListener,
	system\classes\ArcherClass,
	system\classes\BardClass,
	system\deathban\DeathbanListener,
	system\events\KillTheKingEvent,
	system\shop\ShopListener};
use vale\hcf\sage\partneritems\PItemListener;
use vale\hcf\sage\factions\FactionsListener;
use vale\hcf\sage\crates\CrateListener;
use vale\hcf\sage\handlers\events\AntiGlitchHandler;
use vale\hcf\sage\handlers\events\PlayerListener;
use vale\hcf\sage\handlers\events\ItemsHandler;
use vale\hcf\sage\handlers\events\EnderPearlHandler;
use vale\hcf\sage\handlers\events\SotwHandler;

class EventRegistery{

   public static function init(): void {
      $instance = Sage::getInstance();
      $server = $instance->getServer();
      $mgr = $server->getPluginManager();
      $mgr->registerEvents(new ShopListener($instance), $instance);
      $mgr->registerEvents(new SageListener($instance), $instance);
      $mgr->registerEvents(new FactionsListener($instance), $instance);
      $mgr->registerEvents(new AntiGlitchHandler($instance), $instance);
      $mgr->registerEvents(new PItemListener($instance), $instance);
      $mgr->registerEvents(new CrateListener($instance), $instance);
      $mgr->registerEvents(new PlayerListener($instance), $instance);
      $mgr->registerEvents(new ItemsHandler($instance), $instance);
      $mgr->registerEvents(new SotwHandler($instance), $instance);
      $mgr->registerEvents(new RegionListener($instance), $instance);
      $mgr->registerEvents(new PlayerLogHandler($instance), $instance);
      $mgr->registerEvents(new BardClass($instance), $instance);
      $mgr->registerEvents(new ArcherClass($instance), $instance);
      $mgr->registerEvents(new StaffHandler($instance), $instance);
      $mgr->registerEvents(new EnderPearlHandler($instance), $instance);
      $mgr->registerEvents(new DeathbanListener($instance), $instance);
      $mgr->registerEvents(new KillTheKingEvent($instance), $instance);
      $server->getLogger()->info("EVENTS LOADED");
     }
}