<?php

declare(strict_types=1);

namespace Enes5519\CompassBar;

use pocketmine\scheduler\Task;

class ShowBarTask extends Task{
	/** @var CompassBar */
	private $plugin;

	public function __construct(CompassBar $plugin){
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick){
		foreach($this->plugin->enabled as $player){
			if(!$player->isClosed()){
				$this->plugin->sendBossPacket($player, Utils::getCompass($player->yaw));
			}else{
				$this->plugin->removeBossBar($player);
			}
		}
	}

	public function onCancel(){
		foreach($this->plugin->enabled as $player){
			$this->plugin->removeBossBar($player);
		}
	}
}