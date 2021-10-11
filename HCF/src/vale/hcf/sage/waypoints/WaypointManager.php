<?php

namespace vale\hcf\sage\waypoints;
use vale\hcf\sage\waypoints\tasks\WayPointUpdateTask;
use vale\hcf\sage\Sage;
class WaypointManager{

public $plugin;

public function __construct(Sage $plugin){
    $this->plugin = $plugin;
    $this->plugin->getScheduler()->scheduleRepeatingTask(new WayPointUpdateTask(), 10);
 }

}