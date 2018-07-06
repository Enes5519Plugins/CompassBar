<?php

declare(strict_types=1);

namespace Enes5519\CompassBar;

use pocketmine\Player;
use pocketmine\scheduler\Task;

class ShowBarTask extends Task{

    /** @var CompassBar */
    private $plugin;

    public function __construct(CompassBar $plugin){
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick){
        $bossBar = $this->plugin->getBossBar();
        /** @var Player $viewer */
        foreach($bossBar->getViewers() as $viewer){
            if(!$viewer->isClosed()){
                $bossBar->setTitle(Utils::getCompass($viewer->yaw));
                $bossBar->updateFor($viewer);
            }else{
                $viewer->removeBossbar(CompassBar::BOSSBAR_ID);
            }
        }
    }

    public function onCancel(){
        $bossBar = $this->plugin->getBossBar();
        /** @var Player $viewer */
        foreach($bossBar->getViewers() as $viewer){
            $viewer->removeBossbar(CompassBar::BOSSBAR_ID);
        }
    }

}