<?php

declare(strict_types=1);

namespace Enes5519\CompassBar;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\TextFormat;

class CompassBar extends PluginBase implements Listener{

	/** @var int */
	public $refreshRate = 4;
	/** @var TaskHandler[] */
	protected $barTasks = [];

	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();

		$this->refreshRate = (int) $this->getConfig()->get("refresh-rate", 4);
		if($this->refreshRate < 1){
			$this->getLogger()->warning("Refresh rate property in config.yml is less than 1. Resetting to 1");
			$this->getConfig()->set("refresh-rate", 1);
			$this->getConfig()->save();
			$this->refreshRate = 1;
		}

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(!$command->testPermission($sender)){
			return true;
		}

		if(!($sender instanceof Player)){
			$sender->sendMessage(TextFormat::RED . "You can use this command in the game.");
			return true;
		}

		if(!isset($this->barTasks[$sender->getName()])){
			$this->barTasks[$sender->getName()] = $sender->getServer()->getScheduler()->scheduleRepeatingTask(new ShowBarTask($this, $sender), $this->refreshRate);
			$sender->sendMessage(TextFormat::GREEN . "CompassBar is now on!");
		}else{
			$this->cancelTask($sender->getName());
			$sender->sendMessage(TextFormat::RED . "CompassBar is now off.");
		}

		return true;
	}

	private function cancelTask(string $p){
		if(isset($this->barTasks[$p])){
			$this->getServer()->getScheduler()->cancelTask($this->barTasks[$p]->getTaskId());
			unset($this->barTasks[$p]);
		}
	}

	public function onQuit(PlayerQuitEvent $event){
		$this->cancelTask($event->getPlayer()->getName());
	}
}