<?php

namespace vale\hcf\sage\tasks;

use vale\hcf\sage\tasks\player\AnnoucementsTask;
use vale\hcf\sage\tasks\MainThreadTask;
use vale\hcf\sage\Sage;
use vale\hcf\sage\factions\tasks\FactionTask;
use mysqli;
use vale\hcf\sage\tasks\player\BardTask;
use vale\hcf\sage\tasks\player\ClearEntitesTask;
use vale\hcf\sage\tasks\player\CrateParticlesTask;

class TaskRegistery{
    
    public static function init(): void{
      $sage = Sage::getInstance();
      $sage->getScheduler()->scheduleRepeatingTask(new AnnoucementsTask($sage), 2500);
      $sage->getScheduler()->scheduleRepeatingTask(new CrateParticlesTask($sage), 20);
	  $sage->getScheduler()->scheduleRepeatingTask(new MainThreadTask($sage),5*20);
	  $sage->getScheduler()->scheduleRepeatingTask(new BardTask($sage), 20);
	  $sage->getScheduler()->scheduleRepeatingTask(new FactionTask($sage), 2500);
	  $sage->getScheduler()->scheduleRepeatingTask(new ClearEntitesTask($sage), 20 * 120);
	  $sage->getLogger()->info("Enabling TASKS");
    }

}