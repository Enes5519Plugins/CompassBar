<?php

declare(strict_types=1);

namespace Enes5519\CompassBar;

use pocketmine\entity\utils\Bossbar;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class ShowBarTask extends PluginTask{

	/** @var Player */
	private $player;
	/** @var Bossbar */
	private $bossBar;

	public function __construct(CompassBar $owner, Player $player){
		parent::__construct($owner);
		$this->player = $player;
		$this->bossBar = new Bossbar();
		$this->bossBar->setHealthPercent(1.0, 1.0);
		$this->bossBar->showTo($player);
	}

	public function onRun(int $currentTick){
		assert(!$this->player->isClosed());
		$this->bossBar->setTitle(Utils::getCompass($this->player->getYaw()));
		$this->bossBar->updateFor($this->player);
	}

	public function onCancel(){
		$this->bossBar->hideFrom($this->player);
	}

}